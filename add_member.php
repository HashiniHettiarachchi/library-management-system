<?php
// Include database connection
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Function to validate member ID format
function validate_member_id($member_id) {
    return preg_match('/^M\d{3}$/', $member_id);
}

// Function to validate email format
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$error_message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $member_id = $_POST['member_id'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];

    // Validate member ID format
    if (!validate_member_id($member_id)) {
        $error_message = "Invalid Member ID format. It should be in the 'M<MEMBER_ID>' format (e.g., M001).";
    } 
    // Validate email format
    elseif (!validate_email($email)) {
        $error_message = "Invalid email format.";
    } 
    else {
        // Prepare and execute SQL query to insert member into database
        $stmt = $conn->prepare("INSERT INTO member (member_id, first_name, last_name, birthday, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $member_id, $firstname, $lastname, $birthday, $email);

        if ($stmt->execute()) {
            $success_message = "Member added successfully.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Add Member</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .registration-container {
            max-width: 500px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .registration-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .registration-container label {
            font-weight: bold;
        }
        .registration-container input[type="text"],
        .registration-container input[type="date"],
        .registration-container input[type="email"],
        .registration-container input[type="submit"] {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .registration-container input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .registration-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registration-container">
            <h1>Add New Member</h1>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php elseif (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <form action="add_member.php" method="post">
                <div class="form-group">
                    <label for="member_id">Member ID:</label>
                    <input type="text" id="member_id" name="member_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="birthday">Birthday:</label>
                    <input type="date" id="birthday" name="birthday" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <input type="submit" value="Add Member" class="btn btn-primary">
                <div class="mt-3">
        <a href="src/index.html" class="btn btn-primary btn-block mt-3">Go Back</a>
    </div>
            </form>
        </div>
    </div>
</body>
</html>
