<!doctype html>
<html lang='en'>
    <?php include 'dbconnect.php'; ?>
    <?php include 'header.php'; ?>  
    <body>
        
        <?php include 'menu.php'; ?>  
<?php  
 $i=0;
$sql ="select * from forum.categories";
$query_data=mysql_query($sql,$conn);
while($result=mysql_fetch_array($query_data,MYSQL_ASSOC)){
   $i++;
   $c_id=$result['c_id'];
    $c_name=$result['c_name'];
    $c_desc=$result['c_description'];
    $created_at=date('d-M-Y , H:i:s' , strtotime($result['created_at']));
    if($i==1){ ?>
    <table class="table table-strip table-hover">
        <tr>
        <th class="p-0 text-center text-bold text-white bg-success border border-danger"> Sl</th>
        <th class="p-0 text-center text-bold text-white bg-success border border-danger"> Cat Id.</th>
        <th class="p-0 text-center text-bold text-white bg-success border border-danger"> Cat Name</th>
        <th class="p-0 text-center text-bold text-white bg-success border border-danger">Discription</th>
        <th class="p-0 text-center text-bold text-white bg-success border border-danger"> Generated</th>
    </tr>
<?php }
?>
<tr>
        <td class="p-0  border border-danger"><?= $i; ?></td>
        <td class="p-0  border border-danger"><?= $c_id; ?></td>
        <td class="p-0  border border-danger"><?= $c_name; ?></td>
        <td class="p-0  border border-danger"><?= $c_desc; ?></td>
        <td class="p-0 border border-danger"><?= $created_at; ?></td>
    </tr>
<?php
} 
?>
    </body>
    <?php include 'partials/footer.php';?>
</html>