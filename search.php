<!doctype html>
<html lang='en'>
    <?php include 'partials/dbconnect.php'; ?>
    <head>
    <?php include 'partials/header.php'; ?>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <style>
            .container{
                min-height: 81.6vh;
            }
        </style>
    </head>
    <body>
        <div class="container my-3">
        <h1>Search Results for <em>"<?php echo htmlspecialchars($_GET['search']); ?>"</em></h1>
            <?php
             $query = htmlspecialchars($_GET["search"]);
            //$sql = "SELECT * FROM forum.threads WHERE MATCH (t_title, t_desc) AGAINST ('$query')";
            $sql = "SELECT * FROM forum.threads WHERE lower(t_title) LIKE '%".strtolower($query). "%' OR lower(t_desc) LIKE '%" .strtolower($query)."%'";
            $result = selectsql($sql);
            $noResult=true;
            foreach($result as $row){
                $noResult=false;
                $t_id = $row['t_id'];
                $t_title = $row['t_title'];
                $t_desc = $row['t_desc'];
                $url = "threads.php?threadid=".$t_id;
                echo '<div class="result text-justify py-3">
                    <h3 class="py-2"><a href="'.$url.'" class="text-dark">'.$t_title.'</a></h3>
                    <p>'.$t_desc.'</p>
                </div>';
            }
            if($noResult==true){
                echo '<div class="jumbotron jumbotron-fluid">
                            <div class="container1 mx-4">
                                <h1 class="display-5">No Results Found!</h1>
                                <p class="lead">
                                    Suggetions: <ul>
                                    <li>Make sure all the words are spelled correctly.</li>
                                    <li>Try different Keywords.</li>
                                    <li>Try more general Keywords.</li></ul>
                                </p>
                            </div>
                        </div>';
                }
            ?>
        </div>
    </body>
    <?php include 'partials/footer.php';?>
</html>