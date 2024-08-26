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

    <?php if (isset($_GET['alert'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['alert']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <?php
        $thread_id = $_GET['threadid'];
        $sql = "SELECT * FROM forum.threads WHERE t_id = $thread_id";
        $result = selectsql($sql);
        $noResult = true;
        foreach ($result as $row) {
            $noResult = false;
            $t_id = $row['t_id'];
            $t_title = $row['t_title'];
            $t_desc = $row['t_desc'];
            $thread_user_id = $row['t_u_id'];

            $sql = "SELECT u_name FROM forum.users WHERE u_id = $thread_user_id";
            $row = selectsql($sql);
        }
        $showAlert = false;

        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $order_by = 'created_at DESC';
        if ($sort == 'oldest') {
            $order_by = 'created_at ASC';
        } elseif ($sort == 'popular') {
            $order_by = 'likes DESC';
        }

        // Handle Post Comment
        if (isset($_POST['comment_content'])) {
            // Sanitize input
            $comment_content = htmlspecialchars($_POST['comment_content']);
            $u_id = $_POST['u_id'];
        
            // Prepare the SQL statement with placeholders
            $sql = "INSERT INTO forum.comments (comment_content, t_id, comment_by, created_at) 
                    VALUES (:comment_content, :t_id, :u_id, CURRENT_TIMESTAMP)";
            
            // Execute the SQL statement with the provided data
            $params = [
                ':comment_content' => $comment_content,
                ':t_id' => $t_id,
                ':u_id' => $u_id
            ];
        
            // Assuming execsql is a function that handles SQL execution
            $result = execsql($sql, $params);
        
            // Check if the query was successful
            if ($result) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your comment has been added.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> There was an issue adding your comment. Please try again.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }
        }

        // Handle Edit Comment
        if (isset($_POST['edit_comment'])) {
            $edit_comment_id = (int)$_POST['edit_comment_id']; // Ensure this is an integer
            $edit_comment_content = htmlspecialchars($_POST['edit_comment_content']);
            
            $sql = "UPDATE forum.comments SET comment_content = :comment_content, is_edited = TRUE WHERE comment_id = :comment_id";
            $params = [
                ':comment_content' => $edit_comment_content,
                ':comment_id' => $edit_comment_id
            ];
            
            $result = execsql($sql, $params);
            $showAlert = true;
            if ($showAlert) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your comment has been updated.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }

        // Handle Delete Comment
        if (isset($_POST['delete_comment'])) {
            $comment_id = (int)$_POST['delete_comment_id']; // Ensure this is an integer
            
            $sql = "DELETE FROM forum.comments WHERE comment_id = :comment_id";
            $params = [
                ':comment_id' => $comment_id
            ];
            
            $result = execsql($sql, $params);
            $showAlert = true;
            if ($showAlert) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Your comment has been deleted.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    ?>

    <div class="container my-4">
        <div class="jumbotron">
            <h1 class="mb-3"><?php echo $t_title; ?></h1>
            <p class="lead"><?php echo $t_desc; ?></p>
            <hr class="my-4">
            <p>This is a peer-to-peer forum. Spam/Advertising/Self-promote in the forum is not allowed. Do not post copyright-infringing material. Do not post "offensive" posts, links or images. Do not cross post questions. Remain respectful of other members at all times.</p>
            <p>Posted by: <b><em><?php echo $row[0]['u_name']; ?></em></b></p>
        </div>
    </div>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <div class="container">
            <h1 class="py-2">Start a Discussion</h1>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                <div class="form-group">
                    <label for="comment_content">Type your Comment</label>
                    <textarea class="form-control" id="comment_content" name="comment_content" rows="3"></textarea>
                    <input type="hidden" name="u_id" value="<?php echo $_SESSION['u_id']; ?>">
                </div>
                <button type="submit" class="btn btn-success mt-2 mb-5">Post</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="btn-group mb-3" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-primary mr-2" onclick="loadThreads('newest')">Newest</button>
            <button type="button" class="btn btn-secondary" onclick="loadThreads('popular')">Popular</button>
            <button type="button" class="btn btn-info mx-2" onclick="loadThreads('oldest')">Oldest</button>
        </div>
    </div>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <div class="container mb-5">
            <h1>Your Comments</h1>
            <div id="your-comments-container">
                <!-- Your comments will be loaded here via AJAX -->
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <h1 class="py-2">Start a Discussion</h1>
            <p class="lead display-5">You are not logged in. Please Login to be able to comment or see your comments on this post.</p>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Discussions</h1>
        <div id="discussion-comments-container">
            <!-- Discussion comments will be loaded here via AJAX -->
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <input type="hidden" name="edit_comment_id" id="edit_comment_id">
                        <div class="form-group">
                            <label for="edit_comment_content">Comment Content</label>
                            <textarea class="form-control" id="edit_comment_content" name="edit_comment_content" rows="3"></textarea>
                        </div>
                        <button type="submit" name="edit_comment" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <input type="hidden" name="delete_comment_id" id="delete_comment_id">
                        <p>Are you sure you want to delete this comment?</p>
                        <button type="submit" name="delete_comment" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function populateEditForm(id, content) {
        document.getElementById('edit_comment_id').value = id;
        document.getElementById('edit_comment_content').value = content;
    }

    function populateDeleteForm(id) {
        document.getElementById('delete_comment_id').value = id;
    }

    function loadThreads(sort) {
        // Fetch and update the discussion comments
        var xhr = new XMLHttpRequest();
        xhr.open('GET', `load_comments.php?threadid=<?php echo $t_id; ?>&sort=${sort}&role=<?php echo $logged_in_user_role; ?>`, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('discussion-comments-container').innerHTML = xhr.responseText;
                addLikeButtonListeners();
            }
        };
        xhr.send();

        // Fetch and update your comments if logged in
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
            var xhr2 = new XMLHttpRequest();
            xhr2.open('GET', 'load_user_comments.php?userid=<?php echo $_SESSION['u_id']; ?>&threadid=<?php echo $t_id; ?>&sort=' + sort, true);
            xhr2.onreadystatechange = function() {
                if (xhr2.readyState == 4 && xhr2.status == 200) {
                    document.getElementById('your-comments-container').innerHTML = xhr2.responseText;
                    addLikeButtonListeners();
                }
            };
            xhr2.send();
        <?php endif; ?>
    }

    // Initially load comments with the default sort order
    loadThreads('newest');

    function addLikeButtonListeners() {
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                var commentId = this.getAttribute('data-id');
                var action = this.querySelector('i').classList.contains('bi-hand-thumbs-up-fill') ? 'unlike' : 'like';
                updateLikeStatus(commentId, action, this);
            });
        });
    }

    function updateLikeStatus(commentId, action, button) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'like_comm.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var likeIcon = button.querySelector('i');
                    var likeCount = document.getElementById(`like-count-${commentId}`);

                    if (action === 'like') {
                        likeIcon.classList.remove('bi-hand-thumbs-up');
                        likeIcon.classList.add('bi-hand-thumbs-up-fill');
                    } else {
                        likeIcon.classList.remove('bi-hand-thumbs-up-fill');
                        likeIcon.classList.add('bi-hand-thumbs-up');
                    }

                    likeCount.textContent = response.likes;
                } else {
                    console.error('Failed to update like status');
                }
            }
        };
        xhr.send(`comment_id=${commentId}&action=${action}`);
    }
</script>   
</body>
<?php include 'partials/footer.php'; ?>
</html>