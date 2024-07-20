<!doctype html>
<html lang='en'>
    <?php include 'dbconnect.php'; ?>
    <?php include 'header.php'; ?>  
    <body>
           <?php include 'menu.php'; ?>  
           <form method="post" action="">
            <?php 
if(isset($_POST['add'])){
    $c_id=$_POST['c_id'];
    $c_name=$_POST['c_name'];
    $c_desc=$_POST['c_description'];
   
    $insert_sql="INSERT INTO forum.categories VALUES ('$c_id','$c_name','$c_desc',now())";
    ///echo $insert_sql;
    if(mysql_query($insert_sql,$conn)) { ?>
    <div class="alert alert-success"> Data Save</div>
<?php } else { ?>
    <div class="alert alert-danger"> Data Not Save</div>
    <?php }

}
?>
this is add page
<table>
    <tr class="border border-dark">
        <td> Cust ID:</td>
        <td> <input name="c_id" class="form-control-range" required> </td>
</tr>
<tr class="border border-dark">
        <td> Cust Name:</td>
        <td> <input name="c_name" class="form-control-range" required> </td>
</tr>
<tr class="border border-dark">
        <td> Cust ID:</td>
        <td> <textarea name="c_description" style='resize:none' > </textarea></td>
</tr>
</table>
<br><br>
<input type="submit" name="add" value= "Add detail" >
</form>
    </body>
    <?php include 'partials/footer.php';?>
</html>