function validateForm() {
    // Reset error message
    document.getElementById('user-id-error').style.display = 'none';

    // Your validation logic here
    // Example:
    var userId = document.getElementById('user_id').value;
    // You can add more validation here...

    // If validation fails
    if (/* Validation fails */) {
        // Show error message
        document.getElementById('user-id-error').style.display = 'block';
        return false; // Prevent form submission
    }

    return true; // Proceed with form submission
}
