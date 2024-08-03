<?php
// session_start();
include 'handleLogin.php';

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

<!-- Ensure Bootstrap CSS is loaded -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/project">weConnect</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/project">Home<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Categories
                </a>
                <div class="dropdown-menu" id="categoryDropdown" aria-labelledby="navbarDropdown">
                    <?php
                    $sql = "SELECT * FROM forum.categories LIMIT 5";
                    $result = selectsql($sql);
                    foreach ($result as $row) {
                        $cat_id = $row['c_id'];
                        $cat_name = $row['c_name'];
                        echo '<a class="dropdown-item" href="threadlist.php?catid=' . $cat_id . '">' . $cat_name . '</a>';
                    }
                    ?>
                    <div class="dropdown-menu"></div>
                    <a class="dropdown-item" id="viewMoreBtn" data-offset="5" href="#"><i>View More...</i></a>
                    <?php if ($logged_in_user_role == 'admin') : ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="manage_categories.php">Manage Categories</a>
                    <?php endif; ?>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" method="GET" action="search.php">
            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) : ?>
            <form class="form-inline my-2 my-lg-0">
                <p class="text-light my-0 mx-4">
                    Welcome <a href="profile.php?user_id=<?php echo $_SESSION['u_id']; ?>" class="text-white"><?php echo $_SESSION['u_email']; ?></a>
                </p>
                <a href="partials/logout.php" role="button" class="btn btn-outline-danger text-white border-white mx-1">Logout</a>
            </form>
        <?php else : ?>
            <div class="ms.2">
                <button class="btn btn-outline-success text-white border-white ml-4" data-toggle="modal" data-target="#loginModal">Login</button>
                <button class="btn btn-outline-success text-white border-white mx-1" data-toggle="modal" data-target="#signupModal">Signup</button>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Load Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('viewMoreBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default action of the anchor tag
            event.stopPropagation(); // Stop the event from bubbling up and closing the dropdown

            var btn = this;
            var offset = parseInt(btn.getAttribute('data-offset'));

            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/project/partials/load_more_categories.php?offset=' + offset, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log('Response received: ' + xhr.responseText);
                    var moreCategories = JSON.parse(xhr.responseText);
                    var dropdownMenu = btn.parentElement;

                    moreCategories.forEach(function(category) {
                        var categoryItem = document.createElement('a');
                        categoryItem.className = 'dropdown-item dynamic-category';
                        categoryItem.href = 'threadlist.php?catid=' + category.c_id;
                        categoryItem.textContent = category.c_name;
                        dropdownMenu.insertBefore(categoryItem, btn);
                    });

                    // Update the offset for the next batch of categories
                    btn.setAttribute('data-offset', offset + moreCategories.length);

                    // Hide the "View More" button if there are no more categories to load
                    if (moreCategories.length < 5) {
                        btn.style.display = 'none';
                    }

                    // Add the "View Less" button if it doesn't already exist
                    if (!document.getElementById('viewLessBtn')) {
                        var viewLessBtn = document.createElement('a');
                        viewLessBtn.className = 'dropdown-item';
                        viewLessBtn.id = 'viewLessBtn';
                        viewLessBtn.href = '#';
                        viewLessBtn.textContent = 'View Less...';
                        viewLessBtn.style.color = 'black'; // Set text color to blue
                        viewLessBtn.style.fontStyle = 'italic'; // Set text to italic
                        dropdownMenu.insertBefore(viewLessBtn, btn.nextSibling);

                        viewLessBtn.addEventListener('click', function(event) {
                            event.preventDefault();
                            event.stopPropagation();

                            // Remove the additional categories
                            var items = dropdownMenu.querySelectorAll('.dynamic-category');
                            items.forEach(function(item) {
                                dropdownMenu.removeChild(item);
                            });

                            // Reset the offset
                            btn.setAttribute('data-offset', 5);

                            // Show the "View More" button again
                            btn.style.display = 'block';

                            // Remove the "View Less" button
                            dropdownMenu.removeChild(viewLessBtn);
                        });
                    }
                }
            };
            console.log('Sending request to load more categories with offset: ' + offset);
            xhr.send();
        });
    });
</script>


