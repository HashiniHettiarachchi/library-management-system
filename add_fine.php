<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "library_system"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function validate_fine_id($fine_id) {
    return preg_match('/^F\d{3}$/', $fine_id);
}

function validate_member_id($member_id) {
    return preg_match('/^M\d{3}$/', $member_id);
}

function validate_book_id($book_id) {
    return preg_match('/^B\d{3}$/', $book_id);
}

$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $fine_amount = $_POST['fine_amount'];
    $date_modified = date('Y-m-d H:i:s');

    if (!validate_fine_id($fine_id)) {
        $error_messages['fine_id_error'] = "Invalid Fine ID format. It should be in the 'F<FINE_ID>' format (e.g., F001).";
    }

    if (!validate_member_id($member_id)) {
        $error_messages['member_id_error'] = "Invalid Member ID format. It should be in the 'M<MEMBER_ID>' format (e.g., M001).";
    }

    if (!validate_book_id($book_id)) {
        $error_messages['book_id_error'] = "Invalid Book ID format. It should be in the 'B<BOOK_ID>' format (e.g., B001).";
    }

    if ($fine_amount < 2 || $fine_amount > 500) {
        $error_messages['fine_amount_error'] = "Fine amount must be between 2 and 500 LKR.";
    }

    if (empty($error_messages)) {
        // Check if fine ID already exists
        $stmt_check = $conn->prepare("SELECT fine_id FROM fine WHERE fine_id = ?");
        $stmt_check->bind_param("s", $fine_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_messages['fine_id_error'] = "Fine ID already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO fine (fine_id, member_id, book_id, fine_amount, fine_date_modified) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $fine_id, $member_id, $book_id, $fine_amount, $date_modified);

            if ($stmt->execute()) {
                $success_message = "Fine added successfully.";
            } else {
                $error_messages['database_error'] = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt_check->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Fine</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional CSS styles if needed */
        body {
            padding: 20px;
        }

        h1 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center; /* Center align the heading */
        }

        form {
            max-width: 400px;
            margin: 0 auto;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .success {
            color: green;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="display-4">Add New Fine</h1>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <form action="add_fine.php" method="post">
            <div class="form-group">
                <label for="fine_id">Fine ID:</label>
                <input type="text" id="fine_id" name="fine_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['fine_id_error'])) echo $error_messages['fine_id_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <input type="text" id="member_id" name="member_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['member_id_error'])) echo $error_messages['member_id_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <input type="text" id="book_id" name="book_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['book_id_error'])) echo $error_messages['book_id_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="fine_amount">Fine Amount:</label>
                <input type="number" id="fine_amount" name="fine_amount" class="form-control" min="2" max="500" required>
                <span class="error">
                    <?php if (!empty($error_messages['fine_amount_error'])) echo $error_messages['fine_amount_error']; ?>
                </span>
            </div>
            <input type="submit" value="Add Fine" class="btn btn-primary">
            <span class="error">
                <?php if (!empty($error_messages['database_error'])) echo $error_messages['database_error']; ?>
            </span>
            <div class="mt-3">
        <a href="src/index.html" class="btn btn-primary btn-block mt-3">Go Back</a>
    </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
