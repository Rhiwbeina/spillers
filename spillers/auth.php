<?PHP session_start();
include("dbconnect.php");
// convert username and password from _POST or _SESSION
if($_POST["username"]){
  $_SESSION['username']=$_POST["username"];
  $_SESSION['password']=$_POST["password"];
}
$testmd5=md5($_SESSION['password']);

$sql = "select * from `users` where username='".$_SESSION['username']."' and userpass='".$testmd5."'";
session_write_close(); // should have finished with the sesh
$result = domysql($sql);
//$myhtml = "<h> hello </h>";
$num=count($result);
// print login form and exit if failed.
if($num < 1 ){
    $loginfor = '<form method="POST" action="" name="reg-form" id="reg-form" data-ajax="false" >
    <center>
<div >
    <label for="user" class="mytextin" >User Name</label>
    <input class="mytextin ui-corner-all" data-theme="a" name="username" id="user" value="" type="text">
</div>
<br>
<div>
    <label for="pass">Password</label>
    <input class="mytextin ui-corner-all" name="password" id="pass" value="" type="password" onChange="getPageFromForm(\'greeting.php\', \'reg-form\', \'POST\')">
    </div>
<br>
<div class="mymargin ui-btn ui-btn-inline ui-corner-all data-theme="a" data-ajax="false" onClick="getPageFromForm(\'greeting.php\', \'reg-form\', \'POST\')">
<div  class="mybuttmargin ui-icon-check  ui-btn-icon-notext " ></div>
</div>

</center>
</form>
<script>
  $("#mypopup").html("<h2> Please Login </h2> ");
  $("#mypopup").popup("open"); 
  setTimeout(function(){  $("#mypopup").popup("close"); }, 2000);
</script>

 ';	

echo $loginfor;

exit;
}
else {
  $allowadmin = $result[0]['admin'];
  $loggedinuser = $_SESSION['username'];
// log user and action to history
//  $serdata = serialize($_GET);
//  $sql = "INSERT INTO `history` (user, action) VALUES ('".$_SESSION['username']."', '".$serdata."'  )";
//$dum = domysql($sql);
}
// authenticated so continue the page that called this
?>