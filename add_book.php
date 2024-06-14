<?php
session_start();

// Include database connection
include 'db_connection.php';

// Initialize error flag
$error_flag = false;
$error_messages = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $book_id = $_POST['book_id'];
    $book_name = $_POST['book_name'];
    $category_id = $_POST['category_id'];

    // Validate book ID format
    if (!preg_match("/^B\d{3}$/", $book_id)) {
        $error_messages['book_id_error'] = "Invalid Book ID format. Book ID must be in the format B<BOOK_ID> (e.g., B001)";
        $error_flag = true;
    }

    // Validate book name (optional additional validation)
    if (empty($book_name)) {
        $error_messages['book_name_error'] = "Book name cannot be empty";
        $error_flag = true;
    }

    // Check if the book already exists
    $check_stmt = $conn->prepare("SELECT * FROM book WHERE book_id = ?");
    $check_stmt->bind_param("s", $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_messages['book_exists_error'] = "Error: Book with this ID already exists.";
        $error_flag = true;
    }
    $check_stmt->close();

    // If there are no errors, proceed with database insertion
    if (!$error_flag) {
        // Prepare SQL statement
        $sql = "INSERT INTO book (book_id, book_name, category_id) VALUES (?, ?, ?)";  // Ensure correct column name

        // Prepare statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("sss", $book_id, $book_name, $category_id);

            // Execute statement
            if ($stmt->execute()) {
                $success_message = "New book added successfully!";
            } else {
                $error_messages['database_error'] = "Error: " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        } else {
            $error_messages['database_error'] = "Error preparing statement: " . $conn->error;
        }
    }

    // Close database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Book</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }

        h1 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
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
    <script>
        function validateForm() {
            var bookId = document.getElementById('book_id').value;
            var regex = /^B\d{3}$/;
            if (!regex.test(bookId)) {
                document.getElementById('book_id_error').textContent = 'Book ID must be in the format B<BOOK_ID> (e.g., B001)';
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Add New Book</h1>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <form action="add_book.php" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <input type="text" id="book_id" name="book_id" class="form-control" required>
                <span id="book_id_error" class="error">
                    <?php if (!empty($error_messages['book_id_error'])) echo $error_messages['book_id_error']; ?>
                </span>
                <span id="book_exists_error" class="error">
                    <?php if (!empty($error_messages['book_exists_error'])) echo $error_messages['book_exists_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="book_name">Book Name:</label>
                <input type="text" id="book_name" name="book_name" class="form-control" required>
                <span id="book_name_error" class="error">
                    <?php if (!empty($error_messages['book_name_error'])) echo $error_messages['book_name_error']; ?>
                </span>
            </div>
            <div class="form-group">
                <label for="category_id">Book Category:</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <?php
                    // Include database connection
                    include 'db_connection.php';

                    // Query to fetch categories
                    $sql = "SELECT category_id, category_Name FROM bookcategory";

                    // Execute query
                    $result = $conn->query($sql);

                    // Check if categories are fetched successfully
                    if ($result->num_rows > 0) {
                        // Loop through categories and display as options
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['category_id'] . "'>" . $row['category_Name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories found</option>";
                    }

                    // Close database connection
                    $conn->close();
                    ?>
                </select>
            </div>
            <input type="submit" value="Add Book" class="btn btn-primary">
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
