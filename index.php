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
        <!--Slider starts here-->
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="img\sun.jpg" alt="First Slide" style="width:500px;height:15rem;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="img\Planet.jpg" alt="Second Slide" style="width:500px;height:15rem;">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="img\Velvet.jpg" alt="Third Slide" style="width:500px;height:15rem;">
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

        <!--Category Container Starts here-->
        <div class="container-fluid text-center my-3">
            <h1>Welcome to DOO-Forums</h1>
        </div>
        <div class="container">
            <h2 class="text-center">Browse Categories</h2>
            <div class="row" id="ques">
                <?php 
                    //$i=0;
                    $sql = "SELECT * FROM forum.categories";
                    /*$result=mysql_query($sql,$conn);
                    while($row = mysql_fetch_assoc($result)){
                        $i++;
                        $cat_id = $row['c_id'];
                        $cat_name = $row['c_name'];
                        $cat_desc = $row['c_description'];*/
                    $result = selectsql($sql);
                    foreach($result as $row){
                        $cat_id = $row['c_id'];
                        $cat_name = $row['c_name'];
                        $cat_desc = $row['c_description'];
                        echo '<div class="col-md-4 my-3">
                                <div class="card" style="width:15rem;">
                                    <img src="img\Shiv-Pariva-Mobile-Wallpaper.gif" alt="Card Image cap">
                                    <div class="card-body">
                                        <h5 class="card-title"><a href="threadlist.php?catid=' . $cat_id . '">' . $cat_name . '</a></h5>
                                        <p class="card-text">' . substr($cat_desc, 0,70) . '...</p>
                                        <a href="threadlist.php?catid=' . $cat_id . '" class="btn btn-primary">View Threads</a>
                                    </div>
                                </div>
                             </div>';
                    }
                ?>
            </div>                         
        </div>
    </body>
    <?php include 'partials/footer.php';?>
</html>