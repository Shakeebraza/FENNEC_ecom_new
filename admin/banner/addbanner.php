<?php
require_once("../../global.php");
include_once('../header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $heading = $_POST['productName'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_url = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));


        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../../upload/'; 
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_url = 'upload/'.$newFileName; 
        } else {
            echo "<script>alert('There was an error moving the uploaded file.');</script>";
        }
    } else {
        echo "<script>alert('Error: No file uploaded or file upload error.');</script>";
    }

    if (!empty($heading) && !empty($description) && !empty($image_url)) {
        $addNewData = [
            'title' => $heading,
            'description' => $description,
            'image_url' => $image_url,
            'status' => 1,
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
    .error {
        color: red;
    }

    .form-container {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .btn {
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #218838;
    }

    .image-preview {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px; 
    }

    .image-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin-right: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Add New Banner</h1>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="productName">Title <span style="color: red;">*</span></label>
                                <input type="text" id="productName" name="productName" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description <span style="color: red;">*</span></label>
                                <textarea id="description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Banner Image <span style="color: red;">*</span></label>
                                <input type="file" id="image" name="image" accept="image/*" required>
                            </div>

                            <div class="image-preview" id="imagePreview"></div>

                            <button type="submit" class="btn">Add Banner</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../footer.php');
?>

<script>
    document.getElementById('image').addEventListener('change', function(event) {
        var files = event.target.files;
        var preview = document.getElementById('imagePreview');
        preview.innerHTML = '';  
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
