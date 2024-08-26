<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php
include 'partials/header.php';
require_once 'partials/dbconnect.php'; 

$user_id = $_SESSION['u_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Check if email already exists
    $email_check_sql = "SELECT * FROM users WHERE u_email = :email AND u_id != :user_id";
    $email_check_result = selectsql($email_check_sql, ['email' => $email, 'user_id' => $user_id]);

    if (count($email_check_result) > 0) {
        echo '<div class="alert alert-danger" role="alert">Email already exists!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    } else {
        $sql = "UPDATE users SET u_name = :username, u_email = :email WHERE u_id = :user_id";
        $params = [
            'username' => $username,
            'email' => $email,
            'user_id' => $user_id
        ];

        if (execsql($sql, $params)) {
            echo '<div class="alert alert-success" role="alert">Profile updated successfully!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
            $_SESSION['u_email'] = $email; 
        } else {
            echo '<div class="alert alert-danger" role="alert">Sorry, there was an error updating your profile.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
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
                echo '<div class="alert alert-success" role="alert">Password changed successfully!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Sorry, there was an error changing your password.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert"><strong>Old password is incorrect!</strong> Try again...
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
    }
}

// Handle profile photo updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
        $target_dir = "img/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);

        // Ensure the file is an image
        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profile_photo_path = $target_file;
                $sql = "UPDATE users SET u_profile_photo = :profile_photo WHERE u_id = :user_id";
                $params = ['profile_photo' => $profile_photo_path, 'user_id' => $user_id];

                if (execsql($sql, $params)) {
                    echo '<div class="alert alert-success" role="alert">Profile photo updated successfully!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Sorry, there was an error updating your profile photo.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">File is not an image.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
    }
}

// Handle profile photo deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_profile_photo'])) {
    $sql = "UPDATE users SET u_profile_photo = NULL WHERE u_id = :user_id";
    $params = ['user_id' => $user_id];

    if (execsql($sql, $params)) {
        echo '<div class="alert alert-success" role="alert">Profile photo deleted successfully!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Sorry, there was an error deleting your profile photo.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
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
    <style>
        #minheight
        {
        min-height: 406px;
        }
        .profile-picture {
            width: 35vh;
            height: 35vh;
            margin: auto;
            position: relative;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-picture .overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: opacity 0.3s;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-picture:hover .overlay {
            opacity: 1;
        }

        .profile-picture .overlay i {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

    </style>
</head>
<body>    
    <h1 class="my-4 display-4" style="text-align:center"><b>Your Profile</b></h1>
    <div class="row my-5 mx-5" id="minheight">
    <div class="col-md-4">
        <div class="profile-picture position-relative text-center">
            <img src="<?php echo !empty($user['u_profile_photo']) ? $user['u_profile_photo'] : 'img/user_default.png'; ?>" alt="Profile Picture" class="img-thumbnail rounded-circle">
            <div class="overlay d-flex align-items-center justify-content-center">
                <i class="fas fa-edit mx-2" data-toggle="modal" data-target="#editProfilePhotoModal"></i>
                <i class="fas fa-trash-alt mx-2" data-toggle="modal" data-target="#deleteProfilePhotoModal"></i>
            </div>
        </div>
    </div>
        <div class="col-md-8">
            <p>Account created at <b><?php echo $user['created_at']; ?></b></p>
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
                <button type="button" class="btn btn-secondary" id="revertChanges">Revert Changes</button>
            </form>
            <button class="btn btn-warning mt-3" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
        </div>
    </div>   

    <!-- Edit Profile Photo Modal -->
    <div class="modal fade" id="editProfilePhotoModal" tabindex="-1" role="dialog" aria-labelledby="editProfilePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfilePhotoModalLabel">Edit Profile Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="profile_photo">Choose new profile photo</label>
                            <input type="file" class="form-control-file" name="profile_photo" id="profile_photo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_profile_photo" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Profile Photo Modal -->
    <div class="modal fade" id="deleteProfilePhotoModal" tabindex="-1" role="dialog" aria-labelledby="deleteProfilePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProfilePhotoModalLabel">Delete Profile Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="profile.php" method="post">
                    <div class="modal-body">
                        Are you sure you want to delete your profile photo?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="delete_profile_photo" class="btn btn-danger">Delete</button>
                    </div>
                </form>
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
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        document.getElementById('revertChanges').onclick = function() {
            location.reload();
        };
    </script>
    <?php include 'partials/footer.php';?>
</body>
</html>
