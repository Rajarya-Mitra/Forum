<!doctype html>
<html lang='en'>
<?php include '/partials/dbconnect.php'; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>weConnect</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/bootstrap-icons.css">
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
    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
        <?php echo htmlspecialchars($_GET['alert']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="img\businessmen-making-handshake1.jpg" alt="First Slide" style="width:500px;height:50vh;">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="img/young-programmer-working-software.jpg" alt="Second Slide" style="width:500px;height:50vh;">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="img/business-meeting-room.jpg" alt="Third Slide" style="width:500px;height:50vh;">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container my-4">
        <div class="jumbotron">
            <h1 class="display-4">Welcome to weConnect</h1>
            <p class="lead">An online community Forum to discuss and share your ideas.</p>
            <hr class="my-4">
            <p>Explore the forums by categories and join the discussions.</p>
        </div>
    </div>

    <div class="container my-4">
        <h1 class="py-2">Browse Sections</h1>
        <div class="row">
            <?php
            $sql = "SELECT * FROM forum.categories";
            $result = selectsql($sql);
            foreach ($result as $row) {
                $cat_id = $row['c_id'];
                $cat_name = $row['c_name'];
                $cat_desc = $row['c_description'];
                echo '<div class="col-md-4 my-2">
                        <div class="card category-card" style="background-color:lavender">
                            <div class="card-body">
                                <h5 class="card-title"><a href="threadlist.php?catid=' . $cat_id . '?sort=newest">' . $cat_name . '</a></h5>
                                <p class="card-text">' . substr($cat_desc, 0, 100) . '...</p>
                                <a href="threadlist.php?catid=' . $cat_id . '" class="btn btn-primary">View Threads</a>
                            </div>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>

    <div class="container">
        <h1 class="py-2">Browse Threads</h1>
        <div class="btn-group mb-3" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-primary mr-2" onclick="loadThreads('newest')">Newest</button>
            <button type="button" class="btn btn-secondary" onclick="loadThreads('popular')">Popular</button>
            <button type="button" class="btn btn-info mx-2" onclick="loadThreads('oldest')">Oldest</button>
        </div>
        <div id="threads-container">
            <!-- Threads will be loaded here -->
        </div>
    </div>

    <script>
        function loadThreads(sort) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `get_threads_index.php?sort=${sort}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('threads-container').innerHTML = xhr.responseText;
                    // Re-initialize like buttons
                    document.querySelectorAll('.like-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const id = this.dataset.id;

                            fetch('like.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `thread_id=${id}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'redirect') {
                                    alert(data.message);
                                    window.location.href = 'index.php?alert=' + encodeURIComponent(data.message);
                                } else if (data.status === 'success') {
                                    const likeCountElement = document.getElementById(`like-count-${id}`);
                                    likeCountElement.textContent = data.likes;
                                    if (data.action === 'like') {
                                        this.querySelector('i').classList.remove('bi-hand-thumbs-up');
                                        this.querySelector('i').classList.add('bi-hand-thumbs-up-fill');
                                    } else {
                                        this.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                                        this.querySelector('i').classList.add('bi-hand-thumbs-up');
                                    }
                                }
                            })
                            .catch(error => console.error('Error:', error));
                        });
                    });
                }
            };
            xhr.send();
        }

        // Load newest threads by default
        loadThreads('newest');
    </script>

    <script src="like.js"></script>
</body>
<?php include 'partials/footer.php';?>
</html>
