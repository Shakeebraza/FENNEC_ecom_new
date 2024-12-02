<?php
require_once("../../global.php");
include_once('../header.php');

// Fetch existing settings (assuming single entry with ID = 1)
$settings = $pdo->query("SELECT * FROM websettings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$message = ''; // Initialize an empty message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameLength = $_POST['usernameLength'];
    $passwordLength = $_POST['passwordLength'];
    $imagesAllowed = $_POST['imagesAllowed'];
    $freeImages = $_POST['freeImages'];
    $imageSize = $_POST['imageSize'];
    $videosAllowed = $_POST['videosAllowed'];
    $paidImagesPrice = $_POST['paidImagesPrice'];
    $paidVideosPrice = $_POST['paidVideosPrice'];
    $adminEmail = $_POST['adminEmail'];
    $recordsPerPage = $_POST['recordsPerPage'];

    // Update the existing record
    $sql = "UPDATE websettings 
            SET username_length = ?, password_length = ?, images_allowed = ?, free_images = ?, image_size = ?, 
                videos_allowed = ?, paid_images_price = ?, paid_videos_price = ?, admin_email = ?, records_per_page = ? 
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
        $adminEmail, 
        $recordsPerPage
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

                <!-- Display message above the form -->
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
                        <label for="adminEmail" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="adminEmail" name="adminEmail"
                            placeholder="Enter admin email"
                            value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="recordsPerPage" class="form-label">Records Per Page</label>
                        <input type="number" class="form-control" id="recordsPerPage" name="recordsPerPage"
                            placeholder="Maximum records per page"
                            value="<?= htmlspecialchars($settings['records_per_page'] ?? '') ?>">
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