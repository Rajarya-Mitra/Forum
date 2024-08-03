<?php
include 'partials/header.php';
require_once 'partials/dbconnect.php'; // Use require_once

/* if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
} */

$user_id = $_SESSION['u_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Check if email already exists
    $email_check_sql = "SELECT * FROM users WHERE u_email = :email AND u_id != :user_id";
    $email_check_result = selectsql($email_check_sql, ['email' => $email, 'user_id' => $user_id]);

    if (count($email_check_result) > 0) {
        echo '<div class="alert alert-danger" role="alert">Email already exists!</div>';
    } else {
        // Handle profile photo upload
        $profile_photo_path = $user['u_profile_photo'];
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
            $target_dir = "uploads/profile_photos/";
            $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profile_photo_path = $target_file;
            } else {
                echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.</div>';
            }
        }

        $sql = "UPDATE users SET u_profile_photo = :profile_photo, u_name = :username, u_email = :email WHERE u_id = :user_id";
        $params = [
            'profile_photo' => $profile_photo_path,
            'username' => $username,
            'email' => $email,
            'user_id' => $user_id
        ];

        if (execsql($sql, $params)) {
            echo '<div class="alert alert-success" role="alert">Profile updated successfully!</div>';
            $_SESSION['u_email'] = $email; // Update session email
        } else {
            echo '<div class="alert alert-danger" role="alert">Sorry, there was an error updating your profile.</div>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password != $confirm_new_password) {
        echo '<div class="alert alert-danger" role="alert">New passwords do not match!</div>';
    } else {
        $sql = "SELECT u_password FROM users WHERE u_id = :user_id";
        $result = selectsql($sql, ['user_id' => $user_id]);
        $user = $result[0];

        if (sha1($old_password) === $user['u_password']) {
            $new_password_hashed = sha1($new_password);
            $sql = "UPDATE users SET u_password = :new_password WHERE u_id = :user_id";
            $params = ['new_password' => $new_password_hashed, 'user_id' => $user_id];

            if (execsql($sql, $params)) {
                echo '<div class="alert alert-success" role="alert">Password changed successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Sorry, there was an error changing your password.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Old password is incorrect!</div>';
        }
    }
}

$sql = "SELECT * FROM users WHERE u_id = :user_id";
$result = selectsql($sql, ['user_id' => $user_id]);
$user = $result[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | weConnect</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Your Profile</h2>
        <div class="row">
            <div class="col-md-4">
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <img src="<?php echo $user['u_profile_photo']; ?>" class="img-thumbnail" alt="Profile Photo">
                    <div class="form-group">
                        <label for="profile_photo">Change Profile Photo</label>
                        <input type="file" class="form-control-file" name="profile_photo" id="profile_photo">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            <div class="col-md-8">
                <form action="profile.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo $user['u_name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo $user['u_email']; ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
                <button class="btn btn-warning mt-3" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="profile.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="old_password">Old Password</label>
                            <input type="password" class="form-control" name="old_password" id="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" name="new_password" id="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_new_password">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <?php include 'partials/footer.php';?>
</body>
</html>
