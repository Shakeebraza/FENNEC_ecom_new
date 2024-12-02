<?php
require_once("../../global.php");
include_once('../header.php');


$settings = $pdo->query("SELECT * FROM websettings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameLength = $_POST['usernameLength'];
    $passwordLength = $_POST['passwordLength'];
    $imagesAllowed = $_POST['imagesAllowed'];
    $freeImages = $_POST['freeImages'];
    $imageSize = $_POST['imageSize'];
    $videosAllowed = $_POST['videosAllowed'];
    $paidImagesPrice = $_POST['paidImagesPrice'];
    $paidVideosPrice = $_POST['paidVideosPrice'];
    $duration = $_POST['duration'];
    $extensionDuration = $_POST['extensionDuration'];

  
    $currencySymbols = [
        'USD' => '$',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'JPY' => '¥',
    ];


    $selectedCurrency = $_POST['siteCurrency'];
    $siteCurrency = $currencySymbols[$selectedCurrency] ?? '$'; 

    $sql = "UPDATE websettings 
            SET username_length = ?, password_length = ?, images_allowed = ?, free_images = ?, image_size = ?, 
                videos_allowed = ?, paid_images_price = ?, paid_videos_price = ?, 
                site_currency = ?, duration = ?, extension_duration = ? 
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

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <h1 class="mb-4">Website Settings</h1>

                <?= $message ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="usernameLength" class="form-label">Username Length</label>
                        <input type="number" class="form-control" id="usernameLength" name="usernameLength"
                            placeholder="Minimum characters required"
                            value="<?= htmlspecialchars($settings['username_length'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="passwordLength" class="form-label">Password Length</label>
                        <input type="number" class="form-control" id="passwordLength" name="passwordLength"
                            placeholder="Minimum characters required"
                            value="<?= htmlspecialchars($settings['password_length'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="imagesAllowed" class="form-label">Images Allowed</label>
                        <input type="number" class="form-control" id="imagesAllowed" name="imagesAllowed"
                            placeholder="Maximum images allowed"
                            value="<?= htmlspecialchars($settings['images_allowed'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="freeImages" class="form-label">Free Images</label>
                        <input type="number" class="form-control" id="freeImages" name="freeImages"
                            placeholder="Free images allowed"
                            value="<?= htmlspecialchars($settings['free_images'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="imageSize" class="form-label">Image Size (Bytes)</label>
                        <input type="number" class="form-control" id="imageSize" name="imageSize"
                            placeholder="Maximum size in bytes"
                            value="<?= htmlspecialchars($settings['image_size'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="videosAllowed" class="form-label">Videos Allowed</label>
                        <input type="number" class="form-control" id="videosAllowed" name="videosAllowed"
                            placeholder="Maximum videos allowed"
                            value="<?= htmlspecialchars($settings['videos_allowed'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="paidImagesPrice" class="form-label">Price for Paid Images ($)</label>
                        <input type="number" step="0.01" class="form-control" id="paidImagesPrice"
                            name="paidImagesPrice" placeholder="Price for each additional image"
                            value="<?= htmlspecialchars($settings['paid_images_price'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="paidVideosPrice" class="form-label">Price for Paid Videos ($)</label>
                        <input type="number" step="0.01" class="form-control" id="paidVideosPrice"
                            name="paidVideosPrice" placeholder="Price for each additional video"
                            value="<?= htmlspecialchars($settings['paid_videos_price'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="siteCurrency" class="form-label">Site Currency</label>
                        <select class="form-control" id="siteCurrency" name="siteCurrency">
                            <option value="USD" <?= ($settings['site_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
                            <option value="AUD" <?= ($settings['site_currency'] ?? '') === 'AUD' ? 'selected' : '' ?>>AUD</option>
                            <option value="CAD" <?= ($settings['site_currency'] ?? '') === 'CAD' ? 'selected' : '' ?>>CAD</option>
                            <option value="JPY" <?= ($settings['site_currency'] ?? '') === 'JPY' ? 'selected' : '' ?>>JPY</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <input type="text" class="form-control" id="duration" name="duration"
                            placeholder="Enter duration (e.g., 30 days)"
                            value="<?= htmlspecialchars($settings['duration'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="extensionDuration" class="form-label">Extension Duration</label>
                        <input type="text" class="form-control" id="extensionDuration" name="extensionDuration"
                            placeholder="Enter extension duration (e.g., 15 days)"
                            value="<?= htmlspecialchars($settings['extension_duration'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
include_once('../footer.php');
?>