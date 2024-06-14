<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

function validate_borrow_id($borrow_id) {
    return preg_match('/^BR\d{3}$/', $borrow_id);
}

function validate_book_id($book_id) {
    return preg_match('/^B\d{3}$/', $book_id);
}

function validate_member_id($member_id) {
    return preg_match('/^M\d{3}$/', $member_id);
}

$error_messages = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrow_id = $_POST['borrow_id'];
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $borrow_status = $_POST['borrow_status'];
    $borrower_date_modified = date('Y-m-d H:i:s');

    if (!validate_borrow_id($borrow_id)) {
        $error_messages['borrow_id_error'] = "Invalid Borrow ID format. It should be in the 'BR<BORROW_ID>' format (e.g., BR001).";
    }

    if (!validate_book_id($book_id)) {
        $error_messages['book_id_error'] = "Invalid Book ID format. It should be in the 'B<BOOK_ID>' format (e.g., B001).";
    }

    if (!validate_member_id($member_id)) {
        $error_messages['member_id_error'] = "Invalid Member ID format. It should be in the 'M<MEMBER_ID>' format (e.g., M001).";
    }

    if (empty($error_messages)) {
        $stmt = $conn->prepare("INSERT INTO bookborrower (borrow_id, book_id, member_id, borrow_status, borrower_date_modified) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $borrow_id, $book_id, $member_id, $borrow_status, $borrower_date_modified);

        if ($stmt->execute()) {
            $success_message = "Borrow details added successfully.";
        } else {
            $error_messages['database_error'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Borrow Details</title>
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

        input[type="text"],
        select {
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
        <h1 class="display-4">Add Borrow Details</h1>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
            <?php unset($success_message); ?> <!-- Unset the success message after displaying -->
        <?php endif; ?>
        <form action="add_borrow.php" method="post">
            <div class="form-group">
                <label for="borrow_id">Borrow ID:</label>
                <input type="text" id="borrow_id" name="borrow_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['borrow_id_error'])) echo $error_messages['borrow_id_error']; ?>
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
                <label for="member_id">Member ID:</label>
                <input type="text" id="member_id" name="member_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['member_id_error'])) echo $error_messages['member_id_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="borrow_status">Borrow Status:</label>
                <select id="borrow_status" name="borrow_status" class="form-control" required>
                    <option value="available">Available</option>
                    <option value="borrowed">Borrowed</option>
                </select>
            </div>
            <input type="submit" value="Add Borrow Details" class="btn btn-primary">
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
