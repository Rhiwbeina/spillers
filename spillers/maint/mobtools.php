<?php include("../auth.php"); 
if ($allowadmin == '1'){
$headerhtml = '<fieldset class="ui-grid-c">
                  <div class="ui-block-a">
                      <center><div id="backbutt" class="mybuttmargin ui-corner-all ui-btn-icon-left ui-btn ui-icon-arrow-l  ui-btn-icon" onClick="window.location.href = \'../index.html\'" >Back</div></center>
                  </div>

                  <div class="ui-block-b">
                      <center><div id="scanbutt"    class="mybuttmargin ui-corner-all ui-btn-icon-left ui-btn ui-icon-delete  ui-btn-icon" onClick="getPageFromForm(\'settings.php\', \'\', \'POST\')">Settings</div></center>                  
                  </div>

                  <div class="ui-block-c">
                      <center><div id="scanbutt"    class="mybuttmargin ui-corner-all ui-btn-icon-left ui-btn ui-icon-delete  ui-btn-icon" onClick="getPageFromForm(\'switcher.php\', \'\', \'POST\')">Scan</div></center>                  
                  </div>

                  <div class="ui-block-d">
                      <center><div id="usersbutt"  class="mybuttmargin ui-corner-all ui-btn-icon-left ui-btn ui-icon-grid  ui-btn-icon" onClick="getPageFromForm(\'users.php\', \'\', \'POST\');">Users</div></center>
                  </div>
              </fieldset>';

}
else
{
  $headerhtml = '<center> Admin not allowed for user '.$_SESSION['username'].'</center>';
}

?>
<!DOCTYPE html>
<html>
<head>
  <!-- Include meta tag to ensure proper rendering and touch zooming -->
  <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
  <!-- Include jQuery Mobile stylesheets -->
  <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
  <!-- Include the jQuery library -->
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>   
  <!-- Include the jQuery Mobile library -->
  <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
  <link rel="stylesheet" href="../moepg.css">
  <!--
  -->
</head>
<body>
  <div data-role="page" id="toolspage" data-transition="slide" data-theme="a">
      <div data-role="header" data-position="fixed" id="ttop">
          <?= $headerhtml ?>
      </div>

      <div data-role="main" class="ui-content" id="tmid" data-theme="a">

      </div>

      <div data-role="footer" data-position="fixed" id="tbot" data-theme="a">


      </div>
    <div data-role="popup" id="mypopup" data-position-to="window" data-theme="a" data-transition="pop">hgfjhg jhgf jhgf jhgf jhgfj hgfj gfjgfj hgjfh </div> 

  </div>

</body>

//----------------------------------------------------------------------------------------

<script>


$( document ).ready(function() {
//$(document).on('pageinit', function(){
    console.log( "ready!" );

});


function getPageFromForm(url, formid, method){
  console.log(url, formid, method);
  //$('#' + formid + ' #search').blur();
  $.mobile.loading("show",{ textVisible: false, theme: 'a', html: ""});
  var formdata =  "";  
    if ( $( "#" + formid ).length ) { 
            console.log("form do exists");
            formdata =  $("#toolspage #" + formid).serialize();
      } else {
            console.log("form NOT exists");
            //ar logindata =  "";
      }

  $.ajax({
          url: url,
          cache: false,
          async: true,
          type: method,            
          data: formdata,
          timeout: 5000, // in milliseconds
          success: function(data) {              
            $('#tmid').html(data);
            $('#tmid').trigger("create");
            $.mobile.loading( "hide");     
          },
          error: function(request, status, err) {
            $('#tmid').html(err);
            $.mobile.loading( "hide");
            //getPageFromForm('switcher.php', '', 'POST');
          }
      });
}

function startScanlib(){
  //$.mobile.loading("show",{ textVisible: false, theme: 'a', html: ""});

  var formdata =  ""; 
  formdata =  $("#toolspage #scanform").serialize();

  $.ajax({
          url: 'scanlib.php',
          cache: false,
          async: true,
          type: 'POST',            
          data: formdata,
          timeout: 100, // in milliseconds
          success: function(data) {              
            $('#tmid').html(data);
            $('#tmid').trigger("create");
            //$.mobile.loading( "hide");     
          },
          error: function(request, status, err) {
            $('#tmid').html(err);
            //$.mobile.loading( "hide");
            getPageFromForm('switcher.php', '', 'POST');
          }
      });
}


function playnow(buttid) {
  var album = decodeURIComponent($("#" + buttid).data("album"));  
  console.log("audio after=");

  setTimeout(function(){ $('#pageone #playbox').hide(1500); }, 4500);

// update whats playing details at bottom of screen
  document.getElementById('nowplaying').innerHTML = title;
  document.getElementById('nowartist').innerHTML = artist + ' - ' + album;
}

</script>
</html>