<?php
session_start();
include 'partials/dbconnect.php';

$order_by = 'created_at DESC';
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'popular') {
        $order_by = 'likes DESC';
    } elseif ($_GET['sort'] == 'oldest') {
        $order_by = 'created_at ASC';
    }
}

$sql = "SELECT * FROM forum.threads ORDER BY $order_by";
$result = selectsql($sql);

foreach ($result as $row) {
    $t_id = $row['t_id'];
    $t_title = $row['t_title'];
    $t_desc = $row['t_desc'];
    $thread_time = $row['created_at'];
    $t_u_id = $row['t_u_id'];
    $likes = $row['likes'];
    $is_edited = $row['is_edited'];

    $sql2 = "SELECT u_name,u_profile_photo FROM forum.users WHERE u_id = '$t_u_id'";
    $row2 = selectsql($sql2);

    $user_photo = !empty($row2[0]['u_profile_photo']) ? $row2[0]['u_profile_photo'] : 'img/user_default.png';
    $liked = false;
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        $user_id = $_SESSION['u_id'];
        $sql3 = "SELECT * FROM forum.likes WHERE user_id = $user_id AND thread_id = $t_id";
        $like_result = selectsql($sql3);
        $liked = !empty($like_result);
    }
    
    echo '<div class="media my-3">
            <img class="mr-3 rounded-circle" src="' . $user_photo . '" width="50px" height="50px" alt="User Image">
            <div class="media-body">
                <h5 class="font-weight-bold my-0">' . $row2[0]['u_name'] . '</h5>
                <h5 class="mt-0"><a class="text-dark" href="threads.php?threadid=' . $t_id . '">' . $t_title . ($is_edited ? ' (edited)' : '') . '</a></h5>
                ' . $t_desc . '
                <div class="font-weight-bold my-0">Asked at ' . $thread_time . '</div>
            </div>
            <div>
                <button title="Like" class="btn-primary like-btn ' . ($liked ? 'liked' : '') . '" data-id="' . $t_id . '">
                    <i class="bi ' . ($liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up') . '"></i>
                </button>
                <span class="ml-2" id="like-count-' . $t_id . '">' . $likes . '</span>
            </div>
        </div>';
}
?>
