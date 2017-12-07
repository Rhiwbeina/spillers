<?php include("../auth.php");
session_write_close();  // allows other to continue to be served;
 ?>
<HTML>
<HEAD>
      <TITLE>This page should close re direct and continue running</TITLE>
</HEAD>
<BODY>
	<center>AN</center> 
<div id="headtext">starting</div>
<hr>
	<script>
	setTimeout(function(){ getPageFromForm('switcher.php', '', 'POST'); }, 100);
	</script>
</BODY>
</HTML>
<?php
pushtoclient("------------------------------------");
ini_set('max_execution_time', 7200);  // 2 hours

ignore_user_abort(true); // uncomment to allow script to continue even it browser closes

//include("../dbconnect.php"); // sets $conn as sql connection
echo "<br>";
echo $_SESSION['username'];
print_r($_POST);
$statfile = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], DIRECTORY_SEPARATOR))."/log/status";
if (file_get_contents($statfile.".sts") == "Finished"){
pushtoboth("Starting Scan");	
$settings = getSettings();

echo "<b>Settings</b>";
foreach ($settings as $key => $value) {
	echo "<br>".$key."=".$value;
}

require_once('getID3-1.9.15/getid3/getid3.php');
$getID3 = new getID3;

echo "<hr>";

$filecount = 0;
$dircount = 0;
$id3count = 0;
$deletedcount = 0;

if ($_POST['wipedb'] == 'on'){
	
	echo "<br> Emptying SONGS database ";
	pushtoboth("Emptying SONGS database");
				try {
					$statement = $conn->prepare("TRUNCATE TABLE `songs`");
					$statement->execute();
					pushtoboth(" OK ");
				    }
				catch(PDOException $e)
				    {
				    echo "<br> truncate failed: ". $e->getMessage();
				    pushtoboth("truncate failed");
				    }
}				    

$settings = getSettings();

if ($_POST['scanroot'] == 'on'){
$directories = expandDirectories($settings["musicroot-system"]);
echo "<br> Directory count = ".$dircount;
pushtoboth("Directory count = ".$dircount);
			echo "<br> finding songs in directories ";
				foreach ($directories as $apath) {

					$files = scandir($apath);
						foreach ($files as $afile){
							 $fullfileurl = $apath.DIRECTORY_SEPARATOR.$afile;
							 if($fullfileurl == '.' || $fullfileurl == '..') continue;
								 if(ends_with_filetypes($afile)){								
								 	$fileurlfromroot = substr($fullfileurl, strlen($settings["musicroot-system"]) + 1);
								 	//echo $fileurlfromroot;
								 	makesqlentryforfile($fileurlfromroot);
								 	$filecount = $filecount + 1;
								 		if ($filecount % 30 == 0){ 
								 			pushtoclient("songs found: ".$filecount); 
								 			pushtosts("songs found: ".$filecount); 
								 		}	
								 }				 
						}
				}	
			echo "<br> total songs found = ".$filecount;
			pushtoboth("total songs found = ".$filecount);
//			$dummy = domysql("UPDATE `settings` set value=800 WHERE `setting` = 'scan-state'");
}
// ------------------------------------------------------------------------------------------------------------------------


$settings = getSettings();

if ($_POST['taguntagged'] == 'on'){			// file scan completed OK so carry on
	echo "<br> finding id3 tags";
	pushtoboth("finding id3 tags");
			try {
				$statement = $conn->prepare("select `fullpath` FROM `songs`  WHERE `id3state` = \"unchecked\" ");
				$statement->execute();
				$success = $statement->setFetchMode(PDO::FETCH_ASSOC);
			    $resultsarray = $statement->fetchAll();
			    }
			catch(PDOException $e)
			    {
			    echo "<br> failed: ". $e->getMessage();
			    }

			foreach($resultsarray as $aresult){
				$rootfilepath = $aresult['fullpath'];
				$id3count = $id3count + 1;
				if ($id3count % 30 == 0){
					pushtoclient("tagging: ".$id3count);
					pushtosts("tagging: ".$id3count);
				}
				makesqlentryfortags($rootfilepath);
			}
		echo "<br> id3 count = ".$id3count;
		pushtoboth("id3 count = ".$id3count);	
}

if ($_POST['delunusedrecords'] == 'on'){
	echo "<br> deleteing unused records";
	pushtoboth("deleteing unused records");
			try {
				$statement = $conn->prepare("select `fullpath` FROM `songs`  WHERE 1 ");
				$statement->execute();
				$success = $statement->setFetchMode(PDO::FETCH_ASSOC);
			    $resultsarray = $statement->fetchAll();
			    }
			catch(PDOException $e)
			    {
			    echo "<br> failed: ". $e->getMessage();
			    }

		foreach($resultsarray as $aresult){
		$rootfilepath = $settings["musicroot-system"].DIRECTORY_SEPARATOR.$aresult['fullpath'];
			if (!is_file($rootfilepath)){
				removefromdb($aresult['fullpath']);
				$deletedcount = $deletedcount + 1;
				pushtoclient("removing ".$deletedcount);
				pushtoboth("removing ".$aresult['fullpath']);
				echo "<br> removing ".$aresult['fullpath'];
			}
			//echo ".";
		}	
}

echo "<br> removed ".$deletedcount;
pushtoboth("removed ".$deletedcount);

pushtoclient("Finished");
pushtoboth("Finished");
echo "<br> finished";
}


//------------------------------------------------------------------------------------------------------------

function expandDirectories($base_dir) {
	global $dircount;
      $directories = array();
      foreach(scandir($base_dir) as $file) {
            if($file == '.' || $file == '..') continue;
            $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
            if(is_dir($dir)) {
            	$dircount = $dircount + 1;
                $directories []= $dir;
                $directories = array_merge($directories, expandDirectories($dir));
            }
      }
      return $directories;
}


function ends_with_filetypes($haystack){
	global $settings;
	$uhay = strtoupper($haystack);
	$ftm = strtoupper($settings["filetypes"]);
	$filetypes = explode(' ', $ftm);
	foreach ($filetypes as $afiletype) {
		if (substr_compare($uhay, $afiletype, -strlen($afiletype)) === 0){
			return true;
		}
	}
    return false;
}

function removefromdb($rfileurl){
	global $conn;
	try {
		$statement = $conn->prepare("DELETE FROM `songs` WHERE `fullpath` = (?)");
		$statement->execute(array($rfileurl));
	    }
	catch(PDOException $e)
	    {
	    	echo "<br> file-".$rfileurl." failed: ". $e->getMessage();
	    	if ($e->errorInfo[1] != 1062) {
      	    	echo "<br> file-".$rfileurl." failed: ". $e->getMessage();
   			}
	    }
}


function makesqlentryforfile($rfileurl){
	global $conn;
	$defaulttitle1 = substr($rfileurl, strripos($rfileurl, DIRECTORY_SEPARATOR) + 1);
	$defaulttitle = substr($defaulttitle1, 0, strripos($defaulttitle1, '.'));
	try {
		$statement = $conn->prepare("INSERT INTO songs(fullpath, title) VALUES(?, ?)");
		$statement->execute(array($rfileurl, $defaulttitle));
	    }
	catch(PDOException $e)
	    {
	    	if ($e->errorInfo[1] != 1062) {
      	    	echo "<br> file-".$rfileurl." failed: ". $e->getMessage();
   				}
	    }
}

function makesqlentryfortags($rootfileurl){
	global $conn, $getID3, $settings;
	$fullfileurl = $settings["musicroot-system"].DIRECTORY_SEPARATOR.$rootfileurl;
	$ThisFileInfo = $getID3->analyze($fullfileurl);
	getid3_lib::CopyTagsToComments($ThisFileInfo);
	//echo '<pre>'.htmlentities(print_r($ThisFileInfo, true), ENT_SUBSTITUTE).'</pre>';
	$id3val = 'ok';
	if (strlen($ThisFileInfo['comments_html']['title'][0]) > 0){ 
		try {
			$statement = $conn->prepare("UPDATE `songs` SET title=:title, artist=:artist, album=:album, year=:year,  genre=:genre, duration=:duration, seconds=:seconds, id3state=:id3state WHERE fullpath=:fullpath ");
			$statement->bindParam(':title', $ThisFileInfo['comments_html']['title'][0]);
		    $statement->bindParam(':artist', $ThisFileInfo['comments_html']['artist'][0]);
		    $statement->bindParam(':album', $ThisFileInfo['comments_html']['album'][0]);
		    $statement->bindParam(':year', $ThisFileInfo['comments_html']['year'][0]);
		    $statement->bindParam(':genre', $ThisFileInfo['comments_html']['genre'][0]);
		    $statement->bindParam(':duration', $ThisFileInfo['playtime_string']);
		    $intseconds = intval(0 + $ThisFileInfo['playtime_seconds']);
		    $statement->bindParam(':id3state', $id3val);
		    $statement->bindParam(':seconds', $intseconds);
		    $statement->bindParam(':fullpath', $rootfileurl);
		    //$html = $tag["genre"].' '.$tag["title"].' -- '.'<hr>';
			$statement->execute();
			//$statement->commit();
		    }
		catch(PDOException $e)
		    {
		    echo "<br> file-".$fullfileurl." failed: ". $e->getMessage(); 
		    }

	}
	else
	{			// failed to get good id3 data so mark it unavailable so we dont keep rechecking
		try {
			$id3status = 'unavailable';
			$statement = $conn->prepare("UPDATE `songs` SET id3state=:id3state WHERE fullpath=:fullpath ");
		    $statement->bindParam(':id3state', $id3status);
		    $statement->bindParam(':fullpath', $rootfileurl);
			$statement->execute();
			//$statement->commit();
		    }
		catch(PDOException $e)
		    {
		    echo "<br> file-".$fullfileurl." failed: ". $e->getMessage(); 
		    }		
	}   
}

function pushtoclient($html){
	//echo $html;
	echo "<script >";
	echo "document.getElementById('headtext').innerHTML = \"".$html."\";";
	echo "</script>";
	#for($i=0;$i<70;$i++)
	#	{
	#	echo ' ';
	#	}	
	ob_flush();
	flush();
	//ob_clean();
}

function pushtoboth($html){
	global $statfile;
	$dhtml = "<br>".date("Y-m-d H:i:s ").$html;
	file_put_contents ($statfile.".sts" , $html);
	file_put_contents ($statfile.".log" , $dhtml, FILE_APPEND);

	ob_flush();
	flush();
}

function pushtosts($html){
	global $statfile;
	file_put_contents ($statfile.".sts" , $html);

	ob_flush();
	flush();
}
?>
