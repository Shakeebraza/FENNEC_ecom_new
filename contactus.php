<?php
require_once 'global.php';
include_once 'header.php';

// Retrieve contact details from the site_settings table
$contactNumber  = $fun->getData('site_settings', 'value', 2);
$whatsappNumber = $fun->getData('site_settings', 'value', 3);
$emailAddress   = $fun->getData('site_settings', 'value', 4);
$facebookLink   = $fun->getData('site_settings', 'value', 6);
$instagramLink  = $fun->getData('site_settings', 'value', 7);
$twitterLink    = $fun->getData('site_settings', 'value', 8);
$youtubeLink    = $fun->getData('site_settings', 'value', 9);
$pinterestLink  = $fun->getData('site_settings', 'value', 10);

// Retrieve the setting for displaying contact information
$contactInfoSetting = $fun->getData('approval_parameters', 'contact_information', 1);

$success_message = '';
$error_message   = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $message = $_POST['message'];
    $Data = [
        'name'       => $name,
        'email'      => $email,
        'message'    => $message,
        'created_at' => $currentDateTime,
    ];

    $returnData = $dbFunctions->setData('contacts', $Data);
    if ($returnData['success'] == true) {
        $success_message = "Thank you for contacting us! Your message has been successfully received.";
    } else {
        $error_message = "Your session has expired. Please log in again.";
    }
}
?>

<!-- Include Google Fonts and Bootstrap CSS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Global Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f7f9fc;
        margin: 0;
        padding: 0;
        color: #333;
    }
    /* Social Media Icons Section (Above the Form) */
    .cb-social-media-icons {
        margin: 40px 0 20px;
        text-align: center;
    }
    .cb-social-media-icons ul {
        padding: 0;
        list-style: none;
    }
    .cb-social-media-icons li {
        display: inline-block;
        margin: 0 10px;
    }
    .cb-social-icon {
        font-size: 1.8rem;
        color: #00494F; /* Primary color */
        transition: color 0.3s, transform 0.3s;
    }
    .cb-social-icon:hover {
        color: #003B44; /* Slightly darker for hover */
        transform: scale(1.1);
    }
    /* Flex Container for Form and Contact Details */
    .cb-contact-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
        margin-bottom: 40px;
    }
    .cb-contact-form-container,
    .cb-contact-info-container {
        flex: 1 1 500px;
        max-width: 600px;
    }
    /* Contact Form Container */
    .cb-contact-form-container {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .cb-contact-form-container h2 {
        font-size: 2.5rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 30px;
        color: #00494F;
    }
    .cb-form-label {
        font-weight: 500;
    }
    .cb-form-control {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: border-color 0.3s;
        width: 100%;
    }
    .cb-form-control:focus {
        border-color: #00494F;
        box-shadow: none;
    }
    .cb-btn-custom {
        background-color: #00494F;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
        width: 100%;
        margin-top: 20px;
    }
    .cb-btn-custom:hover {
        background-color: #003B44;
        transform: translateY(-2px);
    }
    .alert {
        border-radius: 10px;
    }
    /* Contact Info Container */
    .cb-contact-info-container {
        background: linear-gradient(135deg, #00494F, #006F75); /* Gradient using primary color and a lighter variant */
        border-radius: 15px;
        padding: 40px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .cb-contact-info-container h2 {
        text-align: center;
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 30px;
    }
    .cb-contact-info-container p {
        font-size: 1.1rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .cb-contact-info-icon {
        margin-right: 15px;
        font-size: 1.3rem;
    }
    .cb-contact-info-container a {
        color: #fff;
        text-decoration: underline;
    }
    .cb-contact-info-container a:hover {
        color: #e0e0e0;
    }
</style>

<div class="container">

    <!-- Social Media Icons Section (Above the Flex Container) -->
    <?php if (strtolower($contactInfoSetting) === 'visible'): ?>
    <div class="cb-social-media-icons">
        <ul class="list-inline">
            <?php if (!empty($facebookLink)): ?>
                <li class="list-inline-item">
                    <a href="<?= htmlspecialchars($facebookLink); ?>" target="_blank" class="cb-social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (!empty($instagramLink)): ?>
                <li class="list-inline-item">
                    <a href="<?= htmlspecialchars($instagramLink); ?>" target="_blank" class="cb-social-icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (!empty($twitterLink)): ?>
                <li class="list-inline-item">
                    <a href="<?= htmlspecialchars($twitterLink); ?>" target="_blank" class="cb-social-icon">
                        <i class="fab fa-twitter"></i>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (!empty($youtubeLink)): ?>
                <li class="list-inline-item">
                    <a href="<?= htmlspecialchars($youtubeLink); ?>" target="_blank" class="cb-social-icon">
                        <i class="fab fa-youtube"></i>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (!empty($pinterestLink)): ?>
                <li class="list-inline-item">
                    <a href="<?= htmlspecialchars($pinterestLink); ?>" target="_blank" class="cb-social-icon">
                        <i class="fab fa-pinterest"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Flex Container for Contact Form and Contact Details -->
    <div class="cb-contact-wrapper">
        <!-- Contact Form Container -->
        <div class="cb-contact-form-container">
            <h2>Contact Us</h2>

            <!-- Display success or error messages -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?= $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?= $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="cb-name" class="cb-form-label">Your Name</label>
                    <input type="text" class="cb-form-control" id="cb-name" name="name" 
                           value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" 
                           required>
                </div>
                <div class="mb-3">
                    <label for="cb-email" class="cb-form-label">Your Email</label>
                    <input type="email" class="cb-form-control" id="cb-email" name="email" 
                           value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                           required>
                </div>
                <div class="mb-3">
                    <label for="cb-message" class="cb-form-label">Your Message</label>
                    <textarea class="cb-form-control" id="cb-message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="cb-btn-custom">Send Message</button>
            </form>
        </div>

        <!-- Contact Info Container -->
        <?php if (strtolower($contactInfoSetting) === 'visible'): ?>
        <div class="cb-contact-info-container">
            <h2>Contact Details</h2>
            <p>
                <i class="fas fa-phone cb-contact-info-icon"></i>
                <span><strong>Contact Number:</strong> <?= htmlspecialchars($contactNumber); ?></span>
            </p>
            <p>
                <i class="fab fa-whatsapp cb-contact-info-icon"></i>
                <span><strong>WhatsApp:</strong> <?= htmlspecialchars($whatsappNumber); ?></span>
            </p>
            <p>
                <i class="fas fa-envelope cb-contact-info-icon"></i>
                <span><strong>Email:</strong> <?= htmlspecialchars($emailAddress); ?></span>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
include_once 'footer.php';
?>

<!-- Include Bootstrap Bundle with Popper and additional JS libraries if needed -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $urlval ?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="<?= $urlval ?>admin/asset/vendor/tinymce/tinymce.min.js"></script>
<script>
    // (Your existing JavaScript code for form handling, image preview, TinyMCE, etc.)
</script>
</body>
</html>
