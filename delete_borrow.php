<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['borrow_id'])) {
    $borrow_id = $_GET['borrow_id'];

    $stmt = $conn->prepare("DELETE FROM bookborrower WHERE borrow_id = ?");
    $stmt->bind_param("s", $borrow_id);

    if ($stmt->execute()) {
        header("Location: view_borrow.php?msg=borrow+deleted+successfully");
        exit();
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting borrow: ' . $conn->error . '</div>';
    }
    $stmt->close();
} else {
    header("Location:view_borrow.php");
}
$conn->close();
?>
