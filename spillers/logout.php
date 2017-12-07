<?PHP session_start();
  $_SESSION['username']="";
  $_SESSION['password']="";
  // session_destroy();
?>
<!DOCTYPE html>
<html>
<body>
<center>logging out</center>
<script>
setTimeout(function(){ window.location.href = '../mobseltest.php' }, 100);
</script>

</body>
</html>