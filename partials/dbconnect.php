<?php
// Script to connect to the database
$servername = "localhost";
$username = "root";
$password = "usbw";
$database = "forum";
$port = 3307;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;port=$port", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database not connected: " . $e->getMessage());
}

function selectsql($sql, $params = []) {
    global $conn;
    $sth = $conn->prepare($sql);
    $sth->execute($params);
    $row = $sth->fetchAll(PDO::FETCH_ASSOC);
    return $row;
}

function execsql($sql, $params = []) {
    global $conn;
    $sth = $conn->prepare($sql);
    $result = $sth->execute($params);
    return $result;
}
?>
