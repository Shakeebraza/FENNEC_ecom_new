<?php
require_once("../../global.php");
include_once('../header.php');
include_once('style.php');

// Validate slug parameter
if (!isset($_GET['slug'])) {
    echo "<script>
            alert('Invalid slug ID.');
            window.location.href = '" . $urlval . "admin/index.php'; 
          </script>";
    exit;
}

$slug = $_GET['slug'];

// Retrieve product details
$product = $productFun->getProductDetailsBySlugnew($slug);

if (!$product) {
    echo "<script>
            alert('Slug not found.');
            window.location.href = '" . $urlval . "admin/index.php'; 
          </script>";
    exit;
}

// Retrieve dropdown data
$category = $dbFunctions->getData('categories', 'is_enable = 1');
$subcategories = $dbFunctions->getData('subcategories', 'is_enable = 1');
$countries = $dbFunctions->getData('countries');

?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="container form-container">
                        <h1>Edit Ads</h1>

                        <!-- Success Alert -->
                        <div id="successAlert" class="sufee-alert alert with-close alert-success alert-dismissible fade show" style="display: none;">
                            <span class="badge badge-pill badge-success">Success</span>
                            You successfully read this important alert.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- Error Alert -->
                        <div id="errorAlert" class="sufee-alert alert with-close alert-danger alert-dismissible fade show" style="display: none;">
                            <span class="badge badge-pill badge-danger">Error</span>
                            Something went wrong. Please try again.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="error"><?= $security->decrypt($error) ?></div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form method="POST" enctype="multipart/form-data" class="container" id="productForm">
                            <!-- Ads Details -->
                             <input type="hidden" name="productId" value="<?=$product["product"]['product_id']?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label">Ads Name <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="productName" name="productName" value="<?= htmlspecialchars($product["product"]['product_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($product["product"]['slug'] ?? '') ?>" required>
                                </div>
                            </div>

                     

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span style="color: red;">*</span></label>
                                <textarea id="description" name="description" rows="4" class="form-control" required><?= htmlspecialchars($product['product']['product_description']) ?></textarea>
                            </div>

                            <!-- Dropdown Fields for Category and Subcategory -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span style="color: red;">*</span></label>
                                    <select id="category" name="category" class="form-select" required>
                                        <option value="" disabled>Select a category</option>
                                        <?php foreach ($category as $cat): ?>
                                            <option value="<?= $security->decrypt($cat['id']) ?>" <?= ($security->decrypt($cat['id']) == $product['product']['category_id']) ? 'selected' : '' ?>>
                                                <?= $security->decrypt($cat['category_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcategory" class="form-label">Sub Category <span style="color: red;">*</span></label>
                                    <select id="subcategory" name="subcategory" class="form-select" required>
                                        <option value="" disabled>Select a sub category</option>
                                        <?php foreach ($subcategories as $subcat): ?>
                                            <option value="<?= $security->decrypt($subcat['id']) ?>" <?= ($security->decrypt($subcat['id']) == $product['product']['subcategory_id']) ? 'selected' : '' ?>>
                                                <?= $security->decrypt($subcat['subcategory_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand" class="form-label">Brand <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($product['product']['brand']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="condition" class="form-label">Condition <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="condition" name="condition" value="<?= htmlspecialchars($product['product']['conditions']) ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gallery" class="custom-file-upload">
                                    <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                                    Upload Gallery Images
                                </label>
                                <div id="imagePreview" class="image-preview">
                                    <?php
                                    foreach($product['gallery_images'] as $productImage) {
                                      
                                        echo '<div class="image-container" style="display: inline-block; position: relative; margin: 10px;">
                                            <img src="' . $urlval.$productImage['image_path'] . '" alt="Gallery Image" style="width: 100px; height: 100px; object-fit: cover;">
                                            <button class="delete-image" data-image-id="' . $productImage['image_id'] . '" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; border-radius: 50%; padding: 5px;">X</button>
                                        </div>';
                                    }
                                    ?>
                                </div>
                                <div class="text-danger" id="galleryError"></div> 
                            </div>

                            <!-- Price -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['product']['price']) ?>" step="0.01" required>
                                </div>
                            
                                <div class="col-md-6">
                                    <label for="membershipType" class="form-label">Select Membership Type</label>
                                    <select id="membershipType" name="membershipType" class="form-control" required>
                                        <option value="standard" <?= $product['product']['product_type'] === 'standard' ? 'selected' : '' ?>>Standard</option>
                                        <option value="gold" <?= $product['product']['product_type'] === 'gold' ? 'selected' : '' ?>>Gold</option>
                                        <option value="premium" <?= $product['product']['product_type'] === 'premium' ? 'selected' : '' ?>>Premium</option>
                                    </select>
                                </div>
                            </div>


                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btnsubmit">Update Ads</button>
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

<script src="<?php echo $urlval ?>admin/asset/js/textaera.js"></script>
<script>
    document.getElementById('productName').addEventListener('input', function() {
        const slugField = document.getElementById('slug');
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        slugField.value = slug;
    });

    document.getElementById('gallery').addEventListener('change', function(event) {
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.innerHTML = '';

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.style.position = 'relative';
                const img = document.createElement('img');
                img.src = e.target.result;
                imgContainer.appendChild(img);

                const removeButton = document.createElement('button');
                removeButton.innerText = 'X';
                removeButton.style.position = 'absolute';
                removeButton.style.top = '0px';
                removeButton.style.right = '10px';
                removeButton.style.background = 'red';
                removeButton.style.color = 'white';
                removeButton.style.border = 'none';
                removeButton.style.borderRadius = '5px';
                removeButton.style.padding = '3px';
                removeButton.style.cursor = 'pointer';
                removeButton.onclick = function() {
                    imgContainer.remove();
                }

                imgContainer.appendChild(removeButton);
                imagePreview.appendChild(imgContainer);
            }

            reader.readAsDataURL(file);
        }
    });

    $(document).ready(function() {
        $('#country').on('change', function() {
            var countryId = $(this).val();

            if (countryId) {
                $.ajax({
                    url: '<?php echo $urlval ?>admin/ajax/product/get_cities.php',
                    type: 'POST',
                    data: {
                        country_id: countryId
                    },
                    success: function(data) {
                        $('#city').html(data); // Populate cities dropdown
                        $('#aera').html('<option value="" disabled selected>Select an area</option>'); // Reset areas dropdown
                    },
                    error: function() {
                        alert('Error fetching cities. Please try again.');
                    }
                });
            } else {
                $('#city').html('<option value="" disabled selected>Select a city</option>');
                $('#aera').html('<option value="" disabled selected>Select an area</option>');
            }
        });

        // Handle city change
        $('#city').on('change', function() {
            var cityId = $(this).val();

            if (cityId) {
                $.ajax({
                    url: '<?php echo $urlval ?>admin/ajax/product/get_areas.php',
                    type: 'POST',
                    data: {
                        city_id: cityId
                    },
                    success: function(data) {
                        $('#aera').html(data);
                    },
                    error: function() {
                        alert('Error fetching areas. Please try again.');
                    }
                });
            } else {
                $('#aera').html('<option value="" disabled selected>Select an area</option>');
            }
        });
        $('#category').on('change', function() {
            var catId = $(this).val();

            if (catId) {
                $.ajax({
                    url: '<?php echo $urlval ?>admin/ajax/product/get_subcat.php',
                    type: 'POST',
                    data: {
                        catId: catId
                    },
                    success: function(data) {
                        $('#subcategory').html(data);
                    },
                    error: function() {
                        alert('Error fetching cities. Please try again.');
                    }
                });
            } else {
                $('#subcategory').html('<option value="" disabled selected>Select a city</option>');
            }
        });

        $(document).ready(function() {

    $('#productForm').on('submit', function(e) {
        e.preventDefault(); 


        $('.text-danger').text('');

    
        var formData = new FormData(this);

        
        $.ajax({
            url: '<?= $urlval ?>admin/ajax/product/updateproduct.php',
            type: 'POST',
            data: formData,
            processData: false, 
            contentType: false, 
            success: function(response) {
                if (response.success) {
                    
                    showSuccessAlert();
                } else {
                    
                    if (response.errors) {
                        if (response.errors.productName) {
                            $('#productNameError').text(response.errors.productName);
                        }
                        if (response.errors.slug) {
                            $('#slugError').text(response.errors.slug);
                        }
                        if (response.errors.image) {
                            $('#imageError').text(response.errors.image);
                        }
                        if (response.errors.description) {
                            $('#descriptionError').text(response.errors.description);
                        }
                        if (response.errors.category) {
                            $('#categoryError').text(response.errors.category);
                        }
                        if (response.errors.subcategory) {
                            $('#subcategoryError').text(response.errors.subcategory);
                        }
                        if (response.errors.brand) {
                            $('#brandError').text(response.errors.brand);
                        }
                        if (response.errors.condition) {
                            $('#conditionError').text(response.errors.condition);
                        }
                        if (response.errors.country) {
                            $('#countryError').text(response.errors.country);
                        }
                        if (response.errors.city) {
                            $('#cityError').text(response.errors.city);
                        }
                        if (response.errors.price) {
                            $('#priceError').text(response.errors.price);
                        }
                        if (response.errors.discountPrice) {
                            $('#discountPriceError').text(response.errors.discountPrice);
                        }
                    }
                }
            },
            error: function() {
             
                showErrorAlert();
            }
        });
    });


    function showSuccessAlert() {
        const successAlert = document.getElementById('successAlert');
        successAlert.style.display = 'block';
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 3000); 
    }


    function showErrorAlert() {
        const errorAlert = document.getElementById('errorAlert');
        errorAlert.style.display = 'block';
        setTimeout(() => {
            errorAlert.style.display = 'none';
        }, 3000); 
    }


});

    });

    document.addEventListener('DOMContentLoaded', function() {

const deleteButtons = document.querySelectorAll('.delete-image');

deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const imageId = this.getAttribute('data-image-id');
        

        if (confirm('Are you sure you want to delete this image?')) {
            deleteImage(imageId, this);
        }
    });
});
});

function deleteImage(imageId, button) {

const xhr = new XMLHttpRequest();
xhr.open('POST', '<?= $urlval?>admin/ajax/product/delete-image.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {

        const response = JSON.parse(xhr.responseText);
        if (response.success) {
            button.closest('.image-container').remove();
        } else {
            alert('Error deleting image.');
        }
    }
};

xhr.send('image_id=' + imageId);
}

</script>

</body>

</html>