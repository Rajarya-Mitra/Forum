<?php
include 'dbconnect.php';

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 5;

$sql = "SELECT * FROM forum.categories LIMIT $limit OFFSET $offset";
$result = selectsql($sql);

echo json_encode($result);
?>
