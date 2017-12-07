<?PHP include("auth.php");
$settings = getsettings();
$prelisthtml = "";
if ($_POST['myaction'] != "findhistory"){

		try {
			$statement = $conn->prepare("select songs.* FROM `history` inner join `songs` on history.tune = songs.fullpath  WHERE history.user = :search1 ORDER BY history.date DESC ");
			$statement->bindParam(':search1', $loggedinuser, PDO::PARAM_STR);
			//$statement->execute(array(':search1' => $settings['user']));
			$statement->execute();
			$success = $statement->setFetchMode(PDO::FETCH_ASSOC);
		    $resultsarray = $statement->fetchAll();
		    }
		catch(PDOException $e)
		    {
		    echo "<br> failed: ". $e->getMessage();
		    }
}

if ($_POST['myaction'] == "findalbum"){
		try {
			$statement = $conn->prepare("select * FROM `songs`  WHERE (`album` LIKE :search1 ) ORDER BY fullpath ASC");
			//$statement->bindParam(':search1', "ee");
			$statement->execute(array(':search1' => $_POST['search']));
			//$statement->execute();
			$success = $statement->setFetchMode(PDO::FETCH_ASSOC);
		    $resultsarray = $statement->fetchAll();
		    }
		catch(PDOException $e)
		    {
		    echo "<br> failed: ". $e->getMessage();
		    }

}

//--------------------------------------- output results ----------------------------------------------------

	//$resultsarray = domysql("select * from users where 1");
	//if ( count($resultsarray) > 0){
	//	foreach ($resultsarray as $row) {

	//	}
	//}
echo '<div class="ui-field-contain">
    <select name="select-custom-2" id="select-custom-2" data-native-menu="false" data-mini="true">
        <option value="1">The 1st Option</option>
        <option value="2">The 2nd Option</option>
        <option value="3">The 3rd Option</option>
        <option value="4">The 4th Option</option>
    </select>
</div>';

if ( count($resultsarray) > 0) {
$idcounter = 0;
// output data of each row
		foreach ($resultsarray as $row) {
		$buttid = "songbutt".$idcounter; 
		$titleenc  = rawurlencode($row["title"]);
		$artistenc = rawurlencode($row["artist"]);
		$albumenc = rawurlencode($row["album"]);
		$playURL = rawurlencode($row["fullpath"]);
	#$playURL = rawurlencode($settings["musicroot-web"]."/".$row["fullpath"]."");	
		//echo "<button class=\"ui-btn ui-corner-all\" style=\"text-align: left;\" onclick=\"playnow('".$playURL."', '".$titleurl."', '".$artisturl."', this)\"  mytitle=\"".$titleurl."\" >";
		echo "<button class=\"ui-btn ui-corner-all\" style=\"text-align: left;\" 
		onclick=\"playnow('".$buttid."')\" 
		id=\"".$buttid."\"
		data-playurl=\"".$playURL."\"  
		data-title=\"".$titleenc."\" 
		data-artist=\"".$artistenc."\" 
		data-album=\"".$albumenc."\" >";

		echo " <span class=\"ptitle\">" . $row["title"]. "</span>";
		echo " <p class=\"sminfo notrunc\">" .$row["artist"] .  "</p>";
		echo "</button> " ;
		$idcounter = $idcounter + 1;
		}
    
//------------------ if no results say so also show settings button for admin users -------------
} else {
		echo '<button class="ui-btn ui-corner-all" style="text-align: left;" 
		onclick="javascript:void(0)">
    	<span class="ptitle">Nothing Found</span>
		<p class="sminfo notrunc"> for '.$search.'</p>
		</button>';

		if ($allowadmin == '1'){
		echo '<button class="ui-btn ui-corner-all" style="text-align: left;" 
		onClick="window.location.href = \'maint/mobtools.php\'">
    	<span class="ptitle">Settings </span>
		<p class="sminfo notrunc"> System settings, user admin</p>
		</button> ' ;
		}

}

?>  