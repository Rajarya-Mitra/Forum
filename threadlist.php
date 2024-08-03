<!doctype html>
<html lang='en'>
<?php include '/partials/dbconnect.php'; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>weConnect</title>
</head>
<body>
    <?php include 'partials/header.php';?>
    <?php
        $cat_id = $_GET['catid'];
        $sql = "SELECT * FROM forum.categories WHERE c_id = $cat_id";
        $result = selectsql($sql);
        foreach($result as $row){
            $catname = $row['c_name'];
            $catdesc = $row['c_description'];
        }
    ?>
    <?php
        // Fetch the logged-in user's role at the start of your script
        $logged_in_user_role = '';
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $logged_in_user_id = $_SESSION['u_id'];
            $sql = "SELECT u_role FROM forum.users WHERE u_id = '$logged_in_user_id'";
            $logged_in_user_result = selectsql($sql);
            if (!empty($logged_in_user_result)) {
                $logged_in_user_role = $logged_in_user_result[0]['u_role'];
            }
        }
    ?>

    <?php
        $sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;
        
        if ($sort == 'oldest') {
            $order_by = 'created_at ASC';
        } elseif ($sort == 'popular') {
            $order_by = 'likes DESC';
        } else 
        $order_by = 'created_at DESC';
    ?>

    <?php      
        $showAlert = false; 
        if(isset($_POST['t_title'])){
            // Handle Post Threads
            $t_title = htmlspecialchars($_POST['t_title']);
            $t_desc = htmlspecialchars($_POST['t_desc']);
            $u_id = $_POST['u_id'];
            $sql = "INSERT INTO forum.threads (t_title, t_desc, t_c_id, t_u_id, created_at) VALUES ('$t_title', '$t_desc', '$cat_id', '$u_id', CURRENT_TIMESTAMP)";
            $result = execsql($sql);
            if($result) {
                $showAlert = true;
            }
            if($showAlert){
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your question has been added! Please wait for someone to respond.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }                
        }
        
        // Handle Edit Thread
        if(isset($_POST['edit_thread'])){
            $edit_thread_id = $_POST['edit_thread_id'];
            $edit_t_title = htmlspecialchars($_POST['edit_t_title']);
            $edit_t_desc = htmlspecialchars($_POST['edit_t_desc']);
            $sql = "UPDATE forum.threads SET t_title = '$edit_t_title', t_desc = '$edit_t_desc', is_edited = TRUE WHERE t_id = $edit_thread_id";
            $result = execsql($sql);
            if($result) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your question has been updated.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }
        }

        // Handle Delete Thread
        if(isset($_POST['delete_thread'])){
            $thread_id = $_POST['delete_thread_id'];
            $sql = "DELETE FROM forum.threads WHERE t_id = $thread_id";
            $result = execsql($sql);
            if($result) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your question has been deleted.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }
        }
    ?>
        
    <div class="container my-4">
        <div class="jumbotron">
            <h1 class="display-4">Welcome to <?php echo $catname;?> Forums</h1>
            <p class="lead"><?php echo $catdesc;?></p>
            <hr class="my-4">
            <p>This a peer to peer forum. Spam / Advertising / Self-promote in the forum is not allowed. Do not post copyright-infringing material. Do not post "offensive" posts, links or images. Do not cross post questions. Remain respectful of other members at all times.</p>
            <p class="lead">
                <a class="btn btn-success btn-lg" href="#" role="button">Learn More</a>
            </p>
        </div>
    </div>

    <?php
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
        echo '<div class="container">
                <h1 class="py-2">Post a Question</h1>
                <form action = "' . $_SERVER["REQUEST_URI"] . '" method="post">
                    <div class="form-group">
                        <label for="t_title">Question Title</label>
                        <input type="text" class="form-control" id="t_title" name="t_title" aria-describedby="questionlHelp">
                        <small id="questionHelp" class="form-text text-muted">Keep your title as short and crisp as possible.</small>
                    </div>
                    <input type="hidden" name="u_id" value="'. $_SESSION["u_id"] .'">
                    <div class="form-group">
                        <label for="t_desc">Elaborate your Question</label>
                        <textarea class="form-control" id="t_desc" name="t_desc" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-2 mb-5">Submit</button>
                </form>
            </div>';
        }   

        echo'<div class="container">
            <div class="btn-group mb-3" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary mr-2" onclick="loadThreads(\'newest\')">Newest</button>
                <button type="button" class="btn btn-secondary" onclick="loadThreads(\'popular\')">Popular</button>
                <button type="button" class="btn btn-info mx-2" onclick="loadThreads(\'oldest\')">Oldest</button>
            </div>
        </div>';

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            echo '<div class="container">
                    <h1 class="py-2">Your Questions</h1>';
            $user_id = $_SESSION['u_id'];
            $sql = "SELECT * FROM forum.threads WHERE t_c_id = $cat_id AND t_u_id = $user_id ORDER BY $order_by";
            $result = selectsql($sql);
            $noResult = true;
        
            foreach ($result as $row) {
                $noResult = false;
                $t_id = $row['t_id'];
                $t_title = $row['t_title'];
                $t_desc = $row['t_desc'];
                $thread_time = $row['created_at'];
                $t_u_id = $row['t_u_id'];
                $likes = $row['likes'];
                $is_edited = $row['is_edited'];
        
                $sql2 = "SELECT u_name FROM forum.users WHERE u_id = '$t_u_id'";
                $row2 = selectsql($sql2);
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                    $user_id = $_SESSION['u_id'];
                    $sql3 = "SELECT * FROM forum.likes WHERE user_id = $user_id AND thread_id = $t_id";
                    $like_result = selectsql($sql3);
                    $liked = !empty($like_result);
                }
                echo '<div class="media my-3">
                        <img class="mr-3" src="img/user_default.png" width="50px" alt="Media Img">
                        <div class="media-body">
                            <h5 class="font-weight-bold my-0">' . $row2[0]['u_name'] . '</h5>
                            <h5 class="mt-8"><a class="text-dark" href="threads.php?threadid=' . $t_id . '">' . $t_title . ($is_edited ? ' (edited)' : '') . '</a></h5>
                            <p>' . $thread_time . '</p>
                        </div>
                        <div class="ms-2 mx-4">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal" onclick="populateEditForm(' . $t_id . ', \'' . $t_title . '\', \'' . $t_desc . '\')">Edit</button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick="populateDeleteForm(' . $t_id . ')">Delete</button>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-info like-btn" data-type="thread" data-id="' . $t_id . '">
                                <i class="bi ' . ($liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up') . '"></i>
                            </button>
                            <span id="like-count-' . $t_id . '">' . $likes . '</span>
                        </div>
                    </div>';
            }
            if ($noResult) {
                echo '<div class="jumbotron jumbotron-fluid">
                        <div class="container1 mx-4">
                            <p class="lead">You have not asked any questions in this category.</p>
                        </div>
                    </div>';
            }
            echo '</div>';
        } else {
            echo '<div class="container">
                    <h1 class="py-2">Post a Question</h1>
                    <p class="lead display-5">You are not logged in. Please Login to be able to post questions or see your questions.</p>
                </div>';
        }
        ?>
        
        <div class="container" id="">
            <h1 class="py-2">Browse Questions</h1>
            <?php
            $cat_id = $_GET['catid'];
            $sql = "SELECT * FROM forum.threads WHERE t_c_id = $cat_id ORDER BY $order_by";
            $result = selectsql($sql);
            $noResult = true;
            foreach ($result as $row) {
                $noResult = false;
                $t_id = $row['t_id'];
                $t_title = $row['t_title'];
                $t_desc = $row['t_desc'];
                $thread_time = $row['created_at'];
                $t_u_id = $row['t_u_id'];
                $likes = $row['likes'];
                $is_edited = $row['is_edited'];
        
                $sql2 = "SELECT u_name, u_role FROM forum.users WHERE u_id = '$t_u_id'";
                $row2 = selectsql($sql2);
                $liked = false;
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                    $user_id = $_SESSION['u_id'];
                    $sql3 = "SELECT * FROM forum.likes WHERE user_id = $user_id AND thread_id = $t_id";
                    $like_result = selectsql($sql3);
                    $liked = !empty($like_result);
                }
                echo '<div class="media my-3">
                        <img class="mr-3" src="img/user_default.png" width="50px" alt="Media Img">
                        <div class="media-body">
                            <h5 class="font-weight-bold my-0">' . $row2[0]['u_name'] . '</h5>
                            <h5 class="mt-8"><a class="text-dark" href="threads.php?threadid=' . $t_id . '">' . $t_title . ($is_edited ? ' (edited)' : '') . '</a></h5>
                            <p>' . $thread_time . '</p>
                        </div>';
                if ($logged_in_user_role == 'admin') {
                    echo '<div class="ms-2 mx-4">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal" onclick="populateEditForm(' . $t_id . ', \'' . $t_title . '\', \'' . $t_desc . '\')">Edit</button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick="populateDeleteForm(' . $t_id . ')">Delete</button>
                        </div>';
                }
                echo '<div>
                        <button class="btn btn-sm btn-info like-btn" data-type="thread" data-id="' . $t_id . '">
                            <i class="bi ' . ($liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up') . '"></i>
                        </button>
                        <span id="like-count-' . $t_id . '">' . $likes . '</span>
                    </div>
                </div>';
            }
            if ($noResult) {
                echo '<div class="jumbotron jumbotron-fluid">
                        <div class="container1 mx-4">
                            <p class="lead">No questions have been posted in this category yet. Be the first one to ask a question.</p>
                        </div>
                    </div>';
            }
            ?>
        </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="threadlist.php?catid=<?php echo $cat_id; ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Your Question</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_t_title">Question Title</label>
                            <input type="text" class="form-control" id="edit_t_title" name="edit_t_title">
                        </div>
                        <div class="form-group">
                            <label for="edit_t_desc">Elaborate Your Question</label>
                            <textarea class="form-control" id="edit_t_desc" name="edit_t_desc" rows="3"></textarea>
                        </div>
                        <input type="hidden" id="edit_thread_id" name="edit_thread_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_thread" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="threadlist.php?catid=<?php echo $cat_id; ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Your Question</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this question?</p>
                        <input type="hidden" id="delete_thread_id" name="delete_thread_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="delete_thread" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadThreads(sort) {
            window.location.href = 'threadlist.php?catid=<?php echo $cat_id; ?>&sort=' + sort;
        }

        function populateEditForm(id, title, desc) {
            document.getElementById('edit_thread_id').value = id;
            document.getElementById('edit_t_title').value = title;
            document.getElementById('edit_t_desc').value = desc;
        }

        function populateDeleteForm(id) {
            document.getElementById('delete_thread_id').value = id;
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const threadId = this.getAttribute('data-id');
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'like.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status == 200) {
                        const response = JSON.parse(this.responseText);
                        if (response.status == 'success') {
                            // Update all like buttons and like counts for the same thread
                            document.querySelectorAll(`.like-btn[data-id='${threadId}']`).forEach(btn => {
                                const likeCountElem = document.getElementById('like-count-' + threadId);
                                likeCountElem.textContent = response.likes;

                                const iconElem = btn.querySelector('i');
                                if (response.action === 'like') {
                                    iconElem.classList.remove('bi-hand-thumbs-up');
                                    iconElem.classList.add('bi-hand-thumbs-up-fill');
                                } else if (response.action === 'unlike') {
                                    iconElem.classList.remove('bi-hand-thumbs-up-fill');
                                    iconElem.classList.add('bi-hand-thumbs-up');
                                }
                            });
                        } else if (response.status == 'redirect') {
                            window.location.href = 'index.php?alert=' + response.message;
                        }
                    }
                };
                xhr.send('thread_id=' + threadId);
            });
        });
    });
    </script>

    <?php include 'partials/footer.php'; ?>
</body>
</html>

           
