<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch book details to pre-fill the form
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $stmt = $conn->prepare("SELECT * FROM book WHERE book_id = ?");
    $stmt->bind_param("s", $book_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
    } else {
        die("Error executing the query: " . $conn->error);
    }
    $stmt->close();
}

// Update book details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_book_id = $_POST['book_id'];
    $book_name = $_POST['book_name'];
    $category_id = $_POST['category_id'];

    $update_stmt = $conn->prepare("UPDATE book SET book_id = ?, book_name = ?, category_id = ? WHERE book_id = ?");
    $update_stmt->bind_param("ssss", $new_book_id, $book_name, $category_id, $book_id);

    if ($update_stmt->execute()) {
        header("Location: view_books.php?msg=Book+updated+successfully");
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating book: ' . $conn->error . '</div>';
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Book</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Update Book</h1>
        <form method="post">
            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <input type="text" class="form-control" id="book_id" name="book_id" value="<?php echo $book['book_id']; ?>">
            </div>
            <div class="form-group">
                <label for="book_name">Book Name:</label>
                <input type="text" class="form-control" id="book_name" name="book_name" value="<?php echo $book['book_name']; ?>">
            </div>
            <div class="form-group">
                <label for="book_category">Book Category:</label>
                <input type="text" class="form-control" id="category_id" name="category_id" value="<?php echo $book['category_id']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_books.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
