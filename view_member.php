<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$result = $conn->query("SELECT * FROM member");

if (!$result) {
    die("Error executing the query: " . $conn->error);
}

// Check for delete status and prepare message
$delete_message = "";
if (isset($_GET['delete'])) {
    if ($_GET['delete'] == 'success') {
        $delete_message = "Member deleted successfully.";
    } elseif ($_GET['delete'] == 'error') {
        $delete_message = "Error deleting member.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Members</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            margin: 0 5px;
        }
        a.back-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
            padding: 10px 0;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a.back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Member List</h1>
    <!-- Display delete status message -->
    <?php if (!empty($delete_message)): ?>
        <div class="alert alert-<?php echo ($_GET['delete'] == 'success') ? 'success' : 'danger'; ?>">
            <?php echo $delete_message; ?>
        </div>
    <?php endif; ?>

    <table class="table">
        <thead class="thead-light">
            <tr>
                <th>Member ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birthday</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['member_id']; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><?php echo $row['last_name']; ?></td>
                <td><?php echo $row['birthday']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <a href="update_member.php?member_id=<?php echo $row['member_id']; ?>" class="btn btn-warning btn-sm">Update</a>
                    <a href="delete_member.php?member_id=<?php echo $row['member_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="src/index.html" class="btn btn-primary">Back to Admin Panel</a>
</body>
</html>

<?php
$result->close();
$conn->close();
?>
