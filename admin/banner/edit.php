<?php
require_once("../../global.php");
include_once('../header.php');

if (!isset($_GET['bannerid'])) {
    echo "<script>
            alert('Invalid banner ID.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

$pageid = $security->decrypt($_GET['bannerid']);

$page = $dbFunctions->getDataById('banners', $pageid);
if (!$page) {
    echo "<script>
            alert('Banner not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $btn_text = $_POST['btn_text'] ?? '';
    $btn_url = $_POST['btn_url'] ?? '';
    $text_color = $_POST['text_color'] ?? '#FFFFFF';
    $btn_color = $_POST['btn_color'] ?? '#fbbf24';
    $bg_color = $_POST['bg_color'] ?? '#000000';
    $placement = $_POST['placement'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image_url = $security->decrypt($page['image']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../../upload/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_url = 'upload/' . $newFileName;
        } else {
            echo "<script>alert('Error moving the uploaded file.');</script>";
        }
    }

    if (!empty($title) && !empty($description) && !empty($placement)) {
        $updateData = [
            'title' => $title,
            'description' => $description,
            'btn_text' => $btn_text,
            'btn_url' => $btn_url,
            'text_color' => $text_color,
            'btn_color' => $btn_color,
            'bg_color' => $bg_color,
            'placement' => $placement,
            'is_active' => $is_active,
            'image' => $image_url,
        ];

        $updateResult = $dbFunctions->updateData('banners', $updateData, $pageid);
     

        if ($updateResult['success']) {
            echo "<script>
            alert('Banner updated successfully.');
            window.location.href = '".$urlval."admin/banner/index.php';
        </script>";
        } else {
            echo "<script>alert('Error updating banner: {$updateResult['message']}');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<style>
        .error { color: red; }
    .form-container { background-color: #f9f9f9; padding: 20px; border-radius: 8px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; }
    .btn { background-color: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; }
    .btn:hover { background-color: #218838; }
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
                                <label for="title">Title <span style="color: red;">*</span></label>
                                <input type="text" id="title" name="title" value="<?= $security->decrypt(htmlspecialchars($page['title'])) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description <span style="color: red;">*</span></label>
                                <textarea id="description" name="description" rows="4" required><?= $security->decrypt(htmlspecialchars($page['description']) )?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Banner Image</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <?php if (!empty($page['image'])): ?>
                                    <div class="image-preview">
                                        <img src="<?= $urlval . $page['image'] ?>" alt="Current Image" width="100">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="btn_text">Button Text</label>
                                <input type="text" id="btn_text" name="btn_text" value="<?= $security->decrypt(htmlspecialchars($page['btn_text']) )?>">
                            </div>
                            <div class="form-group">
                                <label for="btn_url">Button URL</label>
                                <input type="url" id="btn_url" name="btn_url" value="<?= $security->decrypt(htmlspecialchars($page['btn_url'])) ?>">
                            </div>
                            <div class="form-group">
                                <label for="text_color">Text Color</label>
                                <input type="color" id="text_color" name="text_color" value="<?= $security->decrypt(htmlspecialchars($page['text_color'])) ?>">
                            </div>
                            <div class="form-group">
                                <label for="btn_color">Button Color</label>
                                <input type="color" id="btn_color" name="btn_color" value="<?= $security->decrypt(htmlspecialchars($page['btn_color'])) ?>">
                            </div>
                            <div class="form-group">
                                <label for="bg_color">Background Color</label>
                                <input type="color" id="bg_color" name="bg_color" value="<?= $security->decrypt(htmlspecialchars($page['bg_color'])) ?>">
                            </div>
                            <div class="form-group">
                                <label for="placement">Placement <span style="color: red;">*</span></label>
                                <select id="placement" name="placement" required>
                                    <option value="home_header" <?= $security->decrypt($page['placement']) === 'home_header' ? 'selected' : '' ?>>Home Header</option>
                                    <option value="home_sidebar" <?= $security->decrypt($page['placement']) === 'home_sidebar' ? 'selected' : '' ?>>Home Sidebar</option>
                                    <option value="category_header" <?= $security->decrypt($page['placement']) === 'category_header' ? 'selected' : '' ?>>Category Header</option>
                                    <option value="category_sidebar" <?= $security->decrypt($page['placement']) === 'category_sidebar' ? 'selected' : '' ?>>Category Sidebar</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_active">Active</label>
                                <input type="checkbox" id="is_active" name="is_active" <?= $security->decrypt($page['is_active']) ? 'checked' : '' ?>>
                            </div>
                            <button type="submit" class="btn">Update Banner</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../footer.php'); ?>
