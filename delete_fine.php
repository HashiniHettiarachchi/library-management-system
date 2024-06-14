<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['delete_fine'])) {
    $fine_id = $_POST['fine_id'];

    // Prepare the SQL statement
    $sql = "DELETE FROM fine WHERE fine_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fine_id);

    // Execute the prepared statement
    if ($stmt->execute()) {
        header("Location: view_fines.php?msg=Fine+deleted+successfully");
        exit(); // Ensure no further code execution after redirection
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting fine: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

$conn->close();
?>
