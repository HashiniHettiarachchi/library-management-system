<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$sql = "SELECT bb.borrow_id, b.book_name, m.first_name, m.last_name, bb.borrow_status, bb.borrower_date_modified 
        FROM bookborrower bb 
        JOIN book b ON bb.book_id = b.book_id 
        JOIN member m ON bb.member_id = m.member_id";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Borrow Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your CSS styles here */
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        h1 {
            margin-bottom: 20px;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            margin: 5px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Borrow Details List</h1>
    <table>
        <tr>
            <th>Borrow ID</th>
            <th>Book Name</th>
            <th>Member Name</th>
            <th>Borrow Status</th>
            <th>Date Modified</th>
            <th>Actions</th> <!-- Added Actions column -->
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['borrow_id']; ?></td>
            <td><?php echo $row['book_name']; ?></td>
            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
            <td><?php echo $row['borrow_status']; ?></td>
            <td><?php echo $row['borrower_date_modified']; ?></td>
            <td>
                <!-- Update button linking to update_borrow.php with borrow_id as parameter -->
                <a href="update_borrow.php?borrow_id=<?php echo $row['borrow_id']; ?>" class="btn btn-warning">Update</a>
                <!-- Delete button linking to delete_borrow.php with borrow_id as parameter -->
                <a href="delete_borrow.php?borrow_id=<?php echo $row['borrow_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this borrow?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="src/index.html" class="btn btn-primary">Back to Admin Panel</a>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
