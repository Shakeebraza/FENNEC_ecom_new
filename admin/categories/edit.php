<?php
require_once("../../global.php");
include_once('../header.php');
if (!isset($_GET['catid'])) {
    echo "<script>
            alert('Invalid menu ID.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}


$catid = $security->decrypt($_GET['catid']);

$cat = $dbFunctions->getDataById('categories', $catid); 


if (!$cat) {
    echo "<script>
            alert('Menu not found.');
            window.location.href = '".$urlval."admin/index.php'; 
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $icon = $_POST['selected_icon'] ?? '';
    $status = $_POST['status'] ?? '';
    

    $image = $_FILES['image'] ?? null;
    $imagePath = '';

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
 
        $imagePath = $fun->uploadImage($image);
    }

    $addNewData = [
        'category_name' => $heading,
        'slug' => $slug,
        'icon' => $icon,
        'category_image' => $imagePath, 
        'is_enable' => $status,
    ];

    $updateResult = $dbFunctions->setData('categories', $addNewData,['id' => $catid]);

    if ($updateResult['success']) {
        echo "<script>alert('Page added successfully.');</script>";
    } else {
        echo "<script>alert('Error adding page: {$updateResult['message']}');</script>";
    }
}



?>
<style>
    svg {
        display: none;
    }

    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .dropdown-button {
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 10px;
        cursor: pointer;
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        z-index: 1;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
    }

    .dropdown-content div {
        padding: 10px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .dropdown-content div:hover {
        background-color: #f1f1f1;
    }

    .dropdown-content div i {
        margin-right: 10px;
    }
</style>



<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Edit Categories</h1>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="heading">Name</label>
                                <input type="text" id="heading" name="heading" class="form-control" value="<?= $security->decrypt($cat['category_name'] )?>" required>
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <input type="text" id="slug" name="slug" class="form-control" value="<?= $security->decrypt($cat['slug'] )?>" required readonly>
                            </div>

                            <div class="form-group">
                                <label for="iconSelector">Select an Icon</label>
                                <div class="custom-dropdown">
                                    <div class="dropdown-button" id="dropdownButton">
                                        <span id="selectedIconDisplay">Select an Icon</span> <i class="fas fa-caret-down"></i>
                                    </div>
                                    <div class="dropdown-content" id="dropdownContent">
                                        <div data-value="fas fa-car"><i class="fas fa-car"></i> Car</div>
                                        <div data-value="fas fa-tshirt"><i class="fas fa-tshirt"></i> Clothing</div>
                                        <div data-value="fas fa-laptop"><i class="fas fa-laptop"></i> Laptop</div>
                                        <div data-value="fas fa-mobile-alt"><i class="fas fa-mobile-alt"></i> Mobile</div>
                                        <div data-value="fas fa-headphones"><i class="fas fa-headphones"></i> Headphones</div>
                                        <div data-value="fas fa-tv"><i class="fas fa-tv"></i> Television</div>
                                        <div data-value="fas fa-camera"><i class="fas fa-camera"></i> Camera</div>
                                        <div data-value="fas fa-book"><i class="fas fa-book"></i> Book</div>
                                        <!-- Add more options as needed -->
                                    </div>
                                </div>
                                <input type="hidden" name="selected_icon" id="selectedIcon" value="<?= $security->decrypt($cat['icon']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option disabled value="">Select Status</option>
                                    <option value="1" <?= $security->decrypt($cat['slug']) == 1 ? "selected" : "" ?>>Activate</option>
                                    <option value="0" <?= $security->decrypt($cat['slug']) == 0 ? "selected" : "" ?>>Decline</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image">Upload Image</label>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                                <div id="imagePreview" class="mt-2">
                                    <!-- Display the existing image if available -->
                                    <img id="preview" src="<?php echo $urlval . $security->decrypt($cat['category_image']); ?>" alt="Image Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc;">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa fa-dot-circle-o"></i> Submit
                                </button>
                                <button type="reset" class="btn btn-danger btn-sm">
                                    <i class="fa fa-ban"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



<?php
include_once('../footer.php');
?>
<script src="<?php echo $urlval ?>admin/asset/js/textaera.js"></script>
<script>
    document.getElementById('heading').addEventListener('input', function() {
        const slugField = document.getElementById('slug');
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        slugField.value = slug;
    });
    const selectedIcon = document.getElementById('selectedIcon').value;
    const selectedIconDisplay = document.getElementById('selectedIconDisplay');
    const dropdownContent = document.getElementById('dropdownContent');
    
    // Display the decrypted value icon on page load
    if (selectedIcon) {
        const selectedIconElement = document.querySelector(`[data-value="${selectedIcon}"]`);
        if (selectedIconElement) {
            selectedIconDisplay.innerHTML = selectedIconElement.innerHTML; // Show the icon text and icon
        }
    }



    dropdownButton.addEventListener('click', function() {
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    });


    dropdownContent.addEventListener('click', function(e) {
        if (e.target.dataset.value) {
            const selectedValue = e.target.dataset.value;
            dropdownButton.innerHTML = `${e.target.innerHTML} <i class="fas fa-caret-down"></i>`;
            selectedIconInput.value = selectedValue; 
            dropdownContent.style.display = 'none';
            console.log("Selected Icon Class:", selectedValue); 
        }
    });

    window.addEventListener('click', function(event) {
        if (!event.target.matches('.dropdown-button')) {
            dropdownContent.style.display = 'none';
        }
    });

    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('preview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    }
</script>

</body>

</html>