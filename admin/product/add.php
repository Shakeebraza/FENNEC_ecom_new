<?php
require_once("../../global.php");
include_once('../header.php');
include_once('style.php');
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
                        <h1>Add New Product</h1>
                        <!-- Success Alert -->
                        <div id="successAlert" class="sufee-alert alert with-close alert-success alert-dismissible fade show" style="display: none;">
                            <span class="badge badge-pill badge-success">Success</span>
                            You successfully read this important alert.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>


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

                        <form method="POST" enctype="multipart/form-data" class="container" id="productForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label">Product Name <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="productName" name="productName" required>
                                    <div class="text-danger" id="productNameError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug" required>
                                    <div class="text-danger" id="slugError"></div> <!-- Error message -->
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image <span style="color: red;">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <div class="text-danger" id="imageError"></div> <!-- Error message -->
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span style="color: red;">*</span></label>
                                <textarea id="description" name="description" rows="4" class="form-control" required></textarea>
                                <div class="text-danger" id="descriptionError"></div> <!-- Error message -->
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span style="color: red;">*</span></label>
                                    <select id="category" name="category" class="form-select" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <?php
                                        foreach ($category as $cat) {
                                            echo '<option value="' . $security->decrypt($cat['id']) . '">' . $security->decrypt($cat['category_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="text-danger" id="categoryError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcategory" class="form-label">Sub Category <span style="color: red;">*</span></label>
                                    <select id="subcategory" name="subcategory" class="form-select" required>
                                        <option value="" disabled selected>Select a sub category</option>
                                    </select>
                                    <div class="text-danger" id="subcategoryError"></div> <!-- Error message -->
                                </div>
                            </div>

                            <!-- Brand and Condition -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand" class="form-label">Brand <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="brand" name="brand" required>
                                    <div class="text-danger" id="brandError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="condition" class="form-label">Condition <span style="color: red;">*</span></label>
                                    <select id="condition" name="condition" class="form-select" required>
                                        <option value="" disabled selected>Select condition</option>
                                        <option value="new">New</option>
                                        <option value="used">Used</option>
                                    </select>
                                    <div class="text-danger" id="conditionError"></div> <!-- Error message -->
                                </div>
                            </div>

                            <!-- Gallery Images -->
                            <div class="form-group">
                                <label for="gallery" class="custom-file-upload">
                                    <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                                    Upload Gallery Images
                                </label>
                                <div id="imagePreview" class="image-preview"></div>
                                <div class="text-danger" id="galleryError"></div> <!-- Error message -->
                            </div>

                            <!-- Country and City -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
    <label for="country" class="form-label">Country <span style="color: red;">*</span></label>
    <select id="country" name="country" class="form-select">
        <option value="" disabled selected>Select a country</option>
        <?php
        foreach ($countries as $cont) {
            echo '<option value="' . $security->decrypt($cont['id']) . '">' . $security->decrypt($cont['name']) . '</option>';
        }
        ?>
    </select>
    <div class="text-danger" id="countryError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City <span style="color: red;">*</span></label>
                                    <select id="city" name="city" class="form-select">
                                        <option value="" disabled selected>Select a city</option>
                                    </select>
                                    <div class="text-danger" id="cityError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="aera" class="form-label">Area <span style="color: red;">*</span></label>
                                    <select id="aera" name="aera" class="form-select">
                                        <option value="" disabled selected>Select an area</option>
                                    </select>
                                    <div class="text-danger" id="aeraError"></div> <!-- Error message -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" >
                                    <div class="text-danger" id="priceError"></div> <!-- Error message -->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="discountPrice" class="form-label">Discount Price <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="discountPrice" name="discountPrice" step="0.01" >
                                    <div class="text-danger" id="discountPriceError"></div> <!-- Error message -->
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btnsubmit">Add Product</button>
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
                    data: { country_id: countryId },
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
                    data: { city_id: cityId },
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
                let isValid = true;
                const productName = $('#productName').val().trim();
                const slug = $('#slug').val().trim();
                const image = $('#image')[0].files[0];
                const description = $('#description').val().trim();
                const category = $('#category').val();
                const subcategory = $('#subcategory').val();
                const brand = $('#brand').val().trim();
                const condition = $('#condition').val();
                const country = $('#country').val();
                const city = $('#city').val();
                const price = $('#price').val().trim();
                const discountPrice = $('#discountPrice').val().trim();
                if (productName === '') {
                    $('#productNameError').text('Product name is required.');
                    isValid = false;
                }
                if (slug === '') {
                    $('#slugError').text('Slug is required.');
                    isValid = false;
                }
                if (!image) {
                    $('#imageError').text('Image is required.');
                    isValid = false;
                }
                if (description === '') {
                    $('#descriptionError').text('Description is required.');
                    isValid = false;
                }
                if (!category) {
                    $('#categoryError').text('Category is required.');
                    isValid = false;
                }
                if (!subcategory) {
                    $('#subcategoryError').text('Subcategory is required.');
                    isValid = false;
                }
                if (brand === '') {
                    $('#brandError').text('Brand is required.');
                    isValid = false;
                }
                if (!condition) {
                    $('#conditionError').text('Condition is required.');
                    isValid = false;
                }
                if (!country) {
                    $('#countryError').text('Country is required.');
                    isValid = false;
                }
                if (!city) {
                    $('#cityError').text('City is required.');
                    isValid = false;
                }
                if (price === '') {
                    $('#priceError').text('Price is required.');
                    isValid = false;
                }
                if (discountPrice === '') {
                    $('#discountPriceError').text('Discount price is required.');
                    isValid = false;
                }
                if (!isValid) {
                    return;
                }
                var formData = new FormData(this);

                $.ajax({
                    url: '<?= $urlval?>admin/ajax/product/addproduct.php', 
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
</script>

</body>

</html>