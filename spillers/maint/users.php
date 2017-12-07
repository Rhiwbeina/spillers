<?PHP include("../auth.php");
	$htmltoaddmid = "";

if ($_POST['myaction'] == "adduser"){

	$htmltoaddmid = $htmltoaddmid.'<br><form  id="addfrm" action="javascript:void(0)" >
	<input type="hidden" name="myaction" id="myaction" value="comitadduser" />
	<label for="useriq">Username</label>
	<input type="text" name="useriq" id="useriq" value="'.$_POST['useriq'].'" /><br>
	<label for="npassword1">Password</label>
	<input type="password" name="npassword1" id="npassword1" value="" /><br>
	<label for="npassword2">Confirm Password</label>
	<input type="password" name="npassword2" id="npassword2" value="" /><br>
	<label>
        <input name="admin" type="checkbox">Administrator
    </label>
    <br>
    <div id="userbutt"  class="ui-corner-all ui-btn-icon-right ui-btn ui-icon-plus  ui-btn-icon" onClick="getPageFromForm(\'users.php\', \'addfrm\', \'POST\');">Add this User</div>
	</form>';

//$resultsarray = domysql("update `settings` set `value` = '".$_POST['value']."' where `setting` LIKE '".$_POST['setting']."' ");	
//print_r($_POST);
}

if ($_POST['myaction'] == "comitadduser"){
//	echo "trying to comit";
	$message = "OK";
	if ( strlen($_POST['npassword1']) < 3){ $message = "Password too short"; }
	if ( $_POST['npassword1'] != $_POST['npassword2']){ $message = "Passwords do not match"; }
	if ( strlen($_POST['useriq']) < 3){ $message = "User name too short"; }	
		if ($message == "OK"){
			$passwordhash = md5($_POST['npassword1']);
			$admin = "0";
			if ($_POST['admin'] == "on"){$admin = "1";}
		$resultsarray = domysql("INSERT INTO `users`( `username`, `userpass`, `admin`) VALUES ('".$_POST['useriq']."','".$passwordhash."','".$admin."')");

			$htmltoaddmid = $htmltoaddmid.'<br>';
			$htmltoaddmid = $htmltoaddmid.'<script>
			$("#mypopup").html("<h2><center>Created user <br>'.$_POST["useriq"].'<br>'.$message.'</center></h2>");
			$("#mypopup").popup("open"); 
			setTimeout(function(){  $("#mypopup").popup("close"); }, 2500);
			setTimeout(function(){ getPageFromForm(\'users.php\', \'\', \'POST\'); }, 2500);
			</script>';				
		}
		else
		{
			$htmltoaddmid = $htmltoaddmid.'<form  id="userfrm" action="javascript:void(0)" >
			<input type="hidden" name="myaction" id="myaction" value="adduser" />
			<input type="hidden" name="useriq" id="useriq" value="'.$_POST['useriq'].'" />
			</form>';

			$htmltoaddmid = $htmltoaddmid.'<script>
			$("#mypopup").html("<h2><center>Failed to create user <br>'.$_POST["useriq"].'<br>'.$message.'</center></h2>");
			$("#mypopup").popup("open"); 
			setTimeout(function(){  $("#mypopup").popup("close"); }, 2500);
			setTimeout(function(){ getPageFromForm(\'users.php\', \'userfrm\', \'POST\'); }, 2500);
			</script>'; 

		}

}

if ($_POST['myaction'] == "selectuser"){ // user has been selected options now delete or cancel op
	$resultsarray = domysql('SELECT admin FROM users WHERE myindex=\''.$_POST["myindex"].'\'');
	//echo $resultsarray[0]['admin'];
	$admintrue = $resultsarray[0]['admin'];

	$resultsarray = domysql('SELECT * FROM users WHERE admin=\'1\'');
	if ( count($resultsarray) < 2 && $admintrue == '1') {
	$htmltoaddmid = $htmltoaddmid.'<br><form  id="usedfrm" action="javascript:void(0)" >
	<button class="ui-btn ui-corner-all" >At least 1 admin account needed</button>
	<button class="ui-btn ui-corner-all" onClick="getPageFromForm(\'users.php\', \'\', \'POST\');" > Back </button>
	</form>';
	}
	else
	{
	$htmltoaddmid = $htmltoaddmid.'<br><form  id="usedfrm" action="javascript:void(0)" >
	<input type="hidden" name="myaction" id="myaction" value="deleteuser" />
	<input type="hidden" name="myindex" id="myindex" value="'.$_POST["myindex"].'" />
	<input type="hidden" name="useriq" id="useriq" value="'.$_POST["useriq"].'" />
	<button class="ui-btn ui-corner-all" onClick="getPageFromForm(\'users.php\', \'usedfrm\', \'POST\');" >Remove '.$_POST["useriq"].'</button>
	<button class="ui-btn ui-corner-all" onClick="getPageFromForm(\'users.php\', \'\', \'POST\');" > Back </button>
	</form>';		
	}

}

if ($_POST['myaction'] == "deleteuser"){
		$resultsarray = domysql('DELETE FROM users WHERE myindex='.$_POST["myindex"]);
		$htmltoaddmid = $htmltoaddmid.'<br>	';
		$htmltoaddmid = $htmltoaddmid.'<script>
		  $("#mypopup").html("<h2><center>Deleted user accound <br>'.$_POST["useriq"].'</center></h2>");
		  $("#mypopup").popup("open"); 
		  setTimeout(function(){  $("#mypopup").popup("close"); }, 2500);
		  setTimeout(function(){ getPageFromForm(\'users.php\', \'\', \'POST\'); }, 2500);
		</script>';
}

if (!$_POST['myaction']){
	unset($resarray);
	$resultsarray = domysql("select * from users where 1");
	if ( count($resultsarray) > 0) {
		$idcounter = 0;
	// output data of each row
			foreach ($resultsarray as $row) {
				if ($row["admin"] == '1'){
				 $aicon='<span class="ui-btn-right ui-btn-inline">
						<span class="ui-btn ui-btn-inline ui-corner-all ui-btn-icon-notext ui-btn-icon ui-icon-star" onClick="javascript:void(0)"></span>
						</span>';
				} else {
				 $aicon='<span class="ui-btn-right ui-btn-inline">
						<span class="ui-btn ui-btn-inline ui-corner-all ui-btn-icon-notext ui-btn-icon ui-icon-user" onClick="javascript:void(0)"></span>
						</span>';
				}

$buttontext ='<span class="ptitle" data-inline="true">'.$row["username"].'</span>'.$aicon;

			$htmltoaddmid = $htmltoaddmid.'<form  id="userfrm'.$idcounter.'" action="javascript:void(0)" >
			<input type="hidden" name="myaction" id="myaction" value="selectuser" />
			<input type="hidden" name="myindex" id="myindex" value="'.$row["myindex"].'" />
			<input type="hidden" name="useriq" id="useriq" value="'.$row["username"].'" />
			<button class="ui-btn ui-corner-all" onClick="getPageFromForm(\'users.php\', \'userfrm'.$idcounter.'\', \'POST\');" >'.$buttontext.'</button>
			</form>';
			$idcounter = $idcounter + 1;
			}
	}

	$htmltoaddmid = $htmltoaddmid.'<form  id="addfrm" action="javascript:void(0)" >
	<input type="hidden" name="myaction" id="myaction" value="adduser" />
	<input type="button" value="Add New User" onClick="getPageFromForm(\'users.php\', \'addfrm\', \'POST\');"  />
	</form>';

}

echo $htmltoaddmid;
?>
