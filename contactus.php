<?php
// Include database connection file
include('dbcon/Database.php');

// Create Database instance
$db = new Database();
$conn = $db->getConnection();

// Initialize success and error messages
$success_message = '';
$error_message = '';

// Simulate form submission and session expiry check (replace with actual logic)
$form_submission_successful = false;  // Set this based on actual form submission success
$session_expired = false;  // Set this based on actual session expiry check

// Handling form submission and session expiry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Prepare SQL query with placeholders for safe data insertion
    $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
    
    // Prepare the statement
    $stmt = $conn->prepare($query);
    
    // Bind the form data to the prepared statement
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);

    // Execute the query
    if ($stmt->execute()) {
        // Success message after insertion
        $form_submission_successful = true;
    } else {
        // Error handling if query execution fails
        $error_message = "Error: " . implode(", ", $stmt->errorInfo());
    }
}

// Handling success or error messages based on conditions
if ($form_submission_successful) {
    $success_message = "Thank you for contacting us! Your message has been successfully received.";
} elseif ($session_expired) {
    $error_message = "Your session has expired. Please log in again.";
} else {
    $error_message = "Something went wrong, please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .contact-container {
            background: linear-gradient(45deg, #00494F, #198754);
            color: white;
            border-radius: 10px;
            padding: 40px;
            margin-top: 50px;
        }
        .contact-container h2 {
            font-size: 36px;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-custom {
            background-color: #198754;
            color: white;
            border-radius: 5px;
        }
        .btn-custom:hover {
            background-color: #00494F;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contact-container mx-auto">
        <h2>Contact Us</h2>

        <!-- Show success or error message -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Your Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-custom">Send Message</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2024 Your Company | All rights reserved.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
