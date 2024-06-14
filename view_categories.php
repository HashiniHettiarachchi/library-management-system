<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Perform the SQL query
$result = $conn->query("SELECT * FROM bookcategory");

// Check if the query was successful
if (!$result) {
    die("Error: " . $conn->error); // Display the error message if the query fails
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }

        h1 {
            color: #007bff;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Category List</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Date Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    $date_modified = new DateTime($row['date_modified']);
                    $modified_date = $date_modified->format('Y-m-d');
                
                ?>
                <tr>
                    <td><?php echo $row['category_id']; ?></td>
                    <td><?php echo $row['category_Name']; ?></td>
                    <td><?php echo $modified_date; ?></td>
                    
                    <td>
                        <a href="update_category.php?category_id=<?php echo $row['category_id']; ?>" class="btn btn-warning btn-sm">Update</a>
                        <a href="delete_category.php?category_id=<?php echo $row['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
