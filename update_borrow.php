<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch borrow details to pre-fill the form
if (isset($_GET['borrow_id'])) {
    $borrow_id = $_GET['borrow_id'];
    $stmt = $conn->prepare("SELECT bb.borrow_id, bb.book_id, bb.member_id, bb.borrow_status, bb.borrower_date_modified, b.book_name, m.first_name, m.last_name
                            FROM bookborrower bb
                            JOIN book b ON bb.book_id = b.book_id
                            JOIN member m ON bb.member_id = m.member_id
                            WHERE bb.borrow_id = ?");
    $stmt->bind_param("s", $borrow_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $borrow = $result->fetch_assoc();
    } else {
        die("Error executing the query: " . $conn->error);
    }
    $stmt->close();
}

// Update borrow details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_borrow_id = $_POST['original_borrow_id'];
    $borrow_id = $_POST['borrow_id'];
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $borrow_status = $_POST['borrow_status'];

    $update_stmt = $conn->prepare("UPDATE bookborrower SET borrow_id = ?, book_id = ?, member_id = ?, borrow_status = ? WHERE borrow_id = ?");
    $update_stmt->bind_param("sssss", $borrow_id, $book_id, $member_id, $borrow_status, $original_borrow_id);

    if ($update_stmt->execute()) {
        header("Location: view_borrow.php?msg=Borrow+updated+successfully");
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating borrow: ' . $conn->error . '</div>';
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Borrow Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Update Borrow Details</h1>
        <form method="post">
            <input type="hidden" name="original_borrow_id" value="<?php echo $borrow['borrow_id']; ?>">
            <div class="form-group">
                <label for="borrow_id">Borrow ID:</label>
                <input type="text" class="form-control" id="borrow_id" name="borrow_id" value="<?php echo $borrow['borrow_id']; ?>">
            </div>
            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <input type="text" class="form-control" id="book_id" name="book_id" value="<?php echo $borrow['book_id']; ?>">
            </div>
            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <input type="text" class="form-control" id="member_id" name="member_id" value="<?php echo $borrow['member_id']; ?>">
            </div>
            <div class="form-group">
                <label for="borrow_status">Borrow Status:</label>
                <select class="form-control" id="borrow_status" name="borrow_status">
                    <option value="borrowed" <?php if ($borrow['borrow_status'] == 'borrowed') echo 'selected'; ?>>Borrowed</option>
                    <option value="available" <?php if ($borrow['borrow_status'] == 'available') echo 'selected'; ?>>Available</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_borrow.php" class="btn btn-secondary">Cancel</a>
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
