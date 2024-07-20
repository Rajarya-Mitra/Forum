<?php
session_start();
echo "Logging you out. Please wait...";
session_destroy();

header("Location: /project?logoutsuccess=true");

if(isset($_GET['logoutsuccess']) && $_GET['logoutsuccess']=="true"){
    echo '<div class="alert alert-success alert-dismissible fade show my-0" role="alert">
            <strong>Logout successful</strong> Thanks for visiting
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aia-hidden="true">&times;</span>
            </button>
        </div>';
}
?>