<?php
    include_once 'dbconnect.php';
    $showError = "false";
    $showAlert = false;
    if(isset($_POST['signupEmail'])){  
        $user_email = $_POST['signupEmail'];
        $user_pass = $_POST['signupPassword'];
        $user_cpass = $_POST['signupcPassword'];
        $username = $_POST['username'];

        $existSql = "SELECT * FROM forum.users WHERE u_email = '$user_email'";
        $numRows = selectsql($existSql);
        //var_dump($numRows);die;
        //$numRows = $result->fetchColumn();
        //$numRows = $sth->rowCount();
        if(count($numRows)>0){
            $showError = "Email is already in use";//var_dump($numRows);die;
        } else {
            if($user_pass == $user_cpass){
                // $hash = password_hash($user_pass, PASSWORD_DEFAULT);
                $user_pass = sha1($user_pass);
                $sql = "INSERT INTO `forum`.`users` (`u_email`, `u_password`, `u_name`, `created_at`) VALUES ('$user_email', '$user_pass','$username', CURRENT_TIMESTAMP)";
                /* $sth = $conn->prepare($sql);
                $result=$sth->execute(); */
                $result=execsql($sql);
                if($result){
                    $showAlert = true;
                    header("Location: /project/index.php?signupsuccess=true");
                    die;
                }
            } else{
                $showError = "Passwords do not match";
            }
        }
        header("Location: /project/index.php?signupsuccess=false&error=$showError");
    }
    if(isset($_GET['signupsuccess']) && $_GET['signupsuccess']=="true"){
        echo '<div class="alert alert-success alert-dismissible fade show my-0" role="alert">
                    <strong>Signup successful!</strong> You can now login into your account.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aia-hidden="true">&times;</span>
                        </button>
                </div>';
    }
    else if(isset($_GET['signupsuccess']) && $_GET['signupsuccess']=="false"){
        echo '<div class="alert alert-danger alert-dismissible fade show my-0" role="alert">
                       <strong>' . $_GET['error'] . '</strong> Try again.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aia-hidden="true">&times;</span>
                            </button>
                    </div>';
    }
    ?>

