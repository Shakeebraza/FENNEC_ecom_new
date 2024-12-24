<?php
require_once 'global.php';
include_once 'header.php';
$success_message = '';
$error_message = '';


$form_submission_successful = false; 
$session_expired = false;  


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $Data = [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'created_at' => $currentDateTime,
    ];

    $returnData=$dbFunctions->setData('contacts',$Data);
    if($returnData['success'] == true){
        $success_message = "Thank you for contacting us! Your message has been successfully received.";
    }else{
        $error_message = "Your session has expired. Please log in again.";
    }
}



?>


<style>


    .contact-container {
        background-color: white;
        color: #00494F;
        border-radius: 10px;
        padding: 40px;
        margin-top: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 50px;
    }

    .contact-container h2 {
        font-size: 36px;
        margin-bottom: 30px;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #ccc;
        color: #00494F;
    }

    .btn-custom {
        background-color: #00494F;
        color: white;
        border-radius: 5px;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #198754;
        color: white;
    }

    .footer {
        text-align: center;
        margin-top: 30px;
        font-size: 14px;
        color: #555;
    }
</style>

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
                <input type="text" class="form-control" id="name" name="name" 
                    value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" 
                    required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                    required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Your Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-custom">Send Message</button>
        </form>

    </div>
</div>



<?php
include_once 'footer.php';
?>

<script>
  
</script>
</body>

</html>
