<?php
include 'partials/dbconnect.php';

$thread_id = $_GET['threadid'];
$user_id = $_GET['userid'];
$sort = $_GET['sort'];
$order_by = 'created_at DESC';

if ($sort == 'oldest') {
    $order_by = 'created_at ASC';
} elseif ($sort == 'popular') {
    $order_by = 'likes DESC';
}

$sql = "SELECT * FROM forum.comments WHERE t_id = $thread_id AND comment_by = $user_id ORDER BY $order_by";
$result = selectsql($sql);
$noResult = true;

foreach ($result as $row) {
    $noResult = false;
    $comment_id = $row['comment_id'];
    $comment_content = $row['comment_content'];
    $comment_time = $row['created_at'];
    $likes = $row['likes'];
    $is_edited = $row['is_edited'];

    $liked = false;
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        $sql3 = "SELECT * FROM forum.likes WHERE user_id = $user_id AND comment_id = $comment_id";
        $like_result = selectsql($sql3);
        $liked = !empty($like_result);
    }

    echo '<div class="media my-3">
        <img class="mr-3" src="img/user_default.png" width="50px" alt="Media Img">
        <div class="media-body">
            <p class="font-weight-bold my-0">You at ' . $comment_time . ($is_edited ? ' (edited)' : '') . '</p>
            <h5 class="mt-8"><a class="text-dark" href="threads.php?commentid=' . $comment_id . '"></a></h5>
            ' . $comment_content . '
        </div>
        <div class="mr-3">
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal" onclick="populateEditForm(' . $comment_id . ', \'' . addslashes($comment_content) . '\')">Edit</button>
            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal" onclick="populateDeleteForm(' . $comment_id . ')">Delete</button>
        </div>
        <div>
            <button class="btn btn-sm btn-info like-btn" data-type="thread" data-id="' . $comment_id . '">
                <i class="bi ' . ($liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up') . '"></i>
            </button>
            <span id="like-count-' . $comment_id . '">' . $likes . '</span>
        </div>
    </div>';
}

if ($noResult) {
    echo '<div class="jumbotron jumbotron-fluid">
            <div class="container">
                <p class="lead">You have not posted any comments yet</p>
            </div>
          </div>';
}
?>
