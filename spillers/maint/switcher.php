<?php include("../auth.php"); ?>
<!DOCTYPE html>
<html>
<body>
<?PHP
//print_r($_POST);
$safetorun = 0;
$statfile = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], DIRECTORY_SEPARATOR))."/log/status";
//echo 	file_get_contents($statfile.".sts")."<br>";
if (file_exists($statfile.".sts")){
		if (file_get_contents($statfile.".sts") == "Finished" || time()- filemtime($statfile.".sts") > 20  ){
			$safetorun = 1;
		}
	}
	else
	{
		$safetorun = 1;
	}

$sql = "select admin from `users` where username='".$_SESSION['username']."' ";
$result = domysql($sql);
//echo "root=".$result[0]['admin'];

	if ($result[0]['admin'] == "1" && $safetorun == 1){
			file_put_contents($statfile.".sts", "Finished");
			startrun();
	}	
else
	{
		//echo "<br>Maybe running";
		inuse(file_get_contents($statfile.".sts"));
	}


function startrun(){
?>
<center>
<form action="javascript:void(0)" id="scanform">
	<label>
        <input name="wipedb" type="checkbox">Wipe all songs from db
    </label>
  <br>  
	<label>
        <input name="scanroot" type="checkbox">Scan root
    </label>
  <br> 
	<label>
        <input name="taguntagged" type="checkbox" checked="checked">Tag where tags missing
    </label>
  <br>    
	<label>
        <input name="delunusedrecords" type="checkbox" checked="checked">Delete unused records
    </label>
  <br>
<div id="backbutt" class="mybuttmargin ui-corner-all ui-btn-icon-left ui-btn ui-icon-arrow-l  ui-btn-icon" onClick="startScanlib()" >Do Scan</div>

</form>
</center>
<?PHP	
}

function inuse($message){
	if ($message == 'Finished'){
$sql = "SELECT COUNT(*) FROM `songs`";
$result = domysql($sql);
//print_r($result);
//echo "root=".$result[0]['COUNT(*)'];
?>
	<center>
	<form action="javascript:void(0)" >
		<label>
	        <input name="uinfo" type="checkbox"><?PHP echo $message; ?>
	    </label>
	  <br>  
	</form>
	</center>

	<center>
	<form action="javascript:void(0)" >
		<label>
	        <input name="uinfo" type="checkbox">Found <?PHP echo $result[0]['COUNT(*)']; ?> songs.
	    </label>
	  <br>  
	</form>
	</center>
<?PHP
		}
	else
		{
?>
	<center>
	<form action="javascript:void(0)" >
		<label>
	        <input name="uinfo" type="checkbox"><?PHP echo $message; ?>
	    </label>
	  <br>  
	</form>
	</center>
	<script>
	setTimeout(function(){ getPageFromForm('switcher.php', '', 'POST'); }, 1000);
	</script>
<?PHP	
	}
}
?>	


</body>
</html>
