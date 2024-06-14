<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_GET['user_id'];

// Fetch current user data
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if user data was found
if ($user === null) {
    // User not found, redirect or show an error message
    header("Location: view_users.php?msg=User+not+found");
    exit();
}

// Initialize error message
$error_message = "";

// Initialize variables for form fields
$new_user_id = $user['user_id'];
$first_name = $user['first_name'];
$last_name = $user['last_name'];
$username = $user['username'];
$email = $user['email'];
$password = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the fields that were submitted
    $new_user_id = $_POST['user_id'] ?? $user['user_id'];
    $first_name = $_POST['first_name'] ?? $user['first_name'];
    $last_name = $_POST['last_name'] ?? $user['last_name'];
    $username = $_POST['username'] ?? $user['username'];
    $password = $_POST['password'] ?? ''; // Show the entered password
    $email = $_POST['email'] ?? $user['email'];

    // Validate user ID format
    if (!preg_match("/^U\d{3}$/", $new_user_id)) {
        $error_message = "User ID must be in the format U<3 digits> (e.g., U001)";
    } else {
        // Check for duplicate data
        $check_sql = "SELECT user_id FROM user WHERE (user_id = ? OR username = ? OR email = ?) AND user_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ssss", $new_user_id, $username, $email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_message = "Error: Duplicate data exists. Please ensure the User ID, Username, or Email are unique.";
        } else {
            // If a new password is entered, update it
            if (!empty($password)) {
                // Check if the password meets the minimum length requirement
                if (strlen($password) < 8) {
                    $error_message = "Password must be at least 8 characters long";
                } else {
                    // Hash the new password before storing it
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    // Prepare the SQL statement
                    $sql = "UPDATE user SET user_id = ?, first_name = ?, last_name = ?, username = ?, password = ?, email = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssss", $new_user_id, $first_name, $last_name, $username, $hashed_password, $email, $user_id);
                    if ($stmt->execute()) {
                        header("Location: update_user.php?user_id=$user_id&msg=User+updated+successfully");
                        exit();
                    } else {
                        $error_message = "Error updating record: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                // If no new password is entered, keep the existing hashed password
                $sql = "UPDATE user SET user_id = ?, first_name = ?, last_name = ?, username = ?, email = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $new_user_id, $first_name, $last_name, $username, $email, $user_id);
                if ($stmt->execute()) {
                    header("Location: update_user.php?user_id=$user_id&msg=User+updated+successfully");
                    exit();
                } else {
                    $error_message = "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Update User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .box {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            max-width: 600px;
            margin: 20px auto;
        }
        label {
            font-size: 0.9em;
            font-weight: bold;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .toggle-password i {
            color: #007bff;
            font-size: 1.2em;
        }
        .center {
            text-align: center;
        }
        .right-align {
            text-align: right;
        }
        h2 {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 20px;
            color: #007bff;
        }
        .back-link, .btn-update {
            display: inline-block;
            margin-top: 20px;
            color: white;
            background-color: #007bff;
            border: 1px solid #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-link:hover, .btn-update:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update User</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
        }
        if (isset($_GET['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_GET['msg']) . '</div>';
        }
        ?>
        <div class="box">
            <form method="post">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($new_user_id);?>">
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name);?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name);?>">
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username);?>">
                </div>
                <div class="form-group password-container">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <span class="toggle-password">
                        <i class="fa fa-eye" id="toggle-password-icon" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email);?>">
                </div>
                <div class="center">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                <div class="center">
                    <a href="view_users.php" class="back-link">Back to View user</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Function to hide the messages after 3 seconds
        setTimeout(function(){
            document.querySelectorAll('.alert').forEach(function(alert){
                alert.style.display = 'none';
            });
        }, 3000);

        document.querySelector('.toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
