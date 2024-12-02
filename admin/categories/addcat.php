<?php
require_once("../../global.php");
include_once('../header.php');

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

    $updateResult = $dbFunctions->setData('categories', $addNewData);

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
                        <h1>Add New Categories</h1>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="heading">Name</label>
                                <input type="text" id="heading" name="heading" class="form-control" value="" required>
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <input type="text" id="slug" name="slug" class="form-control" value="" required readonly>
                            </div>

                            <div class="form-group">
                                <label for="iconSelector">Select an Icon</label>
                                <div class="custom-dropdown">
                                    <div class="dropdown-button" id="dropdownButton">
                                        Select an Icon <i class="fas fa-caret-down"></i>
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
                                        <div data-value="fas fa-bicycle"><i class="fas fa-bicycle"></i> Bicycle</div>
                                        <div data-value="fas fa-basketball-ball"><i class="fas fa-basketball-ball"></i> Sports</div>
                                        <div data-value="fas fa-futbol"><i class="fas fa-futbol"></i> Soccer</div>
                                        <div data-value="fas fa-gift"><i class="fas fa-gift"></i> Gift</div>
                                        <div data-value="fas fa-paint-brush"><i class="fas fa-paint-brush"></i> Art Supplies</div>
                                        <div data-value="fas fa-tools"><i class="fas fa-tools"></i> Tools</div>
                                        <div data-value="fas fa-utensils"><i class="fas fa-utensils"></i> Kitchen</div>
                                        <div data-value="fas fa-plane"><i class="fas fa-plane"></i> Travel</div>
                                        <div data-value="fas fa-paw"><i class="fas fa-paw"></i> Pets</div>
                                        <div data-value="fas fa-baby-carriage"><i class="fas fa-baby-carriage"></i> Baby Products</div>
                                        <div data-value="fas fa-tshirt"><i class="fas fa-tshirt"></i> Fashion</div>
                                        <div data-value="fas fa-couch"><i class="fas fa-couch"></i> Furniture</div>
                                        <div data-value="fas fa-bolt"><i class="fas fa-bolt"></i> Electronics</div>
                                        <div data-value="fas fa-lightbulb"><i class="fas fa-lightbulb"></i> Home Decor</div>
                                    </div>
                                </div>
                                <input type="hidden" name="selected_icon" id="selectedIcon">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option disabled value="">Select Status</option>
                                    <option value="1">Activate</option>
                                    <option value="0">Decline</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="image">Upload Image</label>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                                <div id="imagePreview" class="mt-2" style="display:none;">
                                    <img id="preview" src="" alt="Image Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc;">
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

    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownContent = document.getElementById('dropdownContent');
    const selectedIconInput = document.getElementById('selectedIcon');


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