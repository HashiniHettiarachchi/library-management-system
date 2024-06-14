<?php
include 'db_connection.php';

// Define default username and hashed password
$username = 'danu123';
$password = '123456789';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute the insert statement
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Default user created successfully.";
} else {
    echo "Error creating default user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
