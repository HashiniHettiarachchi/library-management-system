<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Delete book record if requested
if (isset($_GET['delete_book_id'])) {
    $delete_book_id = $_GET['delete_book_id'];
    $delete_stmt = $conn->prepare("DELETE FROM book WHERE book_id = ?");
    $delete_stmt->bind_param("s", $delete_book_id);
    if ($delete_stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Book deleted successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting book: ' . $conn->error . '</div>';
    }
    $delete_stmt->close();
}

// Fetch book records
$result = $conn->query("SELECT * FROM book");

if (!$result) {
    die("Error executing the query: " . $conn->error);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Books</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional CSS styles if needed */
        /* CSS styles from the previous code remain unchanged */
    </style>
</head>
<body>
    <div class="container">
        <h1>Book List</h1>
        <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Book ID</th>
                    <th scope="col">Book Name</th>
                    <th scope="col">Category ID</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['book_id']; ?></td>
                    <td><?php echo $row['book_name']; ?></td>
                    <td><?php echo $row['category_id']; ?></td>
                    <td>
                        <a href="update_book.php?book_id=<?php echo $row['book_id']; ?>" class="btn btn-primary">Update</a>
                        <a href="?delete_book_id=<?php echo $row['book_id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No books found.</p>
        <?php endif; ?>
        <a href="src/index.html" class="btn btn-primary">Back to Admin Panel</a>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
