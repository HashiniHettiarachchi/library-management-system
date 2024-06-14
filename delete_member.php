<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: view_member.php?delete=success");
    exit();
        
}

if (isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];

    // Delete related records in bookborrower first
    $stmt_delete_borrower = $conn->prepare("DELETE FROM bookborrower WHERE member_id = ?");
    $stmt_delete_borrower->bind_param("s", $member_id);

    if ($stmt_delete_borrower->execute()) {
        // Now delete the member
        $stmt_delete_member = $conn->prepare("DELETE FROM member WHERE member_id = ?");
        $stmt_delete_member->bind_param("s", $member_id);

        if ($stmt_delete_member->execute()) {
            // Redirect with success message
            header("Location: view_member.php?delete=success");
            exit();
        } else {
            // Redirect with error message if member deletion fails
            header("Location: view_member.php?delete=error");
            exit();
        }

        $stmt_delete_member->close();
    } else {
        // Redirect with error message if bookborrower deletion fails
        header("Location: view_member.php?delete=error");
        exit();
    }

    $stmt_delete_borrower->close();
    $conn->close();
} else {
    // Redirect if member_id is not provided
    header("Location: view_member.php");
    exit();
}
?>
