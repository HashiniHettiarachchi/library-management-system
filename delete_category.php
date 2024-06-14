<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $conn->prepare("DELETE FROM bookcategory WHERE category_id = ?");
    $stmt->bind_param("s", $category_id);
    if ($stmt->execute()) {
        header("Location: view_categories.php?msg=Category+deleted+successfully");
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting category: ' . $conn->error . '</div>';
    }
    $stmt->close();
} else {
    header("Location: view_categories.php");
}

$conn->close();
?>
