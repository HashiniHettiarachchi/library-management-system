<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $new_member_id = $_POST['new_member_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];

    // Check if the new member ID or email already exists
    $check_stmt = $conn->prepare("SELECT * FROM member WHERE (member_id = ? OR email = ?) AND member_id != ?");
    $check_stmt->bind_param("sss", $new_member_id, $email, $member_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo '<div class="alert alert-danger" role="alert">Error: Member ID or Email already exists.</div>';
    } else {
        $update_stmt = $conn->prepare("UPDATE member SET member_id = ?, first_name = ?, last_name = ?, birthday = ?, email = ? WHERE member_id = ?");
        $update_stmt->bind_param("ssssss", $new_member_id, $first_name, $last_name, $birthday, $email, $member_id);

        if ($update_stmt->execute()) {
            header("Location: view_member.php?msg=Member+updated+successfully");
            exit();
        } else {
            echo '<div class="alert alert-danger" role="alert">Error updating member: ' . $conn->error . '</div>';
        }
        $update_stmt->close();
    }
    $check_stmt->close();
}

if (isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];
    $stmt = $conn->prepare("SELECT * FROM member WHERE member_id = ?");
    $stmt->bind_param("s", $member_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
    } else {
        die("Error executing the query: " . $conn->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Member</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Update Member</h1>
        <form method="post">
            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <input type="text" class="form-control" id="member_id" name="member_id" value="<?php echo $member['member_id']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="new_member_id">New Member ID:</label>
                <input type="text" class="form-control" id="new_member_id" name="new_member_id" value="<?php echo $member['member_id']; ?>">
            </div>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $member['first_name']; ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $member['last_name']; ?>">
            </div>
            <div class="form-group">
                <label for="birthday">Birthday:</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo $member['birthday']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $member['email']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_member.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
