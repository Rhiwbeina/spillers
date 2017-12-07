<?PHP include("../auth.php");

if ($_POST['value'] &&  $_POST['setting']){
$resultsarray = domysql("update `settings` set `value` = '".$_POST['value']."' where `setting` LIKE '".$_POST['setting']."' ");	
}
 

unset($resarray);
$resultsarray = domysql("select * from settings where 1");

$htmltoaddmid = "";
if ( count($resultsarray) > 0) {
	$idcounter = 0;
// output data of each row
		foreach ($resultsarray as $row) {
		//$settingname  = rawurlencode($row["setting"]);
		//$settingvalue = rawurlencode($row["value"]);
		//echo "<button class=\"ui-btn ui-corner-all\" style=\"text-align: left;\" onclick=\"playnow('".$playURL."', '".$titleurl."', '".$artisturl."', this)\"  mytitle=\"".$titleurl."\" >";

		$htmltoaddmid = $htmltoaddmid.'<label for="value">'.$row["setting"].'</label>';
		$htmltoaddmid = $htmltoaddmid.'<form  id="settingfrm'.$idcounter.'" action="javascript:void(0)" >
		<input type="hidden" name="setting" id="setting" value="'.$row["setting"].'" />
		<input type="text" name="value" id="value" value="'.$row["value"].'" onChange="getPageFromForm(\'settings.php\', \'settingfrm'.$idcounter.'\', \'POST\');"  />
		</form>';
		$idcounter = $idcounter + 1;
		}
}
echo $htmltoaddmid;
?>
