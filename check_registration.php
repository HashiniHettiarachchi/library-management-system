<?php
// check_registration.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$username = $_POST['username'];
$email = $_POST['email'];

// Check if username or email exists
$response = [
    'usernameExists' => false,
    'emailExists' => false
];

$stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['username'] === $username) {
            $response['usernameExists'] = true;
        }
        if ($row['email'] === $email) {
            $response['emailExists'] = true;
        }
    }
}

echo json_encode($response);

$conn->close();
?>
