<?php
require_once("../../global.php");
include_once("../header.php");

// Role check: allow only admins (roles 1 and 3) to access this page.
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token.
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Retrieve form values using null coalescing for safety.
        $email_verification      = $_POST['email_verification'] ?? 'Disabled';
        $member_approval         = $_POST['member_approval'] ?? 'Auto';
        $classified_approval     = $_POST['classified_approval'] ?? 'Auto';
        $video_option_throughout = $_POST['video_option_throughout'] ?? 'Disabled';
        $video_option_posting    = $_POST['video_option_posting'] ?? 'Disabled';
        $video_option_home       = $_POST['video_option_home'] ?? 'Disabled';
        $image_option_direct_upload   = $_POST['image_option_direct_upload'] ?? 'Disabled';
        $image_option_imagemagick       = $_POST['image_option_imagemagick'] ?? 'Disabled';
        $image_option_watermark         = $_POST['image_option_watermark'] ?? 'Disabled';
        $seo_mode                = $_POST['seo_mode'] ?? 'Disabled';
        $seo_param_description   = $_POST['seo_param_description'] ?? 'Disabled';
        $seo_param_keyword       = $_POST['seo_param_keyword'] ?? 'Disabled';
        $seo_param_title         = $_POST['seo_param_title'] ?? 'Disabled';
        $seo_param_url           = $_POST['seo_param_url'] ?? 'Disabled';
        $captcha_signup          = $_POST['captcha_signup'] ?? 'Disabled';
        $captcha_login           = $_POST['captcha_login'] ?? 'Disabled';
        $captcha_contact         = $_POST['captcha_contact'] ?? 'Disabled';
        $captcha_lost_password   = $_POST['captcha_lost_password'] ?? 'Disabled';
        $captcha_classified_details = $_POST['captcha_classified_details'] ?? 'Disabled';
        $google_map_status       = $_POST['google_map_status'] ?? 'Disabled';
        $google_map_key          = $_POST['google_map_key'] ?? '';
        $google_map_longitude    = $_POST['google_map_longitude'] ?? 0;
        $google_map_latitude     = $_POST['google_map_latitude'] ?? 0;
        $google_map_height       = $_POST['google_map_height'] ?? 0;
        $contact_information     = $_POST['contact_information'] ?? 'Hidden';
        $extra_shipping_option   = $_POST['extra_shipping_option'] ?? 'Disabled';

        // Update the approval_parameters row (assuming id=1)
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

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve current settings (row id=1)
$stmt = $pdo->prepare("SELECT * FROM approval_parameters WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
// Use default values if no settings found (to avoid warnings)
if (!$settings) {
    $settings = [
        'email_verification' => 'Disabled',
        'member_approval' => 'Auto',
        'classified_approval' => 'Auto',
        'video_option_throughout' => 'Disabled',
        'video_option_posting' => 'Disabled',
        'video_option_home' => 'Disabled',
        'image_option_direct_upload' => 'Disabled',
        'image_option_imagemagick' => 'Disabled',
        'image_option_watermark' => 'Disabled',
        'seo_mode' => 'Disabled',
        'seo_param_description' => 'Disabled',
        'seo_param_keyword' => 'Disabled',
        'seo_param_title' => 'Disabled',
        'seo_param_url' => 'Disabled',
        'captcha_signup' => 'Disabled',
        'captcha_login' => 'Disabled',
        'captcha_contact' => 'Disabled',
        'captcha_lost_password' => 'Disabled',
        'captcha_classified_details' => 'Disabled',
        'google_map_status' => 'Disabled',
        'google_map_key' => '',
        'google_map_longitude' => 0,
        'google_map_latitude' => 0,
        'google_map_height' => 0,
        'contact_information' => 'Hidden',
        'extra_shipping_option' => 'Disabled'
    ];
}
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Approval Parameters</h3>
            <p class="mb-0">These parameters control various aspects of user signups, classifieds posting, video and image options, SEO, CAPTCHA, Google Map settings, and more.</p>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Email Verification -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Verification:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="email_verification" value="Enabled" <?= ($settings['email_verification'] === 'Enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Enabled</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="email_verification" value="Disabled" <?= ($settings['email_verification'] === 'Disabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Disabled</label>
                    </div>
                    <div class="form-text">Sets whether email gets verified before signup or not.</div>
                </div>
                
                <!-- Member Approval -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Member Approval:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="member_approval" value="Admin" <?= ($settings['member_approval'] === 'Admin') ? 'checked' : '' ?>>
                        <label class="form-check-label">Admin</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="member_approval" value="Auto" <?= ($settings['member_approval'] === 'Auto') ? 'checked' : '' ?>>
                        <label class="form-check-label">Auto</label>
                    </div>
                    <div class="form-text">Sets whether admin will approve every new signup or it will be approved automatically.</div>
                </div>
                
                <!-- Classified Approval -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Classified Approval:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="classified_approval" value="Admin" <?= ($settings['classified_approval'] === 'Admin') ? 'checked' : '' ?>>
                        <label class="form-check-label">Admin</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="classified_approval" value="Auto" <?= ($settings['classified_approval'] === 'Auto') ? 'checked' : '' ?>>
                        <label class="form-check-label">Auto</label>
                    </div>
                    <div class="form-text">Sets whether admin will approve every new classified or it will be approved automatically upon posting.</div>
                </div>
                
                <!-- Video Options -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Video Options:</label>
                    <div class="ms-3">
                        <label>Throughout Website:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_throughout" value="Enabled" <?= ($settings['video_option_throughout'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_throughout" value="Disabled" <?= ($settings['video_option_throughout'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Sets whether the video gallery section is enabled on the website or not.</div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Posting Item Page:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_posting" value="Enabled" <?= ($settings['video_option_posting'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_posting" value="Disabled" <?= ($settings['video_option_posting'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Sets whether video uploading on posting classified page is enabled or not.</div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Home Page:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_home" value="Enabled" <?= ($settings['video_option_home'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="video_option_home" value="Disabled" <?= ($settings['video_option_home'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Sets whether video gallery is enabled on the home page or not.</div>
                    </div>
                </div>
                
                <!-- Image Options -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Image Options:</label>
                    <div class="ms-3">
                        <label>Direct Upload:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_direct_upload" value="Enabled" <?= ($settings['image_option_direct_upload'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_direct_upload" value="Disabled" <?= ($settings['image_option_direct_upload'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Sets whether images can be uploaded directly from the post classified page or not.</div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Image Magick:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_imagemagick" value="Enabled" <?= ($settings['image_option_imagemagick'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_imagemagick" value="Disabled" <?= ($settings['image_option_imagemagick'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Automatically resize images using Image Magick (requires Image Magick on the server).</div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Water Marking:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_watermark" value="Enabled" <?= ($settings['image_option_watermark'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="image_option_watermark" value="Disabled" <?= ($settings['image_option_watermark'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                        <div class="form-text">Sets whether to water mark the images using <code>images/watermark.gif</code> under the website root folder.</div>
                    </div>
                </div>
                
                <!-- SEO Mode -->
                <div class="mb-3">
                    <label class="form-label fw-bold">SEO Mode:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="seo_mode" value="Enabled" <?= ($settings['seo_mode'] === 'Enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Enabled</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="seo_mode" value="Disabled" <?= ($settings['seo_mode'] === 'Disabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Disabled</label>
                    </div>
                    <div class="form-text">Sets whether to use URL rewriting for SEO via the .htaccess file.</div>
                </div>
                
                <!-- SEO Parameters -->
                <div class="mb-3">
                    <label class="form-label fw-bold">SEO Parameters:</label>
                    <div class="ms-3">
                        <label>Description:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_description" value="Enabled" <?= ($settings['seo_param_description'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_description" value="Disabled" <?= ($settings['seo_param_description'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Keyword:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_keyword" value="Enabled" <?= ($settings['seo_param_keyword'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_keyword" value="Disabled" <?= ($settings['seo_param_keyword'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Title:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_title" value="Enabled" <?= ($settings['seo_param_title'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_title" value="Disabled" <?= ($settings['seo_param_title'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>URL:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_url" value="Enabled" <?= ($settings['seo_param_url'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="seo_param_url" value="Disabled" <?= ($settings['seo_param_url'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="form-text">Enabling these forces members to input these parameters for SEO purposes.</div>
                </div>
                
                <!-- CAPTCHA Options -->
                <div class="mb-3">
                    <label class="form-label fw-bold">CAPTCHA Options:</label>
                    <div class="ms-3">
                        <label>Signup:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_signup" value="Enabled" <?= ($settings['captcha_signup'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_signup" value="Disabled" <?= ($settings['captcha_signup'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Login:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_login" value="Enabled" <?= ($settings['captcha_login'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_login" value="Disabled" <?= ($settings['captcha_login'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Contact Us:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_contact" value="Enabled" <?= ($settings['captcha_contact'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_contact" value="Disabled" <?= ($settings['captcha_contact'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Lost Password:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_lost_password" value="Enabled" <?= ($settings['captcha_lost_password'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_lost_password" value="Disabled" <?= ($settings['captcha_lost_password'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Classified Details:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_classified_details" value="Enabled" <?= ($settings['captcha_classified_details'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="captcha_classified_details" value="Disabled" <?= ($settings['captcha_classified_details'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                </div>
                
                <!-- Google Map Parameters -->
                <!-- <div class="mb-3">
                    <label class="form-label fw-bold">Google Map Parameters:</label>
                    <div class="ms-3">
                        <label>Status:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="google_map_status" value="Enabled" <?= ($settings['google_map_status'] === 'Enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Enabled</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="google_map_status" value="Disabled" <?= ($settings['google_map_status'] === 'Disabled') ? 'checked' : '' ?>>
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                    <div class="ms-3 mt-2">
                        <label>Key:</label>
                        <input type="text" name="google_map_key" class="form-control" value="<?= htmlspecialchars($settings['google_map_key']) ?>">
                    </div>
                    <div class="ms-3 mt-2 row">
                        <div class="col">
                            <label>Longitude:</label>
                            <input type="text" name="google_map_longitude" class="form-control" value="<?= htmlspecialchars($settings['google_map_longitude']) ?>">
                        </div>
                        <div class="col">
                            <label>Latitude:</label>
                            <input type="text" name="google_map_latitude" class="form-control" value="<?= htmlspecialchars($settings['google_map_latitude']) ?>">
                        </div>
                        <div class="col">
                            <label>Height:</label>
                            <input type="text" name="google_map_height" class="form-control" value="<?= htmlspecialchars($settings['google_map_height']) ?>">
                        </div>
                    </div>
                    <div class="form-text">Sets the google map's center when no values are available.</div>
                </div> -->
                
                <!-- Contact Information -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Contact Information:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="contact_information" value="Visible" <?= ($settings['contact_information'] === 'Visible') ? 'checked' : '' ?>>
                        <label class="form-check-label">Visible</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="contact_information" value="Hidden" <?= ($settings['contact_information'] === 'Hidden') ? 'checked' : '' ?>>
                        <label class="form-check-label">Hidden</label>
                    </div>
                    <div class="form-text">Sets whether to display seller's contact information on the classified details page.</div>
                </div>
                
                <!-- Extra Shipping Option -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Extra Shipping Option:</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="extra_shipping_option" value="Enabled" <?= ($settings['extra_shipping_option'] === 'Enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Enabled</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="extra_shipping_option" value="Disabled" <?= ($settings['extra_shipping_option'] === 'Disabled') ? 'checked' : '' ?>>
                        <label class="form-check-label">Disabled</label>
                    </div>
                    <div class="form-text">Sets whether to show extra shipping cost option on posting, editing and classified description pages.</div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
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
