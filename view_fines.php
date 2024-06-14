<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$error_message = "";

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "library_system"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    $error_message = "Connection failed: " . $conn->connect_error;
}

$result = null;

if (empty($error_message)) {
    // Fetch fines data with member names
    $result = $conn->query("SELECT f.fine_id, m.first_name AS member_first_name, m.last_name AS member_last_name, 
                        b.book_name, f.fine_amount, f.fine_date_modified 
                        FROM fine f 
                        JOIN member m ON f.member_id = m.member_id 
                        JOIN book b ON f.book_id = b.book_id");}


?>

<!DOCTYPE html>
<html>
<head>
    <title>View Fines</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Fine List</h1>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php elseif ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fine ID</th>
                        <th>Member Name</th>
                        <th>Book Name</th>
                        <th>Fine Amount (LKR)</th>
                        <th>Date Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['fine_id']; ?></td>
                            <td><?php echo $row['member_first_name'] . ' ' . $row['member_last_name']; ?></td>
                            <td><?php echo $row['book_name']; ?></td>
                            <td><?php echo $row['fine_amount']; ?></td>
                            <td><?php echo $row['fine_date_modified']; ?></td>
                            <td>
                                <a href="update_fine.php?fine_id=<?php echo $row['fine_id']; ?>" class="btn btn-primary btn-sm">Update</a>
                                <form method="post" action="delete_fine.php" style="display:inline;">
                                    <input type="hidden" name="fine_id" value="<?php echo $row['fine_id']; ?>">
                                    <button type="submit" name="delete_fine" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No fines found.</p>
        <?php endif; ?>
        <a href="src/index.html" class="btn btn-primary">Back to Admin Panel</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
