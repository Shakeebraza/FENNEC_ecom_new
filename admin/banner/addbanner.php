<?php
require_once("../../global.php");
include_once('../header.php');

// 1) Check the logged-in user’s role
$role = $_SESSION['role'] ?? 0;
// 2) Decide if this user can add banners
$isAdmin = in_array($role, [1,3]); // 1=Super Admin, 3=Admin

// If you want to also allow role=4 to add, just include it in the array above.
?>

<?php if (!$isAdmin): ?>
    <!-- If NOT Admin/Super Admin => show a read-only or error message -->
    <div class="page-container">
        <div class="main-content">
            <div class="container-fluid">
                <h1>Access Denied</h1>
                <p>You do not have permission to add banners.</p>
            </div>
        </div>
    </div>
    <?php include_once('../footer.php'); ?>
    </body>
    </html>
    <?php
    exit;
endif;
?>

<!-- If we reach here, the user IS Admin or Super Admin. Show the normal form: -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title']       ?? '';
    $description = $_POST['description'] ?? '';
    $btn_text    = $_POST['btn_text']    ?? '';
    $btn_url     = $_POST['btn_url']     ?? '';
    $text_color  = $_POST['text_color']  ?? '#FFFFFF';
    $btn_color   = $_POST['btn_color']   ?? '#fbbf24';
    $bg_color    = $_POST['bg_color']    ?? '#000000';
    $placement   = $_POST['placement']   ?? 'home_header';
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image_url   = '';

    // Handle image upload
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
            echo "<script>alert('There was an error moving the uploaded file.');</script>";
        }
    } else {
        echo "<script>alert('Error: No file uploaded or file upload error.');</script>";
    }

    // Basic validation
    if (!empty($title) && !empty($description) && !empty($image_url)) {
        $addNewData = [
            'title'       => $title,
            'description' => $description,
            'image'       => $image_url,
            'btn_text'    => $btn_text,
            'btn_url'     => $btn_url,
            'text_color'  => $text_color,
            'btn_color'   => $btn_color,
            'bg_color'    => $bg_color,
            'placement'   => $placement,
            'is_active'   => $is_active,
        ];

        $updateResult = $dbFunctions->setData('banners', $addNewData);

        if ($updateResult['success']) {
            echo "<script>alert('Banner added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding banner: {$updateResult['message']}');</script>";
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
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 10px; border: 1px solid #ccc;
    }
    .btn {
        background-color: #28a745; color: white; padding: 10px 15px;
        border: none; cursor: pointer;
    }
    .btn:hover { background-color: #218838; }
</style>

<div class="page-container">
    <div class="main-content">
        <div class="container-fluid">
            <div class="form-container">
                <h1>Add New Banner</h1>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title <span style="color: red;">*</span></label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description <span style="color: red;">*</span></label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Banner Image <span style="color: red;">*</span></label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="btn_text">Button Text</label>
                        <input type="text" id="btn_text" name="btn_text">
                    </div>
                    <div class="form-group">
                        <label for="btn_url">Button URL</label>
                        <input type="url" id="btn_url" name="btn_url">
                    </div>
                    <div class="form-group">
                        <label for="text_color">Text Color</label>
                        <input type="color" id="text_color" name="text_color" value="#FFFFFF">
                    </div>
                    <div class="form-group">
                        <label for="btn_color">Button Color</label>
                        <input type="color" id="btn_color" name="btn_color" value="#fbbf24">
                    </div>
                    <div class="form-group">
                        <label for="bg_color">Background Color</label>
                        <input type="color" id="bg_color" name="bg_color" value="#000000">
                    </div>
                    <div class="form-group">
                        <label for="placement">Placement <span style="color: red;">*</span></label>
                        <select id="placement" name="placement" required>
                            <option value="home_header">Home Header</option>
                            <option value="home_sidebar">Home Sidebar</option>
                            <option value="category_header">Category Header</option>
                            <option value="category_sidebar">Category Sidebar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Active</label>
                        <input type="checkbox" id="is_active" name="is_active" checked>
                    </div>
                    <button type="submit" class="btn">Add Banner</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('../footer.php'); ?>
