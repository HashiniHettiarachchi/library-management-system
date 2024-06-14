<?php
// Initialize message variables
$success_message = '';
$error_message = '';

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "library_system";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $user_id = htmlspecialchars($_POST['user_id']);
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email']);

    // Validate form data
    if (!preg_match("/^U\d{3}$/", $user_id)) {
        $error_message = "Invalid User ID format.";
    } elseif (strlen($password) <= 8) {
        $error_message = "Password must be more than 8 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if user ID already exists
        $stmt_check = $conn->prepare("SELECT user_id FROM user WHERE user_id = ?");
        $stmt_check->bind_param("s", $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "User ID already exists.";
        } else {
            // Insert new staff member
            $stmt = $conn->prepare("INSERT INTO user (user_id, first_name, last_name, username, password, email) VALUES (?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt->bind_param("ssssss", $user_id, $first_name, $last_name, $username, $hashed_password, $email);

            try {
                if ($stmt->execute()) {
                    $success_message = "Registration successful!";
                } else {
                    $error_message = "Registration failed.";
                }
            } catch (mysqli_sql_exception $e) {
                $error_message = "Registration failed: " . $e->getMessage();
            }
        }
        $stmt_check->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Staff Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            width: 400px;
        }
        .register-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
        .register-container form input[type="text"],
        .register-container form input[type="password"],
        .register-container form input[type="email"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 25px;
            border: 1px solid #ced4da;
        }
        .register-container form input[type="submit"] {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .register-container form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
        }
    </style>
    <script>
    function validateForm() {
        var userId = document.getElementById('user_id').value;
        var password = document.getElementById('password').value;
        var userIdRegex = /^U\d{3}$/;
        var passwordMinLength = 8;
        var errors = false;

        if (!userIdRegex.test(userId)) {
            document.getElementById('user_id_error').textContent = 'User ID must be in the format U<USER_ID> (e.g., U001)';
            errors = true;
            setTimeout(function() {
                document.getElementById('user_id_error').textContent = ''; // Clear error message after 2 seconds
            }, 2000);
        }

        if (password.length < passwordMinLength) {
            document.getElementById('password_error').textContent = 'Password must be at least ' + passwordMinLength + ' characters long.';
            errors = true;
            setTimeout(function() {
                document.getElementById('password_error').textContent = ''; // Clear error message after 2 seconds
            }, 2000);
        }

        return !errors;
    }
</script>

</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <form id="registrationForm" action="" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="user_id">User ID (e.g., U001):</label>
                <input type="text" id="user_id" name="user_id" class="form-control" required>
                <div id="user_id_error" class="error-message"></div> <!-- User ID error message container -->
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
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password (min 8 characters):</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div id="password_error" class="error-message"></div> <!-- Password error message container -->
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <input type="submit" value="Register" class="btn btn-primary btn-block mt-4">
            </form>
            <div class="mt-3">
        <a href="src/index.html" class="btn btn-primary btn-block mt-3">Go Back</a>
    </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
