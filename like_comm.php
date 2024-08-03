<?php
session_start();
include 'partials/dbconnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    echo json_encode(['status' => 'redirect', 'message' => 'You need to be logged in to like a comment.']);
    exit();
}

$user_id = $_SESSION['u_id'];
$comment_id = $_POST['comment_id'];

// Check if the user has already liked the comment
$sql = "SELECT * FROM forum.likes WHERE user_id = $user_id AND comment_id = $comment_id";
$result = selectsql($sql);

if (!empty($result)) {
    // User has already liked the comment, so unlike it
    $sql = "DELETE FROM forum.likes WHERE user_id = $user_id AND comment_id = $comment_id";
    execsql($sql);
    
    // Decrease the like count in the comments table
    $sql = "UPDATE forum.comments SET likes = likes - 1 WHERE comment_id = $comment_id";
    execsql($sql);

    // Fetch the updated like count
    $sql = "SELECT likes FROM forum.comments WHERE comment_id = $comment_id";
    $likes_result = selectsql($sql);
    $likes = $likes_result[0]['likes'];
    
    echo json_encode(['status' => 'success', 'likes' => $likes, 'action' => 'unlike']);
} else {
    // User has not liked the comment, so like it
    $sql = "INSERT INTO forum.likes (user_id, comment_id) VALUES ($user_id, $comment_id)";
    execsql($sql);

    // Increase the like count in the comments table
    $sql = "UPDATE forum.comments SET likes = likes + 1 WHERE comment_id = $comment_id";
    execsql($sql);

    // Fetch the updated like count
    $sql = "SELECT likes FROM forum.comments WHERE comment_id = $comment_id";
    $likes_result = selectsql($sql);
    $likes = $likes_result[0]['likes'];
    
    echo json_encode(['status' => 'success', 'likes' => $likes, 'action' => 'like']);
}
?>
