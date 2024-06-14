<?php
session_start();
include 'db_connection.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if the form is submitted for updating the fine
if (isset($_POST['update_fine'])) {
    // Retrieve form data
    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $fine_amount = $_POST['fine_amount'];
    $fine_date_modified = $_POST['fine_date_modified'];

    // Validate the data (You can add more validation if needed)

    // Update the fine in the database
    $sql = "UPDATE fine SET member_id=?, fine_amount=?, fine_date_modified=? WHERE fine_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $member_id, $fine_amount, $fine_date_modified, $fine_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Fine updated successfully.";
        header("Location: view_fines.php"); // Redirect to view fines page after successful update
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating fine: " . $conn->error;
    }
    $stmt->close();
}

// Fetch fine details
$fine = null; // Initialize fine variable
if (isset($_GET['fine_id'])) {
    $fine_id = $_GET['fine_id'];
    // Check if the fine ID matches the expected format (e.g., F001)
    if (!preg_match('/^F\d+$/', $fine_id)) {
        echo '<div class="alert alert-danger" role="alert">Invalid fine ID: ' . htmlspecialchars($fine_id) . '</div>';
        exit(); // Stop further execution
    }
    // Fetch fine details based on the alphanumeric fine ID
    $sql = "SELECT fine_id, member_id, fine_amount, fine_date_modified FROM fine WHERE fine_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fine_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $fine = $result->fetch_assoc(); // Fetch fine details
        } else {
            echo '<div class="alert alert-danger" role="alert">Fine not found. Fine ID: ' . htmlspecialchars($fine_id) . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Error retrieving fine details: ' . $conn->error . '</div>';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Fine</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Update Fine</h2>
        <?php if ($fine): ?>
        <form method="post">
            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <input type="text" class="form-control" id="member_id" name="member_id" value="<?php echo htmlspecialchars($fine['member_id']);?>" required>
            </div>
            <div class="form-group">
                <label for="fine_amount">Fine Amount (LKR):</label>
                <input type="number" class="form-control" id="fine_amount" name="fine_amount" value="<?php echo htmlspecialchars($fine['fine_amount']);?>" required>
            </div>
            <div class="form-group">
                <label for="fine_date_modified">Date Modified:</label>
                <input type="date" class="form-control" id="fine_date_modified" name="fine_date_modified" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($fine['fine_date_modified'])));?>">
            </div>
            <input type="hidden" name="fine_id" value="<?php echo htmlspecialchars($fine['fine_id']);?>">
            <button type="submit" class="btn btn-primary" name="update_fine">Update</button>
        </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
