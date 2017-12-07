<?PHP include("auth.php"); 
$greet = "<h2> Croeso ".$_SESSION['username']."  </h2>"
// if auth fails it displays login form and exits
?>
<script>
  $("#pageone #mypopup").html("<?PHP echo $greet;?>");
  //$("#mypopup").enhanceWithin(); 
  $("#pageone #mypopup").popup("open"); 
  setTimeout(function(){  $("#pageone #mypopup").popup("close"); }, 1500);
</script>
