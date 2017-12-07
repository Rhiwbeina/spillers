This will create a user and database named spillers, it needs root or admin access to sql
<form method="POST" action="setupdb.php" name="sqlform" id="sqlform" >
    <center>
                Give root user name and password for sql to allow this setup programme to make a database.<br>
                This was probably setup when mysql was installed.
            <div >
                <label for="user"  >User Name</label>
                <input name="username" id="user" value="" type="text">
            </div>
        <br>
            <div>
                <label for="pass">Password</label>
                <input  name="password" id="pass" value="" type="text">
            </div>
            <hr>
            Choose a password to access the new SPILLERS database, this password must also be set in line 4 of dbconnect.php file.<br>
            As supplied the file contains password 'thehayes' so this default should work but not secure.
            <div>
                <label for="spillpass">Password</label>
                <input  name="spillpass" id="spillpass" value="thehayes" type="text">
            </div>

        <br>
            <button type="submit">Try to make database</button>
    </center>
</form>
<?PHP 
$servername = "127.0.0.1";
$dbusername = $_POST['username'];
$dbpassword = $_POST['password'];
$dbname = "spillers";

try {
    $conn = new PDO("mysql:host=$servername;charset=utf8", $dbusername, $dbpassword);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully ";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
$sql = "SELECT CURRENT_USER ";
    try {
        $stmt = $conn->query($sql);   
        $success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultsarray = $stmt->fetchAll();
    }
    catch(PDOException $e) {
        echo "<br>  failed: ". $e->getMessage();
    }
echo "<br>".$resultsarray[0]['CURRENT_USER']."<br>";


//print_r($resultsarray);

$sql = "GRANT USAGE ON *.* TO 'spillers'@'localhost' IDENTIFIED BY '".$_POST['spillpass']."'";
    try {
        $stmt = $conn->query($sql);   
        $success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultsarray = $stmt->fetchAll();
    }
    catch(PDOException $e) {
        echo "<br>  failed: ". $e->getMessage();
    }

$sql = "GRANT ALL PRIVILEGES ON `spillers`.* TO 'spillers'@'localhost'";
    try {
        $stmt = $conn->query($sql);   
        $success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultsarray = $stmt->fetchAll();
    }
    catch(PDOException $e) {
        echo "<br>  failed: ". $e->getMessage();
    }



// make db from text file
        try {
            $sql = file_get_contents("SQLdbSPEC.txt");
            //echo "<br>query: ".$sql;

            $stmt = $conn->query($sql);
            //$stmt->execute();
            $success = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultsarray = $stmt->fetchAll();
            //echo count($resultsarray);    
            echo "<br> all went well ";
            return $resultsarray;
            }
        catch(PDOException $e)
            {
              
                        echo "<br>  failed: ". $e->getMessage();

            }

echo "<br> IGNORE GENERAL ERRORS";
?>    