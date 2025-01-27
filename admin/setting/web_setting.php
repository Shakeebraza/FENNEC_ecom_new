<?php
require_once("../../global.php");
include_once('../header.php');

// Detect user role
$role = $_SESSION['role'];
// True if user is either Super Admin (1) or Admin (3)
$isAdmin = in_array($role, [1, 3]);

// Fetch existing settings
$settings = $pdo->query("SELECT * FROM websettings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$message = '';

// If user is Admin or Super Admin, allow POST updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $usernameLength    = $_POST['usernameLength'];
    $passwordLength    = $_POST['passwordLength'];
    $imagesAllowed     = $_POST['imagesAllowed'];
    $freeImages        = $_POST['freeImages'];
    $imageSize         = $_POST['imageSize'];
    $videosAllowed     = $_POST['videosAllowed'];
    $paidImagesPrice   = $_POST['paidImagesPrice'];
    $paidVideosPrice   = $_POST['paidVideosPrice'];
    $duration          = $_POST['duration'];
    $extensionDuration = $_POST['extensionDuration'];

    // Define currency symbols
    $currencySymbols = [
        'USD' => '$',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'JPY' => '¥',
    ];

    $selectedCurrency = $_POST['siteCurrency'];
    $siteCurrency = $currencySymbols[$selectedCurrency] ?? '$';

    $sql = "UPDATE websettings 
            SET username_length = ?, 
                password_length = ?, 
                images_allowed = ?, 
                free_images = ?, 
                image_size = ?, 
                videos_allowed = ?, 
                paid_images_price = ?, 
                paid_videos_price = ?, 
                site_currency = ?, 
                duration = ?, 
                extension_duration = ? 
            WHERE id = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $usernameLength, 
        $passwordLength, 
        $imagesAllowed, 
        $freeImages, 
        $imageSize, 
        $videosAllowed, 
        $paidImagesPrice, 
        $paidVideosPrice, 
        $siteCurrency, 
        $duration, 
        $extensionDuration
    ]);

    if ($stmt->rowCount() > 0) {
        $message = "<div class='alert alert-success'>Settings updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-warning'>No changes were made to the settings.</div>";
    }
}
?>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
}
.card-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
}
.form-label {
    font-weight: bold;
}
.btn-primary {
    background-color: #0056b3;
    border: none;
    transition: background-color 0.3s ease;
}
.btn-primary:hover {
    background-color: #003f8a;
}
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="card shadow-lg border-0">
                    <div class="card-header" style="background: linear-gradient(45deg, #28a745, #218838); color: white;">
                        <h1 class="mb-0 text-center">Website Settings</h1>
                    </div>
                    <div class="card-body p-4" style="background-color: #f9f9f9;">
                        <?= $message ?>

                        <?php
                          // If Moderator (role=4), disable the input fields.
                          // If Admin or Super Admin, leave them enabled.
                          $disabled = $isAdmin ? '' : 'disabled';
                        ?>

                        <form method="POST" action="">
                            <!-- Website Settings Section -->
                            <div class="mb-5">
                                <h3 style="color: #28a745; border-bottom: 2px solid silver; padding-bottom: 8px;">
                                    Website Settings
                                </h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="usernameLength" class="form-label">Username Length</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="usernameLength" name="usernameLength" <?= $disabled ?>
                                               placeholder="Minimum characters required"
                                               value="<?= htmlspecialchars($settings['username_length'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="passwordLength" class="form-label">Password Length</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="passwordLength" name="passwordLength" <?= $disabled ?>
                                               placeholder="Minimum characters required"
                                               value="<?= htmlspecialchars($settings['password_length'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Image Settings Section -->
                            <div class="mb-5">
                                <h3 style="color: #28a745; border-bottom: 2px solid silver; padding-bottom: 8px;">
                                    Image Settings
                                </h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="imagesAllowed" class="form-label">Images Allowed</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="imagesAllowed" name="imagesAllowed" <?= $disabled ?>
                                               placeholder="Maximum images allowed"
                                               value="<?= htmlspecialchars($settings['images_allowed'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="freeImages" class="form-label">Free Images</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="freeImages" name="freeImages" <?= $disabled ?>
                                               placeholder="Free images allowed"
                                               value="<?= htmlspecialchars($settings['free_images'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="imageSize" class="form-label">Image Size (Bytes)</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="imageSize" name="imageSize" <?= $disabled ?>
                                               placeholder="Maximum size in bytes"
                                               value="<?= htmlspecialchars($settings['image_size'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="paidImagesPrice" class="form-label">Price for Paid Images ($)</label>
                                        <input type="number" step="0.01" class="form-control border-secondary"
                                               id="paidImagesPrice" name="paidImagesPrice" <?= $disabled ?>
                                               placeholder="Price for each additional image"
                                               value="<?= htmlspecialchars($settings['paid_images_price'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Video Settings Section -->
                            <div class="mb-5">
                                <h3 style="color: #28a745; border-bottom: 2px solid silver; padding-bottom: 8px;">
                                    Video Settings
                                </h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="videosAllowed" class="form-label">Videos Allowed</label>
                                        <input type="number" class="form-control border-secondary"
                                               id="videosAllowed" name="videosAllowed" <?= $disabled ?>
                                               placeholder="Maximum videos allowed"
                                               value="<?= htmlspecialchars($settings['videos_allowed'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="paidVideosPrice" class="form-label">Price for Paid Videos ($)</label>
                                        <input type="number" step="0.01" class="form-control border-secondary"
                                               id="paidVideosPrice" name="paidVideosPrice" <?= $disabled ?>
                                               placeholder="Price for each additional video"
                                               value="<?= htmlspecialchars($settings['paid_videos_price'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Currency and Duration Section -->
                            <div class="mb-4">
                                <h3 style="color: #28a745; border-bottom: 2px solid silver; padding-bottom: 8px;">
                                    Currency and Duration
                                </h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="siteCurrency" class="form-label">Site Currency</label>
                                        <select class="form-control border-secondary" id="siteCurrency" name="siteCurrency" <?= $disabled ?>>
                                            <option value="USD" <?= ($settings['site_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
                                            <option value="AUD" <?= ($settings['site_currency'] ?? '') === 'AUD' ? 'selected' : '' ?>>AUD</option>
                                            <option value="CAD" <?= ($settings['site_currency'] ?? '') === 'CAD' ? 'selected' : '' ?>>CAD</option>
                                            <option value="JPY" <?= ($settings['site_currency'] ?? '') === 'JPY' ? 'selected' : '' ?>>JPY</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="duration" class="form-label">Duration</label>
                                        <input type="text" class="form-control border-secondary"
                                               id="duration" name="duration" <?= $disabled ?>
                                               placeholder="Enter duration (e.g., 30 days)"
                                               value="<?= htmlspecialchars($settings['duration'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="extensionDuration" class="form-label">Extension Duration</label>
                                        <input type="text" class="form-control border-secondary"
                                               id="extensionDuration" name="extensionDuration" <?= $disabled ?>
                                               placeholder="Enter extension duration (e.g., 15 days)"
                                               value="<?= htmlspecialchars($settings['extension_duration'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Only show "Save Changes" button to Admin or Super Admin -->
                            <?php if ($isAdmin): ?>
                                <div class="text-center">
                                    <button type="submit" 
                                            class="btn" 
                                            style="background-color: #28a745; color: white; border: 1px solid silver; padding: 10px 20px;">
                                        Save Changes
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- Moderator sees this message or sees just the fields disabled -->
                                <div class="alert alert-info text-center">
                                    You have read-only access to these settings.
                                </div>
                            <?php endif; ?>
                        </form>
                    </div><!-- card-body -->
                </div><!-- card -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php
include_once('../footer.php');
?>
