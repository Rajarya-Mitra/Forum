<!doctype html>
<html lang='en'>
<?php include '/partials/dbconnect.php'; ?>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>DOO-Forums</title>
    </head>
    <body>
        <?php include 'partials/header.php';?>
        <?php
            $thread_id = $_GET['threadid'];
            $sql = "SELECT * FROM forum.threads WHERE t_id = $thread_id";
            //$result = mysql_query($sql,$conn);
            $result = selectsql($sql);
            $noResult=true;
            //var_dump($result);  
            //while($row = mysql_fetch_assoc($result)){
            foreach($result as $row){
                $noResult=false;
                $t_id = $row['t_id'];
                $t_title = $row['t_title'];
                $t_desc = $row['t_desc'];
                $thread_user_id = $row['t_u_id'];

                //Query the users table to find out the name of the original poster
                $sql = "SELECT u_name FROM forum.users WHERE u_id =  $thread_user_id";
                $row = selectsql($sql);
            }
        ?>

        <?php
            $showAlert = false;
            if(isset($_POST['comment_content'])){
                //insert thread into db
                $comm_content = $_POST['comment_content'];
                $comm_content = str_replace("<", "&lt;", $comm_content);
                $comm_content = str_replace(">", "&gt;", $comm_content);
                $comm_content = str_replace("'", "\';", $comm_content);
                $u_id = $_POST['u_id'];
                $sql = "INSERT INTO `forum`.`comments` (`comment_content`, `t_id`, `comment_by`, `created_at`) VALUES ('$comm_content', '$t_id', '$u_id', CURRENT_TIMESTAMP)";
                $result=execsql($sql);
                $showAlert = true;
                if($showAlert){
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your comment has been added.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aia-hidden="true">&times;</span>
                            </button>
                          </div>';
                }
            }
        ?>
            
        <div class="container my-4">
            <div class="jumbotron">
                <h1 class="mb-3"><?php echo $t_title;?></h1>
                <p class="lead"><?php echo $t_desc?></p>
                <hr class="my-4">
                <p>This a peer to peer forum. Spam / Advertising / Self-promote in the firum is not allowed. Do not post copyright-infringing material. Do not post "offensive" posts, links or images. Do not cross post questions. Remain respectful of other members at all times. </p>
                <p>Posted by: <b><em><?php echo $row[0]['u_name']; ?></em></b></p>
            </div>
        </div>
        <?php
            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
                echo'<div class="container">
                        <h1 class="py-2">Start a Discussion</h1>
                        <form action = "' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <div class="form-group">
                                <label for="comment_content">Type your Comment</label>
                                <textarea class="form-control" id="comment_content" name="comment_content" rows="3"></textarea>
                                <input type="hidden" name="u_id" value='.$_SESSION["u_id"].'">
                            </div>
                            <button type="submit" class="btn btn-success mt-2 mb-5">Post</button> 
                        </form>
                    </div>';
            }
            else{
                echo '<div class="container">
                        <h1 class="py-2">Start a Discussion</h1>
                        <p class="lead display-5" >You are not logged in. Please Login to be able to comment on this post.</p>
                      </div>';
            }
        ?>
        <div class="container" id="ques">
            <h1 class>Discussions</h1>
            <?php
            $t_id = $_GET['threadid'];
            $sql = "SELECT * FROM forum.comments WHERE t_id = $t_id";
                $sth = $conn->prepare($sql);
                $sth->execute();
                $result = $sth->fetchAll(PDO::FETCH_ASSOC);
                $noResult = true;
                foreach($result as $row){
                    $noResult = false;
                    $comm_id = $row['comment_id'];
                    $comm_content = $row['comment_content'];
                    $comm_time = $row['created_at'];
                    $comm_u_id = $row['comment_by'];
                    $sql2 = "SELECT u_name FROM forum.users WHERE u_id = $comm_u_id";
                    $row2 = selectsql($sql2);
                echo '<div class="media my-3">
                    <img class="mr-3" src="img\MAA1.JPG" width="50px" alt="Media Img">
                    <div class="media-body">
                        <p class="font-weight-bold my-0">'. $row2[0]['u_name'].' at ' . $comm_time . '</p>
                        <h5 class="mt-8"><a class="text-dark" href="threads.php?commentid=' . $comm_id . '"></a></h5>
                        ' . $comm_content . '
                    </div>
                </div>';
            }
            if($noResult){
                echo '<div class="jumbotron jumbotron-fluid">
                        <div class="container">
                            <h1 class="display-5">No Comments till now</h1>
                            <p class="lead"> Be the first person to post a comment!</p>
                        </div>
                      </div>';
            }
            ?>
        </div>
        
    </body>
    <?php include 'partials/footer.php';?>
</html>