<?php
require_once 'global.php';

if(!isset($_SESSION['userid'])){
    header('Location: index.php');
    exit();
}
$countries = $dbFunctions->getData('countries');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Your Ad</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= $urlval ?>custom/css/poststyle.css">
</head>
<style>
.custom-file-upload {
    display: flex;
    padding: 20px;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: black;
    border-radius: 5px;
    text-align: center;
    transition: background-color 0.3s;

    width: 100%;
}

.form-container .form-group:hover {
    background-color: #2624243b;
}

.custom-file-upload input[type="file"] {
    display: none;
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

.form-container h1 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 30px;
    color: #333;
}


.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.form-group.full-width {
    display: block;
    margin-right: 0;
}

#image,
#gallery {
    width: 100%;
}


.form-container .form-group {
    display: flex;
    justify-content: space-between;
    border: 1px solid black;
    border-radius: 5px;
}


input:hover,
select:hover,
textarea:hover,
input:focus,
select:focus,
textarea:focus {

    border-color: #f39c12;
    box-shadow: 0px 0px 5px rgba(243, 156, 18, 0.5);
    outline: none;
}

.pdt-ads {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 15px !important;
}

#step1 h2 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px !important;
}

#step1 h6 {
    font-size: 14px;
    margin-bottom: 15px !important;
    font-weight: 600;
}

.pst-inpt-serc {
    margin-top: 20px;
    margin-bottom: 0px;
}

.pst-inpt-serc input {
    width: 300px;
    height: 44px;
    line-height: 20px;
    padding: 4px 8px;
    border: 1px solid #d8d6d9;
    border-radius: 4px;
    font-size: 16px;
    margin-bottom: 20px;
}

.pst-inpt-serc input:focus,
.pst-inpt-serc input:hover {
    box-shadow: none !important;
    border: 1px solid #d8d6d9 !important;
}

.pst-inpt-serc input::placeholder {
    font-size: 14px;
    color: #b1adb3;
}

.container {
    max-width: 1000px !important;
}

#step2 {
    margin-top: 0px !important;
    border: 1px solid #d8d6d9 !important;
    border-radius: 0px !important;
    max-width: 1000px !important;
    width: 100% !important;
    min-width: 1000px !important;
    margin-left: -24px;
    padding: 40px !important;
    background-color: white !important;
}


.sb-cytr-opt .sbrct-prere {
    padding: 15px !important;
    border: none;
    border-right: 1px solid #d8d6d9 !important;
}

.category-btn {
    background-color: white !important;
    border: 1px solid #d8d6d9 !important;
    color: #333 !important;
    text-align: center;
    padding: 10px;
    border-radius: 0px !important
}

.ct-mtb-mn {
    padding: 0px !important;
}

.sbrct-prere::after {
    content: "\f054";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-left: 20px;
    color: #000;
    display: inline-block;
    font-size: 12px;
}

.sbrct-prere.active::after {
    content: "\f00c";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #28a745;
    font-size: 14px;
}

#step3 {
    padding: 80px 0px;
    background-color: white !important;
}

input#gallery {
    height: 50px !important;
    width: 50px !important;
    padding: 60px !important;
    background: url("custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce\ \(1\).png") no-repeat;
    background-size: contain;
    background-position: center;
    background-color: #ECEDEF;

}

.upld-free-imag {
    display: flex;
    align-items: center;
    justify-content: center;
}

input#gallery {
    -webkit-appearance: none;
    appearance: none;
    opacity: 0;
    position: absolute;
    z-index: -1;
}

.fdfadfbfhfkj {
    padding: 20px;
    background: #ECEDEF;
    border-radius: 10px;
    border: 1px dashed bl;
}

.fdfadfbfhfkj img {
    height: 50px !important;
    width: 50px !important;
}

.hidden321 {
    display: none;
}
</style>

<body>

    <nav class="navbar navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php" style="text-decoration: none;">
                <?php
    $logoData = $fun->getBox('box1');
    $logo = $urlval . $logoData[0]['image'];
    $title = $logoData[0]['heading'];
    $phara = $logoData[0]['phara'];
    ?>
                <img src="<?php echo $logo ?>" alt="Fennec Logo"
                    style="max-width: 50px; height: auto; margin-right: 10px;" />
                <span style="font-size: 1.7rem; font-weight: bold; color: inherit;"><?= $title ?></span>
            </a>

            <a class="btn btn-outline-secondary" href="<?= $urlval?>">Back to home</a>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4 pdt-ads">POST YOUR AD</h1>

        <div id="step1">
            <h2 class=" mb-4">Choose a Category</h2>
            <h6>Tell us what category you are posting in</h6>

            <div class="row g-4 justify-content-center">
                <div class="pst-inpt-serc">
                    <input type="text" id="categorySearch" placeholder="e.g. Cars, Sofas, Bikes, Laptops"
                        oninput="filterCategories()">
                </div>
            </div>
            <div class="row g-4 justify-content-center" id="categoryContainer">
                <?php
    $findCate = $categoryManager->getAllCategoriesHeaderMenu();
    if ($findCate['status'] == 'success') {
        foreach ($findCate['data'] as $category) {
            echo '
            <div class="col-md-2 ct-mtb-mn category-item" data-name="' . strtolower($category['category_name']) . '">
                <div class="category-btn w-100" onclick="selectCategory(`' . $category['category_name'] . '`, `' . $category['id'] . '`)">
                    <i class="fas ' . $category['icon'] . '"></i><br>' . $category['category_name'] . '
                </div>
            </div>
            ';
        }
    }
    ?>
            </div>

        </div>
        <div class="container">
            <div id="step2" class="hidden">
                <h2 class=" mb-4">Choose a Subcategory for <span id="selectedCategory"></span></h2>
                <div class="row d-block  sb-cytr-opt " id="subcategoryOptions"></div>
                <!-- <button class="btn btn-secondary" onclick="goBackToCategory()">Back</button> -->
            </div>
        </div>


        <div id="step3" class="hidden">
            <h2 class="text-center mb-4">Post an ad </h2>
            <form id="productForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" id="finalCategory" name="finalCategory" readonly>
                    <input type="hidden" id="finalCategoryId" name="category">
                </div>
                <div class="mb-3">
                    <label class="form-label">Subcategory</label>
                    <input type="text" class="form-control" id="finalSubcategory" name="finalSubcategory" readonly>
                    <input type="hidden" id="finalSubcategoryId" name="subcategory">
                </div>


                <div class="form-group mb-3"
                    style="padding: 20px;border: 1px dashed #d8d6d9 ;border-radius: 10px;box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <div class="upld-free-imag d-flex w-100">
                        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                    </div>
                    <label for="gallery" class="custom-file-upload D-FLEX">
                        <div class="fdfadfbfhfkj">
                            <img src="custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce (1).png" alt="">
                        </div>
                    </label>
                    <div id="imagePreview" class="image-preview"></div>
                    <div class="text-danger" id="galleryError"></div>
                </div>
                <p>
                    <a href="#" id="youtube-link">Click to open YouTube video input</a>
                </p>




                <div class="mb-3 hidden321" id="input-container">
                    <label for="youtube_url" class="form-label">Enter youtube URL:</label>
                    <input type="utl" class="form-control" id="youtube_url" name="youtube_url">
                </div>

                <div class="mb-3">
                    <label for="brand" class="form-label">Brand<span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="brand" name="brand" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="condition" class="form-label">Condition <span style="color: red;">*</span></label>
                    <select id="condition" name="condition" class="form-select" required>
                        <option value="" disabled selected>Select condition</option>
                        <option value="new">New</option>
                        <option value="used">Used</option>
                    </select>
                    <div class="text-danger" id="conditionError"></div> 
                </div>
                <div class="mb-3">
                    <label for="adTitle" class="form-label">Ad Title<span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="adTitle" name="productName" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description<span style="color: red;">*</span>
                    </label>
                    <textarea id="description" name="description"></textarea>
                    <div id="wordCounter" style="margin-top: 5px; font-size: 0.9em; color: #555;">
                        0 / 200 words
                    </div>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country<span style="color: red;">*</span></label>
                    <select class="form-select" id="country" name="country" required>
                        <option value="" disabled>Select Country</option>
                        <?php foreach($countries as $val): ?>
                        <option value="<?= $security->decrypt($val['id']) ?>"><?= $security->decrypt($val['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <select class="form-select" id="city" name="city">
                        <option value="" disabled>Select City</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="aera" class="form-label">Area</label>
                    <select class="form-select" id="aera" name="aera">
                        <option value="" disabled>Select Area</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price<span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" value="0">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $_SESSION['email']?>"
                        required>
                </div>

                <div class="mb-3"
                    style="padding: 20px; border: 2px solid #007bff; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f0f8ff;">
                    <h5>Select Package</h5>
                    <div>
                        <!-- Free Package Option -->
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="boostPlan" id="packageFree"
                                value="standard" checked>
                            <label class="form-check-label" for="packageFree">
                                Free Package
                            </label>
                        </div>

                        <!-- Dynamically Loaded Boost Plans -->
                        <?php
                        $boostPlans = $fun->getBoostPlans(); 
                        if (!empty($boostPlans)) : ?>
                        <?php foreach ($boostPlans as $plan) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="boostPlan"
                                id="package_<?= $plan['id'] ?>" value="<?= $plan['id'] ?>">
                            <label class="form-check-label" for="package_<?= $plan['id'] ?>">
                                <?=$plan['name'] ?> - <?= $plan['price'] ?> USD
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>No packages available.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <h5>Image Packages</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="freeImages" value="free" disabled checked>
                        <label class="form-check-label" for="freeImages">
                            Free Images Allowed: <?= $fun->getFieldData('free_images'); ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="extraImages" id="extraImages" value="6">
                        <label class="form-check-label" for="extraImages">
                            Add <?= $fun->getFieldData('images_allowed'); ?> More Images for
                            <?= $fun->getFieldData('paid_images_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <h5>Video Packages</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="extraVideos" id="extraVideos" value="1">
                        <label class="form-check-label" for="extraVideos">
                            Add <?= $fun->getFieldData('videos_allowed'); ?> Video for
                            <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <h5>Website Redirect</h5>
                    <div class="form-check">

                        <input class="form-check-input website-redict" type="checkbox" name="website-redict"
                            id="websiteRedict" value="1">
                        <label class="form-check-label" for="extraVideos">
                            Add <?= $fun->getFieldData('videos_allowed'); ?> Webiste url for
                            <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>

                    <div id="urlInputField" style="display: none; margin-top: 10px;">
                        <label for="redirectUrl">Enter Redirect URL:</label>
                        <input type="url" class="form-control" id="redirectUrl" name="redirectUrl"
                            placeholder="https://example.com">
                    </div>
                </div>
                <div class="mb-3">
                    <h5>Payment Options</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentOption" id="paypal" value="paypal">
                        <label class="form-check-label" for="paypal">
                            PayPal
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentOption" id="bitcoin" value="bitcoin">
                        <label class="form-check-label" for="bitcoin">
                            Bitcoin
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentOption" id="onlinePayment"
                            value="online">
                        <label class="form-check-label" for="onlinePayment">
                            Online Transfer
                        </label>
                    </div>
                </div>


                <div class="btn-main-div" style="display: flex;justify-content: space-between;">
                    <button type="submit" class="btn btn-primary post-btn">Post Ad</button>
                    <button type="button" class="btn btn-secondary" onclick="goBackToSubcategory()">Back</button>
                    <div class="success-message" style="display: none; margin-top: 10px; color: green;"></div>
                </div>

            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $urlval ?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $urlval?>admin/asset/vendor/tinymce/tinymce.min.js"></script>
    <script>
    function selectCategory(categoryName, categoryId) {

        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('selectedCategory').innerText = categoryName;
        document.getElementById('finalCategory').value = categoryName;
        document.getElementById('finalCategoryId').value = categoryId;

        const subcategoryOptions = document.getElementById('subcategoryOptions');
        subcategoryOptions.innerHTML = '';

        if (categoryId) {
            fetch('<?php echo $urlval ?>admin/ajax/product/get_catjson.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'catId=' + encodeURIComponent(categoryId)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    try {
                        const parsedData = JSON.parse(data);
                        if (parsedData.status === 'success') {
                            parsedData.data.forEach(subcategory => {
                                const colDiv = document.createElement('div');
                                colDiv.className = 'col-md-3';
                                colDiv.innerHTML =
                                    `<div class=" sbrct-prere w-100" onclick="selectSubcategory('${subcategory.name}', '${subcategory.id}')">${subcategory.name}</div>`;
                                subcategoryOptions.appendChild(colDiv);

                            });
                        } else {
                            alert(parsedData.message);
                        }
                    } catch (error) {
                        alert('Error parsing JSON: ' + error.message);
                    }
                })
                .catch(error => {
                    alert('Error fetching subcategories: ' + error.message);
                });
        } else {
            subcategoryOptions.innerHTML = '<p class="text-danger">No subcategories available.</p>';
        }
    }

    function selectSubcategory(subcategoryName, subcategoryId) {
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step3').classList.remove('hidden');
        document.getElementById('finalSubcategory').value = subcategoryName;
        document.getElementById('finalSubcategoryId').value = subcategoryId;
    }

    function goBackToCategory() {
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');
        document.getElementById('step1').classList.remove('hidden');
    }

    function goBackToSubcategory() {
        document.getElementById('step3').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('step1').classList.remove('hidden');
    }

    document.getElementById('gallery').addEventListener('change', function(event) {
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = '';  // Clear previous previews

    const files = event.target.files;
    const filesToShow = files.length > 8 ? Array.from(files).slice(files.length - 8) : files;


    filesToShow.forEach(function(file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.style.position = 'relative';
            imgContainer.style.margin = '5px';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';  
            img.style.height = '100px'; 
            img.style.objectFit = 'cover'; 

    
            const removeButton = document.createElement('button');
            removeButton.innerText = 'X';
            removeButton.style.position = 'absolute';
            removeButton.style.top = '0px';
            removeButton.style.right = '0px';
            removeButton.style.background = 'red';
            removeButton.style.color = 'white';
            removeButton.style.border = 'none';
            removeButton.style.borderRadius = '5px';
            removeButton.style.padding = '3px';
            removeButton.style.cursor = 'pointer';

            removeButton.onclick = function() {
                imgContainer.remove(); 
            }

      
            imgContainer.appendChild(img);
            imgContainer.appendChild(removeButton);
            imagePreview.appendChild(imgContainer);
        }

        reader.readAsDataURL(file);
    });
});

    $(document).ready(function() {
        // Fetch cities based on country selection
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
                        $('#city').html(data);
                    },
                    error: function() {
                        alert('Error fetching cities. Please try again.');
                    }
                });
            } else {
                $('#city').html('<option value="" disabled>Select City</option>');
            }
        });

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

        $('#productForm').on('submit', function(e) {
            e.preventDefault();

            // Get the selected package value
            let selectedPackage = $('input[name="boostPlan"]:checked').val();

            // Determine the URL based on the selected package
            let url = (selectedPackage === 'standard') ?
                '<?= $urlval ?>ajax/addproduct.php' :
                '<?= $urlval ?>ajax/addproductpackige.php';

            let formData = new FormData(this);

            // Submit the form using the determined URL
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let parsedResponse = JSON.parse(response);
                    if (parsedResponse.success) {
                        console.log(parsedResponse);

                        // Show success message below the button
                        $('.success-message').text(parsedResponse.message).fadeIn();

                        // Hide the "Post Ad" button
                        $('.post-btn').hide();

                        // Redirect to success_page.php after 5 seconds
                        setTimeout(function() {
                            window.location.href = '<?=$urlval?>success_page.php';
                        }, 5000); 
                    } else if (parsedResponse.errors) {
                        handleErrors(parsedResponse.errors);
                    }
                },
                error: function() {
                    // Log error if AJAX fails
                    console.log("Error: Something went wrong with the AJAX request.");
                }
            });
        });


        function showMessage(type, message) {
            // Remove any existing message
            $('.form-message').remove();

            // Create a new message below the buttons
            let messageBox =
                `<div class="form-message alert alert-${type}" style="margin-top: 10px;">${message}</div>`;
            $('.btn-main-div').after(messageBox); // Add the message below the button section
        }

        function handleErrors(errors) {
            // Clear previous error messages
            $('.text-danger').text('');
            // Display errors for specific fields
            for (let field in errors) {
                $(`#${field}Error`).text(errors[field]);
            }
        }

    });

    function toggleCheckmark(element) {
        // Remove 'active' class from all siblings
        document.querySelectorAll('.sbrct-prere').forEach(el => {
            el.classList.remove('active');
        });

        // Add 'active' class to the clicked element
        element.classList.add('active');
    }

    function filterCategories() {
        let searchTerm = document.getElementById('categorySearch').value.toLowerCase();
        let categories = document.querySelectorAll('.category-item');

        categories.forEach(function(category) {
            let categoryName = category.getAttribute('data-name');
            if (categoryName.includes(searchTerm)) {
                category.style.display = 'block';
            } else {
                category.style.display = 'none';
            }
        });
    }


    const link = document.getElementById('youtube-link');
    const inputContainer = document.getElementById('input-container');

    link.addEventListener('click', (event) => {
        event.preventDefault();
        inputContainer.classList.toggle('hidden321');
    });

    document.querySelector('.website-redict').addEventListener('change', function() {
        var urlInputField = document.getElementById('urlInputField');
        urlInputField.style.display = this.checked ? 'block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const wordLimit = 200;

        tinymce.init({
            selector: '#description',
            plugins: 'wordcount',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist',
            setup: function(editor) {
                editor.on('keyup', function() {
                    const content = editor.getContent({
                        format: 'text'
                    });
                    const words = content.trim().split(/\s+/).filter(word => word.length >
                        0);
                    const wordCount = words.length;

                    const wordCounter = document.getElementById('wordCounter');
                    if (wordCount > wordLimit) {
                        const truncatedContent = words.slice(0, wordLimit).join(' ');
                        editor.setContent(truncatedContent);
                        wordCounter.textContent =
                            `${wordLimit} / ${wordLimit} words (Limit reached)`;
                    } else {
                        wordCounter.textContent = `${wordCount} / ${wordLimit} words`;
                    }
                });
            }
        });
    });


    </script>

</body>

</html>