<?php
//Script to connect to the database
$servername = "localhost";
$username = "root";
$password = "usbw";
$database = "forum";
//$conn = mysql_connect($servername,$username,$password,$database);
$conn = new PDO("mysql:host=$servername;dbname=$database; port=3307", $username, $password);
if(!$conn) {die( "Database not connected");}
//var_dump($conn);
function selectsql($sql){
    global $conn;
    $sth = $conn->prepare($sql);
    $sth->execute();
    $row = $sth->fetchAll(PDO::FETCH_ASSOC);
    return $row;
}
function execsql($sql){
    global $conn;
    $sth = $conn->prepare($sql);
    $result=$sth->execute();
    return $result;
}

?>