<?PHP include("auth.php");
$settings = getsettings();

    $file_name = rawurldecode ($_GET['filename']);
    $justfilename = substr($file_name, strrpos($file_name, DIRECTORY_SEPARATOR)+1);
    $file = $settings["musicroot-system"].DIRECTORY_SEPARATOR.$file_name ;
// I have protected the mp3 directory by .htaccess so to get round that this script reads and supplies the mp3 data
// it should also use auth.php to only allow download when logged in   

// log user and tune to history table
    $sql = "DELETE FROM `history` WHERE  `user` = '".$_SESSION['username']."' AND `tune` = '".$file_name."' ";
    $dum = domysql($sql);
    $sql = "INSERT INTO `history` (user, tune) VALUES ('".$_SESSION['username']."', '".$file_name."'  )";
    $dum = domysql($sql);

    $mime_type = "audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3, audio/mp4";
    header('Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3, audio/mp4');
    //header("Content-Disposition: inline;filename=\"".$justfilename."\"");
    //header("Content-Disposition: inline;");
    //header('Content-length: 200000'
    header('Content-length: '.filesize($file));
    //header('X-Pad: avoid browser bug');
    //header('Cache-Control: no-cache');
    header("Content-Transfer-Encoding: chunked");
    header('Content-Description: File Transfer');
    header('Accept-Ranges: bytes');

    ob_clean();
    flush();
    readfile($file);
    //echo "whydoes";
    exit;
?>
