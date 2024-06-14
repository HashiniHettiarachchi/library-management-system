<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $conn->prepare("SELECT * FROM bookcategory WHERE category_id = ?");
    $stmt->bind_param("s", $category_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
    } else {
        die("Error executing the query: " . $conn->error);
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_category_id = $_POST['old_category_id'];
    $new_category_id = $_POST['new_category_id'];
    $category_Name = $_POST['category_Name'];
    $date_modified = date("Y-m-d H:i:s"); // Get the current date and time

    $update_stmt = $conn->prepare("UPDATE bookcategory SET category_id = ?, category_Name = ?, date_modified = ? WHERE category_id = ?");
    $update_stmt->bind_param("ssss", $new_category_id, $category_Name, $date_modified, $old_category_id);

    if ($update_stmt->execute()) {
        header("Location: view_categories.php?msg=Category+updated+successfully");
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating category: ' . $conn->error . '</div>';
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Category</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Update Category</h1>
        <form method="post">
            <input type="hidden" name="old_category_id" value="<?php echo $category['category_id']; ?>">
            <div class="form-group">
                <label for="new_category_id">Category ID:</label>
                <input type="text" class="form-control" id="new_category_id" name="new_category_id" value="<?php echo $category['category_id']; ?>">
            </div>
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" id="category_Name" name="category_Name" value="<?php echo $category['category_Name']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_categories.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
