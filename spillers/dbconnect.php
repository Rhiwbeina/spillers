<?PHP 
$servername = "127.0.0.1";
$dbusername = "spillers";
$dbpassword = "thehayes";
$dbname = "spillers";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

function domysql($sql){
    global $conn;
try {
    //echo "<br>query: ".$sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $resultsarray = $stmt->fetchAll();
    //echo count($resultsarray);    
    //print_r($resultsarray);
    return $resultsarray;
    }
catch(PDOException $e)
    {
        if ($e->errorInfo[1] > 0) {
                echo "<br>  failed: ". $e->getMessage();
        }
    }
}

function getsettings(){
    global $conn;
        try {
            $resarray = domysql("select * from settings where 1");
            $settings = array();
                foreach ($resarray as $aresult){
                    $settings[$aresult['setting']] = $aresult['value'];
                }
            return $settings;
            }
        catch(PDOException $e)
            {
            echo "warning or error: " . $e->getMessage();
            }
}
?>
