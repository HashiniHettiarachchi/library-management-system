<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

function validate_category_id($category_id) {
    return preg_match('/^C\d{3}$/', $category_id);
}

$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $date_modified = date('Y-m-d H:i:s');

    if (!validate_category_id($category_id)) {
        $error_messages['category_id_error'] = "Invalid Category ID format. It should be in the 'C<CATEGORY_ID>' format (e.g., C001).";
    }

    if (empty($category_name)) {
        $error_messages['category_name_error'] = "Category name cannot be empty.";
    }

    if (empty($error_messages)) {
        $stmt = $conn->prepare("INSERT INTO bookcategory (category_id, category_name, date_modified) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $category_id, $category_name, $date_modified);

        if ($stmt->execute()) {
            $success_message = "Category added successfully.";
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
    <title>Add New Category</title>
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

        input[type="text"] {
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
        <h1 class="display-4">Add New Category</h1>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <form action="add_category.php" method="post">
            <div class="form-group">
                <label for="category_id">Category ID:</label>
                <input type="text" id="category_id" name="category_id" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['category_id_error'])) echo $error_messages['category_id_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" class="form-control" required>
                <span class="error">
                    <?php if (!empty($error_messages['category_name_error'])) echo $error_messages['category_name_error']; ?>
                </span>
            </div>
            <input type="submit" value="Add Category" class="btn btn-primary">
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
