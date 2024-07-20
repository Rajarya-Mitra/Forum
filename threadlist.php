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
            $cat_id = $_GET['catid'];
            $sql = "SELECT * FROM forum.categories WHERE c_id = $cat_id";
            //$result = mysql_query($sql,$conn);
            $result = selectsql($sql);
            //while($row = mysql_fetch_assoc($result)){
            foreach($result as $row){
                $catname = $row['c_name'];
                $catdesc = $row['c_description'];
            }
        ?>

        <?php
        
            $showAlert = false; 
            if(isset($_POST['t_title'])){
                //insert thread into db
                $t_title = $_POST['t_title'];
                $t_title = str_replace("<", "&lt;", $t_title);
                $t_title = str_replace(">", "&gt;", $t_title);
                $t_title = str_replace("'", "\'", $t_title);

                $t_desc = $_POST['t_desc'];
                $t_desc = str_replace("<", "&lt;", $t_desc);
                $t_desc = str_replace(">", "&gt;", $t_desc);
                $t_desc = str_replace("'", "\'", $t_desc);

                $u_id = $_POST['u_id'];
                $sql = "INSERT INTO `forum`.`threads` (`t_title`, `t_desc`, `t_c_id`, `t_u_id`, `created_at`) VALUES ('$t_title', '$t_desc', '$cat_id', '$u_id', CURRENT_TIMESTAMP)";
                $result=execsql($sql);
                //$result = $sth1->fetchAll(PDO::FETCH_ASSOC);
                if ($result)
                    $showAlert = true;
                if($showAlert){
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your question has been added! Please wait for someone to respond.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aia-hidden="true">&times;</span>
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
                <p>This a peer to peer forum. Spam / Advertising / Self-promote in the firum is not allowed. Do not post copyright-infringing material. Do not post "offensive" posts, links or images. Do not cross post questions. Remain respectful of other members at all times.</p>
                <p class="lead">
                    <a class="btn btn-success btn-lg" href="#" role="button">Learn More</a>
                </p>
            </div>
        </div>

        <?php
            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            echo'<div class="container">
                    <h1 class="py-2">Post a Question</h1>
                    <form action = "' . $_SERVER["REQUEST_URI"] . '" method="post">
                        <div class="form-group">
                            <label for="t_title">Question Title</label>
                            <input type="text" class="form-control" id="t_title" name="t_title" aria-describedby="questionlHelp">
                            <small id="questionHelp" class="form-text text-muted">Keep your title as short and crisp as possible.</small>
                        </div>
                        <input type="hidden" name="u_id" value='.$_SESSION["u_id"].'">
                        <div class="form-group">
                            <label for="t_desc">Elaborate your Question</label>
                            <textarea class="form-control" id="t_desc" name="t_desc" rows="3"></textarea>
                            
                        </div>
                        <button type="submit" class="btn btn-success mt-2 mb-5">Submit</button>
                    </form>
                </div>';
            }
            else{
                echo '<div class="container">
                        <h1 class="py-2">Post a Question</h1>
                        <p class="lead display-5" >You are not logged in. Please Login to be able to post questions.</p>
                      </div>';
            }
        ?>

        <div class="container" id="ques">
            <h1 class="py-2">Browse Questions</h1>
            <?php
            $cat_id = $_GET['catid'];
            $sql = "SELECT * FROM forum.threads WHERE t_c_id = $cat_id";
            /*$result = mysql_query($sql,$conn);
            while($row = mysql_fetch_assoc($result)){
                $t_title = $row['t_title'];
                $t_desc = $row['t_desc'];
                $sth2 = $conn->prepare($sql);
                $sth2->execute(['category_id' => $cat_id]);
                $result = $sth2->fetchAll(PDO::FETCH_ASSOC); */
                $result=selectsql($sql);
                $noResult = true;
                foreach($result as $row){
                    $noResult = false;
                    $t_id = $row['t_id'];
                    $t_title = $row['t_title'];
                    $t_desc = $row['t_desc'];
                    $thread_time = $row['created_at'];
                    $t_u_id = $row['t_u_id'];

                    $sql2 = "SELECT u_name FROM forum.users WHERE u_id = '$t_u_id'";
                    /* $sth2 = $conn->prepare($sql2);
                    $sth2->execute(['thread_user_id' => $t_u_id]);
                    $row2 = $sth2->fetchAll(PDO::FETCH_ASSOC); */
                    $row2 = selectsql($sql2);
                    echo '<div class="media my-3">
                        <img class="mr-3" src="img\MAA1.JPG" width="50px" alt="Media Img">
                        <div class="media-body">
                            <h5 class="font-weight-bold my-0">'. $row2[0]['u_name'].' </h5>
                            <h5 class="mt-8"><a class="text-dark" href="threads.php?threadid=' . $t_id . '">'. $t_title .'</a></h5>
                            <p>' . $thread_time . '</p>
                        </div>
                    </div>';
            }
            if($noResult){
                echo '<div class="jumbotron jumbotron-fluid">
                        <div class="container">
                            <h1 class="display-5">No Questions Found</h1>
                            <p class="lead"> Be the first person to ask a question!</p>
                        </div>
                      </div>';
            }
            ?>
        </div>
    </body>
    <?php include 'partials/footer.php';?>
</html>