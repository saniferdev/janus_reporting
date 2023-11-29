<?php
$servername = "localhost";
$username = "root";
$password = "";

/*try {
    $conn = new PDO("mysql:host=$servername;dbname=testserialize", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

$sql = "SELECT * FROM serialize";

$getData = $conn->query($sql);

$data = $getData->fetchAll(PDO::FETCH_OBJ);

var_dump($data);*/


/*$data = file_get_contents("serialize.txt");
$data_uncompressed = unserialize($data);
var_dump($data_uncompressed);*/
if(isset($_POST['Valider'])){
    $qtt = $_POST['qtt'];
    $datetime = new DateTime();
    $date= $datetime->format('Y-m-d H:i:s');

    $histo = array(['qtt'=>$qtt, 'date'=>$date]);
    $serialize = serialize($histo);
    var_dump($serialize);
die();
    if(empty($data)){
        $sql = "INSERT INTO serialize(histo) VALUES ('".$serialize."')";
        var_dump($sql);
        $insert = $conn->query($sql);
    }else{
        $data_uncompressed = unserialize($data[0]->histo);
        array_push($data_uncompressed,['qtt'=>$qtt,'date'=>$date]);
        var_dump($data_uncompressed);
        $serialize = serialize($data_uncompressed);
        var_dump($serialize);
        $sqlt = "UPDATE serialize
                SET histo = '".$serialize."'";
        $conn->query($sqlt);
    }

}

//$datetime->setTimezone($tz_object);

/*
array_push($data_uncompressed,['qtt'=>$qtt,'date'=>$date]);
var_dump($data_uncompressed);


$histoS=serialize($data_uncompressed);

file_put_contents("serialize.txt", $histoS);
echo $histoS;
echo "<br>";*/
?>
<html>
<head>

</head>
<body>
<form method="post">
    qtt : <input type="text" name="qtt">
    <input type="submit" value="Valider" name="Valider">
</form>
</body>
</html>