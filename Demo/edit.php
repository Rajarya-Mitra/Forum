<!doctype html>
<html lang='en'>
    <?php include 'dbconnect.php'; ?>
    <?php include 'header.php'; ?>  
    <body>
           <?php include 'view.php';?>
           <form method="post" action="">
            <?php 
if(isset($_POST['edit'])){
    $c_id=$_POST['c_id'];
    $c_name=$_POST['c_name'];
    $c_desc=$_POST['c_description'];
   
    $edit_sql="UPDATE categories SET c_id='$c_id',c_name='$c_name',c_description='$c_desc' WHERE c_id='$c_id' ";
    ///echo $insert_sql;
    if(mysql_query($edit_sql,$conn)) { ?>
    <div class="alert alert-success"> Data Updated</div>
<?php } else { ?>
    <div class="alert alert-danger"> Data Not Updated</div>
    <?php }
}
?>
this is edit page
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
<input type="submit" name="edit" value= "Edit detail" >
</form>
    </body>
    <?php include 'partials/footer.php';?>
</html>