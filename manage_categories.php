<?php
include 'partials/dbconnect.php';
include 'partials/header.php';


// Handle adding a new category
if (isset($_POST['add_category'])) {
    $c_name = htmlspecialchars($_POST['c_name']);
    $c_description = htmlspecialchars($_POST['c_description']);
    $sql = "INSERT INTO forum.categories (c_name, c_description) VALUES ('$c_name', '$c_description')";
    execsql($sql);
}

// Handle editing a category
if (isset($_POST['edit_category'])) {
    $c_id = $_POST['edit_c_id'];
    $c_name = htmlspecialchars($_POST['edit_c_name']);
    $c_description = htmlspecialchars($_POST['edit_c_description']);
    $sql = "UPDATE forum.categories SET c_name='$c_name', c_description='$c_description' WHERE c_id=$c_id";
    execsql($sql);
}

// Handle deleting a category
if (isset($_POST['delete_category'])) {
    $c_id = $_POST['delete_c_id'];
    $sql = "DELETE FROM forum.categories WHERE c_id=$c_id";
    execsql($sql);
}

// Fetch all categories
$sql = "SELECT * FROM forum.categories";
$categories = selectsql($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | weConnect</title>
</head>
<body>
    <div class="container my-4">
        <h2>Manage Categories</h2>
        <form action="manage_categories.php" method="post">
            <div class="form-group">
                <label for="c_name">Category Name</label>
                <input type="text" class="form-control" id="c_name" name="c_name" required>
            </div>
            <div class="form-group">
                <label for="c_description">Category Description</label>
                <textarea class="form-control" id="c_description" name="c_description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
        </form>

        <h3 class="mt-4">Existing Categories</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td style="text-align:justify"><?php echo htmlspecialchars($category['c_name']); ?></td>
                        <td style="text-align:justify"><?php echo htmlspecialchars($category['c_description']); ?></td>
                        <td>
                            <div class="ms-2">
                            <button class="btn btn-sm btn-primary mb-2" style="width:10vh;" data-toggle="modal" data-target="#editModal" onclick="populateEditForm(<?php echo $category['c_id']; ?>, '<?php echo addslashes(htmlspecialchars($category['c_name'])); ?>', '<?php echo addslashes(htmlspecialchars($category['c_description'])); ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" style="width:10vh;" data-toggle="modal" data-target="#deleteModal" onclick="populateDeleteForm(<?php echo $category['c_id']; ?>)">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="manage_categories.php" method="post">
                        <input type="hidden" name="edit_c_id" id="edit_c_id">
                        <div class="form-group">
                            <label for="edit_c_name">Category Name</label>
                            <input type="text" class="form-control" id="edit_c_name" name="edit_c_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_c_description">Category Description</label>
                            <textarea class="form-control" id="edit_c_description" name="edit_c_description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="edit_category">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="manage_categories.php" method="post">
                        <input type="hidden" name="delete_c_id" id="delete_c_id">
                        <p>Are you sure you want to delete this category?</p>
                        <button type="submit" class="btn btn-danger" name="delete_category">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function populateEditForm(id, name, desc) {
            document.getElementById('edit_c_id').value = id;
            document.getElementById('edit_c_name').value = name;
            document.getElementById('edit_c_description').value = desc;
        }

        function populateDeleteForm(id) {
            document.getElementById('delete_c_id').value = id;
        }
    </script>
    <?php include 'partials/footer.php'; ?>
</body>
</html>
