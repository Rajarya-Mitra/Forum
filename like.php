<?php
session_start();
include 'partials/dbconnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    echo json_encode(['status' => 'redirect', 'message' => 'You need to be logged in to like a thread.']);
    exit();
}

$user_id = $_SESSION['u_id'];
$thread_id = $_POST['thread_id'];

// Check if the user has already liked the thread
$sql = "SELECT * FROM forum.likes WHERE user_id = $user_id AND thread_id = $thread_id";
$result = selectsql($sql);

if (!empty($result)) {
    // User has already liked the thread, so unlike it
    $sql = "DELETE FROM forum.likes WHERE user_id = $user_id AND thread_id = $thread_id";
    execsql($sql);
    
    // Decrease the like count in the threads table
    $sql = "UPDATE forum.threads SET likes = likes - 1 WHERE t_id = $thread_id";
    execsql($sql);

    // Fetch the updated like count
    $sql = "SELECT likes FROM forum.threads WHERE t_id = $thread_id";
    $likes_result = selectsql($sql);
    $likes = $likes_result[0]['likes'];
    
    echo json_encode(['status' => 'success', 'likes' => $likes, 'action' => 'unlike']);
} else {
    // User has not liked the thread, so like it
    $sql = "INSERT INTO forum.likes (user_id, thread_id) VALUES ($user_id, $thread_id)";
    execsql($sql);

    // Increase the like count in the threads table
    $sql = "UPDATE forum.threads SET likes = likes + 1 WHERE t_id = $thread_id";
    execsql($sql);

    // Fetch the updated like count
    $sql = "SELECT likes FROM forum.threads WHERE t_id = $thread_id";
    $likes_result = selectsql($sql);
    $likes = $likes_result[0]['likes'];
    
    echo json_encode(['status' => 'success', 'likes' => $likes, 'action' => 'like']);
}
?>
