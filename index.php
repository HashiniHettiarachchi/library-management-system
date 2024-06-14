<?php
session_start();
include 'db_connection.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection and XSS
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Check if the user exists and the password is correct
        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: src/index.html");
            exit(); // Ensure to exit after redirect
        } else {
            $error_message = "Invalid username or password.";
        }

        $stmt->close();
    } else {
        $error_message = "Database query failed: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Staff Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            width: 400px;
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
        .login-container form input[type="text"],
        .login-container form input[type="password"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 25px;
            border: 1px solid #ced4da;
        }
        .login-container form input[type="submit"] {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .login-container form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .user-photo img {
            max-width: 100px;
            height: auto;
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="photo">
            <div class="user-photo d-flex justify-content-center mb-4">
                <img src="user_logo.png" alt="User Photo">
            </div>
        </div>
        <div class="form">
            <h1>Welcome Back!</h1>
            <form action="" method="post">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <input type="submit" class="btn btn-primary btn-block mt-4" value="Login">
            </form>
            <?php if ($error_message): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
