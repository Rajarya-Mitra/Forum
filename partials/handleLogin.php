<?php
    include_once 'dbconnect.php';
    $showError = "false";
    if(isset($_POST['loginEmail'])){
        $user_email = $_POST['loginEmail'];
        $user_pass = $_POST['loginPassword'];

        $sql = "SELECT * FROM forum.users WHERE u_email = '$user_email'";
        /* $sth = $conn->prepare($sql);
        $sth->execute(['email' => $user_email]);
        $row = $sth->fetchAll(PDO::FETCH_ASSOC); */
        $row = selectsql($sql);
        $numRows = count($row);
        if($numRows == 1){         
            $user_pass = sha1($user_pass);
            if($user_pass == $row[0]['u_password']){
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['u_id'] = $row[0]['u_id'];
                $_SESSION['u_email'] = $user_email;
                $_SESSION['u_name'] = $user_name;
                //echo "logged in " . $user_email;
                header("Location: /project/index.php?loginsuccess=true");
                die;
            }
            else{
                $showError = "Passwords do not match";
                //echo "Passwords do not match";
            }
        }
        else{
            $showError = "Email not found";
            //echo "Email not found";
        }
        header("Location: /project/index.php?loginsuccess=false&error=$showError");
    }
    if(isset($_GET['loginsuccess']) && $_GET['loginsuccess']=="true"){
        echo '<div class="alert alert-success alert-dismissible fade show my-0" role="alert">
                    <strong>Login successful!</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aia-hidden="true">&times;</span>
                        </button>
                </div>';
    }
    else if(isset($_GET['loginsuccess']) && $_GET['loginsuccess']=="false"){
        echo '<div class="alert alert-danger alert-dismissible fade show my-0" role="alert">
                <strong>' . $_GET['error'] . '</strong> Try again.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aia-hidden="true">&times;</span>
                    </button>
            </div>';
    }
?>