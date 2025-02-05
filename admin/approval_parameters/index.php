<?php
require_once("../../global.php");
include_once("../header.php");

// Role check: allow only admins (roles 1, 3) to access this page.
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Retrieve form values (using ternary operators for safety)
        $email_verification = $_POST['email_verification'] ?? 'Disabled';
        $member_approval = $_POST['member_approval'] ?? 'Auto';
        $classified_approval = $_POST['classified_approval'] ?? 'Auto';
        $video_option_throughout = $_POST['video_option_throughout'] ?? 'Disabled';
        $video_option_posting = $_POST['video_option_posting'] ?? 'Disabled';
        $video_option_home = $_POST['video_option_home'] ?? 'Disabled';
        $image_option_direct_upload = $_POST['image_option_direct_upload'] ?? 'Disabled';
        $image_option_imagemagick = $_POST['image_option_imagemagick'] ?? 'Disabled';
        $image_option_watermark = $_POST['image_option_watermark'] ?? 'Disabled';
        $seo_mode = $_POST['seo_mode'] ?? 'Disabled';
        $seo_param_description = $_POST['seo_param_description'] ?? 'Disabled';
        $seo_param_keyword = $_POST['seo_param_keyword'] ?? 'Disabled';
        $seo_param_title = $_POST['seo_param_title'] ?? 'Disabled';
        $seo_param_url = $_POST['seo_param_url'] ?? 'Disabled';
        $captcha_signup = $_POST['captcha_signup'] ?? 'Disabled';
        $captcha_login = $_POST['captcha_login'] ?? 'Disabled';
        $captcha_contact = $_POST['captcha_contact'] ?? 'Disabled';
        $captcha_lost_password = $_POST['captcha_lost_password'] ?? 'Disabled';
        $captcha_classified_details = $_POST['captcha_classified_details'] ?? 'Disabled';
        $google_map_status = $_POST['google_map_status'] ?? 'Disabled';
        $google_map_key = $_POST['google_map_key'] ?? '';
        $google_map_longitude = $_POST['google_map_longitude'] ?? 0;
        $google_map_latitude = $_POST['google_map_latitude'] ?? 0;
        $google_map_height = $_POST['google_map_height'] ?? 0;
        $contact_information = $_POST['contact_information'] ?? 'Hidden';
        $extra_shipping_option = $_POST['extra_shipping_option'] ?? 'Disabled';

        // Update the settings row (assuming id=1)
        $stmt = $pdo->prepare("UPDATE approval_parameters SET 
            email_verification = ?,
            member_approval = ?,
            classified_approval = ?,
            video_option_throughout = ?,
            video_option_posting = ?,
            video_option_home = ?,
            image_option_direct_upload = ?,
            image_option_imagemagick = ?,
            image_option_watermark = ?,
            seo_mode = ?,
            seo_param_description = ?,
            seo_param_keyword = ?,
            seo_param_title = ?,
            seo_param_url = ?,
            captcha_signup = ?,
            captcha_login = ?,
            captcha_contact = ?,
            captcha_lost_password = ?,
            captcha_classified_details = ?,
            google_map_status = ?,
            google_map_key = ?,
            google_map_longitude = ?,
            google_map_latitude = ?,
            google_map_height = ?,
            contact_information = ?,
            extra_shipping_option = ?,
            updated_at = ?
            WHERE id = 1
        ");
        $result = $stmt->execute([
            $email_verification,
            $member_approval,
            $classified_approval,
            $video_option_throughout,
            $video_option_posting,
            $video_option_home,
            $image_option_direct_upload,
            $image_option_imagemagick,
            $image_option_watermark,
            $seo_mode,
            $seo_param_description,
            $seo_param_keyword,
            $seo_param_title,
            $seo_param_url,
            $captcha_signup,
            $captcha_login,
            $captcha_contact,
            $captcha_lost_password,
            $captcha_classified_details,
            $google_map_status,
            $google_map_key,
            $google_map_longitude,
            $google_map_latitude,
            $google_map_height,
            $contact_information,
            $extra_shipping_option,
            date("Y-m-d H:i:s")
        ]);
        if ($result) {
            $message = "Settings updated successfully.";
        } else {
            $csrfError = "Failed to update settings.";
        }
    }
}

// Generate a new CSRF token for the form.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve current settings (row id=1)
$stmt = $pdo->prepare("SELECT * FROM approval_parameters WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h3>Approval Parameters</h3>
    <p>
        These parameters control various aspects of user signups, classifieds posting, video and image options, SEO, CAPTCHA, Google Map settings, and more.
    </p>
    <?php if (!empty($csrfError)): ?>
        <div class="alert alert-danger" id="alert-message"><?php echo htmlspecialchars($csrfError); ?></div>
    <?php elseif (!empty($message)): ?>
        <div class="alert alert-success" id="alert-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <!-- Email Verification -->
        <div class="mb-3">
            <label class="form-label"><strong>Email Verification:</strong></label><br>
            <label><input type="radio" name="email_verification" value="Enabled" <?php echo ($settings['email_verification'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="email_verification" value="Disabled" <?php echo ($settings['email_verification'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            <div class="form-text">Sets whether email gets verified before signup or not.</div>
        </div>
        
        <!-- Member Approval -->
        <div class="mb-3">
            <label class="form-label"><strong>Member Approval:</strong></label><br>
            <label><input type="radio" name="member_approval" value="Admin" <?php echo ($settings['member_approval'] === 'Admin') ? 'checked' : ''; ?>> Admin</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="member_approval" value="Auto" <?php echo ($settings['member_approval'] === 'Auto') ? 'checked' : ''; ?>> Auto</label>
            <div class="form-text">Sets whether admin will approve every new signup or it will be approved automatically.</div>
        </div>
        
        <!-- Classified Approval -->
        <div class="mb-3">
            <label class="form-label"><strong>Classified Approval:</strong></label><br>
            <label><input type="radio" name="classified_approval" value="Admin" <?php echo ($settings['classified_approval'] === 'Admin') ? 'checked' : ''; ?>> Admin</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="classified_approval" value="Auto" <?php echo ($settings['classified_approval'] === 'Auto') ? 'checked' : ''; ?>> Auto</label>
            <div class="form-text">Sets whether admin will approve every new classified or it will be approved automatically upon posting.</div>
        </div>
        
        <!-- Video Options -->
        <div class="mb-3">
            <label class="form-label"><strong>Video Options:</strong></label>
            <div class="ms-3">
                <label>Throughout Website:</label><br>
                <label><input type="radio" name="video_option_throughout" value="Enabled" <?php echo ($settings['video_option_throughout'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="video_option_throughout" value="Disabled" <?php echo ($settings['video_option_throughout'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Sets whether the video gallery section is enabled on the website or not.</div>
            </div>
            <div class="ms-3 mt-2">
                <label>Posting Item Page:</label><br>
                <label><input type="radio" name="video_option_posting" value="Enabled" <?php echo ($settings['video_option_posting'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="video_option_posting" value="Disabled" <?php echo ($settings['video_option_posting'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Sets whether video uploading on posting classified page is enabled or not.</div>
            </div>
            <div class="ms-3 mt-2">
                <label>Home Page:</label><br>
                <label><input type="radio" name="video_option_home" value="Enabled" <?php echo ($settings['video_option_home'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="video_option_home" value="Disabled" <?php echo ($settings['video_option_home'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Sets whether video gallery is enabled on the home page or not.</div>
            </div>
        </div>
        
        <!-- Image Options -->
        <div class="mb-3">
            <label class="form-label"><strong>Image Options:</strong></label>
            <div class="ms-3">
                <label>Direct Upload:</label><br>
                <label><input type="radio" name="image_option_direct_upload" value="Enabled" <?php echo ($settings['image_option_direct_upload'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="image_option_direct_upload" value="Disabled" <?php echo ($settings['image_option_direct_upload'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Sets whether images can be uploaded directly from the post classified page or not.</div>
            </div>
            <div class="ms-3 mt-2">
                <label>Image Magick:</label><br>
                <label><input type="radio" name="image_option_imagemagick" value="Enabled" <?php echo ($settings['image_option_imagemagick'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="image_option_imagemagick" value="Disabled" <?php echo ($settings['image_option_imagemagick'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Automatically resize images using Image Magick (requires Image Magick on the server).</div>
            </div>
            <div class="ms-3 mt-2">
                <label>Water Marking:</label><br>
                <label><input type="radio" name="image_option_watermark" value="Enabled" <?php echo ($settings['image_option_watermark'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="image_option_watermark" value="Disabled" <?php echo ($settings['image_option_watermark'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
                <div class="form-text">Sets whether to water mark the images using <code>images/watermark.gif</code> under the website root folder.</div>
            </div>
        </div>
        
        <!-- SEO Mode -->
        <div class="mb-3">
            <label class="form-label"><strong>SEO Mode:</strong></label><br>
            <label><input type="radio" name="seo_mode" value="Enabled" <?php echo ($settings['seo_mode'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="seo_mode" value="Disabled" <?php echo ($settings['seo_mode'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            <div class="form-text">Sets whether to use URL rewriting for SEO via the .htaccess file.</div>
        </div>
        
        <!-- SEO Parameters -->
        <div class="mb-3">
            <label class="form-label"><strong>SEO Parameters:</strong></label>
            <div class="ms-3">
                <label>Description:</label><br>
                <label><input type="radio" name="seo_param_description" value="Enabled" <?php echo ($settings['seo_param_description'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="seo_param_description" value="Disabled" <?php echo ($settings['seo_param_description'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Keyword:</label><br>
                <label><input type="radio" name="seo_param_keyword" value="Enabled" <?php echo ($settings['seo_param_keyword'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="seo_param_keyword" value="Disabled" <?php echo ($settings['seo_param_keyword'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Title:</label><br>
                <label><input type="radio" name="seo_param_title" value="Enabled" <?php echo ($settings['seo_param_title'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="seo_param_title" value="Disabled" <?php echo ($settings['seo_param_title'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>URL:</label><br>
                <label><input type="radio" name="seo_param_url" value="Enabled" <?php echo ($settings['seo_param_url'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="seo_param_url" value="Disabled" <?php echo ($settings['seo_param_url'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="form-text">Enabling these forces members to input these parameters for SEO purposes.</div>
        </div>
        
        <!-- CAPTCHA Options -->
        <div class="mb-3">
            <label class="form-label"><strong>CAPTCHA Options:</strong></label>
            <div class="ms-3">
                <label>Signup:</label><br>
                <label><input type="radio" name="captcha_signup" value="Enabled" <?php echo ($settings['captcha_signup'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="captcha_signup" value="Disabled" <?php echo ($settings['captcha_signup'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Login:</label><br>
                <label><input type="radio" name="captcha_login" value="Enabled" <?php echo ($settings['captcha_login'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="captcha_login" value="Disabled" <?php echo ($settings['captcha_login'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Contact Us:</label><br>
                <label><input type="radio" name="captcha_contact" value="Enabled" <?php echo ($settings['captcha_contact'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="captcha_contact" value="Disabled" <?php echo ($settings['captcha_contact'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Lost Password:</label><br>
                <label><input type="radio" name="captcha_lost_password" value="Enabled" <?php echo ($settings['captcha_lost_password'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="captcha_lost_password" value="Disabled" <?php echo ($settings['captcha_lost_password'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Classified Details:</label><br>
                <label><input type="radio" name="captcha_classified_details" value="Enabled" <?php echo ($settings['captcha_classified_details'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="captcha_classified_details" value="Disabled" <?php echo ($settings['captcha_classified_details'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
        </div>
        
        <!-- Google Map Parameters -->
        <div class="mb-3">
            <label class="form-label"><strong>Google Map Parameters:</strong></label>
            <div class="ms-3">
                <label>Status:</label><br>
                <label><input type="radio" name="google_map_status" value="Enabled" <?php echo ($settings['google_map_status'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="google_map_status" value="Disabled" <?php echo ($settings['google_map_status'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            </div>
            <div class="ms-3 mt-2">
                <label>Key:</label>
                <input type="text" name="google_map_key" class="form-control" value="<?php echo htmlspecialchars($settings['google_map_key']); ?>">
            </div>
            <div class="ms-3 mt-2 row">
                <div class="col">
                    <label>Longitude:</label>
                    <input type="text" name="google_map_longitude" class="form-control" value="<?php echo htmlspecialchars($settings['google_map_longitude']); ?>">
                </div>
                <div class="col">
                    <label>Latitude:</label>
                    <input type="text" name="google_map_latitude" class="form-control" value="<?php echo htmlspecialchars($settings['google_map_latitude']); ?>">
                </div>
                <div class="col">
                    <label>Height:</label>
                    <input type="text" name="google_map_height" class="form-control" value="<?php echo htmlspecialchars($settings['google_map_height']); ?>">
                </div>
            </div>
            <div class="form-text">Sets the google map's center when no values are available.</div>
        </div>
        
        <!-- Contact Information -->
        <div class="mb-3">
            <label class="form-label"><strong>Contact Information:</strong></label><br>
            <label><input type="radio" name="contact_information" value="Visible" <?php echo ($settings['contact_information'] === 'Visible') ? 'checked' : ''; ?>> Visible</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="contact_information" value="Hidden" <?php echo ($settings['contact_information'] === 'Hidden') ? 'checked' : ''; ?>> Hidden</label>
            <div class="form-text">Sets whether to display seller's contact information on the classified details page.</div>
        </div>
        
        <!-- Extra Shipping Option -->
        <div class="mb-3">
            <label class="form-label"><strong>Extra Shipping Option:</strong></label><br>
            <label><input type="radio" name="extra_shipping_option" value="Enabled" <?php echo ($settings['extra_shipping_option'] === 'Enabled') ? 'checked' : ''; ?>> Enabled</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="extra_shipping_option" value="Disabled" <?php echo ($settings['extra_shipping_option'] === 'Disabled') ? 'checked' : ''; ?>> Disabled</label>
            <div class="form-text">Sets whether to show extra shipping cost option on posting, editing and classified description pages.</div>
        </div>
        
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Auto-hide alert messages after 5 seconds.
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
});
</script>

<?php include_once("../footer.php"); ?>
