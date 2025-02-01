<?php
require_once("../../global.php");
include_once('../header.php');

// 1) Check role
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3])) {
    echo "<script>
        alert('Access Denied. You do not have permission to edit banners.');
        window.location.href = '".$urlval."admin/banner/index.php';
    </script>";
    exit;
}

// 2) Confirm banner ID
if (!isset($_GET['bannerid'])) {
    echo "<script>
        alert('Invalid banner ID.');
        window.location.href = '".$urlval."admin/banner/index.php';
    </script>";
    exit;
}

$bannerIdDecrypted = $security->decrypt($_GET['bannerid']);
$banner = $dbFunctions->getDataById('banners', $bannerIdDecrypted);
if (!$banner) {
    echo "<script>
        alert('Banner not found.');
        window.location.href = '".$urlval."admin/banner/index.php';
    </script>";
    exit;
}

// 3) Decrypt fields you want to show in the form
$decryptedTitle       = $security->decrypt($banner['title']);
$decryptedDescription = $security->decrypt($banner['description']);
$decryptedBtnText     = $security->decrypt($banner['btn_text']);
$decryptedBtnUrl      = $security->decrypt($banner['btn_url']);
$decryptedTextColor   = $security->decrypt($banner['text_color']);
$decryptedBtnColor    = $security->decrypt($banner['btn_color']);
$decryptedBgColor     = $security->decrypt($banner['bg_color']);
$decryptedImage       = $security->decrypt($banner['image']);
$decryptedPlacement   = $security->decrypt($banner['placement']);
$decryptedIsActive    = $security->decrypt($banner['is_active']);

// 4) If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title']       ?? '';
    $description = $_POST['description'] ?? '';
    $btn_text    = $_POST['btn_text']    ?? '';
    $btn_url     = $_POST['btn_url']     ?? '';
    $text_color  = $_POST['text_color']  ?? '#FFFFFF';
    $btn_color   = $_POST['btn_color']   ?? '#fbbf24';
    $bg_color    = $_POST['bg_color']    ?? '#000000';
    $placement   = $_POST['placement']   ?? '';
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    // If user didn't upload a new image, keep the old
    $image_url = $decryptedImage;

    // If user uploaded a new file
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath    = $_FILES['image']['tmp_name'];
        $fileName       = $_FILES['image']['name'];
        $fileNameCmps   = explode(".", $fileName);
        $fileExtension  = strtolower(end($fileNameCmps));
        $newFileName    = md5(time() . $fileName) . '.' . $fileExtension;

        $uploadFileDir  = '../../upload/';
        $dest_path      = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_url = 'upload/' . $newFileName;
        } else {
            echo "<script>alert('Error moving the uploaded file.');</script>";
        }
    }

    // If required fields present
    if (!empty($title) && !empty($description) && !empty($placement)) {
        // Re-encrypt each field if your DB truly needs encryption
        // e.g. if the DB expects the stored data to be ciphertext:
        $updateData = [
            'title'       => $security->encrypt($title),
            'description' => $security->encrypt($description),
            'btn_text'    => $security->encrypt($btn_text),
            'btn_url'     => $security->encrypt($btn_url),
            'text_color'  => $security->encrypt($text_color),
            'btn_color'   => $security->encrypt($btn_color),
            'bg_color'    => $security->encrypt($bg_color),
            'placement'   => $security->encrypt($placement),
            'is_active'   => $security->encrypt($is_active),
            'image'       => $security->encrypt($image_url),
        ];

        $updateResult = $dbFunctions->updateData('banners', $updateData, $bannerIdDecrypted);

        if ($updateResult['success']) {
            echo "<script>
                alert('Banner updated successfully.');
                window.location.href = '".$urlval."admin/banner/index.php';
            </script>";
        } else {
            echo "<script>alert('Error updating banner: ".$updateResult['message']."');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>
<style>
.form-container {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
}

.btn {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
}

.btn:hover {
    background-color: #218838;
}
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Edit Banner</h1>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Title <span style="color:red;">*</span></label>
                                <input type="text" id="title" name="title"
                                    value="<?= htmlspecialchars($decryptedTitle) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description <span style="color:red;">*</span></label>
                                <textarea id="description" name="description" rows="4"
                                    required><?= htmlspecialchars($decryptedDescription) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Banner Image</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <?php if (!empty($decryptedImage)): ?>
                                <div class="image-preview" style="margin-top:10px;">
                                    <img src="<?= $urlval . $decryptedImage ?>" alt="Current Image" width="100">
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="btn_text">Button Text</label>
                                <input type="text" id="btn_text" name="btn_text"
                                    value="<?= htmlspecialchars($decryptedBtnText) ?>">
                            </div>
                            <div class="form-group">
                                <label for="btn_url">Button URL</label>
                                <input type="url" id="btn_url" name="btn_url"
                                    value="<?= htmlspecialchars($decryptedBtnUrl) ?>">
                            </div>
                            <div class="form-group">
                                <label for="text_color">Text Color</label>
                                <input type="color" id="text_color" name="text_color"
                                    value="<?= htmlspecialchars($decryptedTextColor) ?>">
                            </div>
                            <div class="form-group">
                                <label for="btn_color">Button Color</label>
                                <input type="color" id="btn_color" name="btn_color"
                                    value="<?= htmlspecialchars($decryptedBtnColor) ?>">
                            </div>
                            <div class="form-group">
                                <label for="bg_color">Background Color</label>
                                <input type="color" id="bg_color" name="bg_color"
                                    value="<?= htmlspecialchars($decryptedBgColor) ?>">
                            </div>
                            <div class="form-group">
                                <label for="placement">Placement <span style="color:red;">*</span></label>
                                <select id="placement" name="placement" required>
                                    <option value="home_header"
                                        <?= $decryptedPlacement=='home_header'     ? 'selected' : '' ?>>Home Header
                                    </option>
                                    <option value="home_sidebar"
                                        <?= $decryptedPlacement=='home_sidebar'    ? 'selected' : '' ?>>Home Sidebar
                                    </option>
                                    <option value="category_header"
                                        <?= $decryptedPlacement=='category_header' ? 'selected' : '' ?>>Category Header
                                    </option>
                                    <option value="category_sidebar"
                                        <?= $decryptedPlacement=='category_sidebar'? 'selected' : '' ?>>Category Sidebar
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_active">Active</label>
                                <input type="checkbox" id="is_active" name="is_active"
                                    <?= ($decryptedIsActive==1) ? 'checked' : '' ?>>
                            </div>
                            <button type="submit" class="btn">Update Banner</button>
                        </form>
                    </div><!-- container -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('../footer.php'); ?>