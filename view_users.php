<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>User List</h1>
    <?php
    session_start();
    include 'db_connection.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }

    // Handle deletion if requested
    if (isset($_GET['delete_user_id'])) {
        $delete_user_id = $_GET['delete_user_id'];
        $delete_stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
        $delete_stmt->bind_param("s", $delete_user_id);
        if ($delete_stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">User deleted successfully</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error deleting user: ' . $conn->error . '</div>';
        }
        $delete_stmt->close();
    }

    // Fetch users
    $result = $conn->query("SELECT * FROM user");

    if ($result) {
        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th>User ID</th>';
            echo '<th>First Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Username</th>';
            echo '<th>Email</th>';
            echo '<th>Actions</th>'; // Merged column for actions
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['user_id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>';
                // Combined buttons for update and delete
                echo '<a href="update_user.php?user_id=' . $row['user_id'] . '" class="btn btn-primary">Update</a>';
                echo '<a href="view_users.php?delete_user_id=' . $row['user_id'] . '" class="btn btn-danger ml-1">Delete</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="alert alert-info" role="alert">No users found</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Error fetching users: ' . $conn->error . '</div>';
    }

    $conn->close();
    ?>

    <a href="src/index.html" class="btn btn-primary">Back to Admin Panel</a>
</body>
</html>
