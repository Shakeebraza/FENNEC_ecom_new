<?php
require_once 'global.php';

if (!isset($_SESSION['userid'])) {
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $urlval ?>custom/css/poststyle.css">
    
    <!-- Include SortableJS from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
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
        .image-preview .image-item {
            position: relative;
            margin: 5px;
            width: 100px;
            height: 100px;
        }
        .image-preview .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .image-preview .image-item button {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            padding: 0;
            cursor: pointer;
            font-size: 12px;
            line-height: 20px;
            text-align: center;
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
            border-radius: 0px !important;
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
            background: url("custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce (1).png") no-repeat;
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
        /* Boost packages */
        .boost-container {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .boost-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .boost-package {
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .boost-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .boost-description {
            font-size: 0.875rem;
            color: #666;
        }
        /* Submit button */
        .submit-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 2rem;
        }
        .submit-button:hover {
            background-color: #16a34a;
        }
        /* Radio buttons */
        .radio-group {
            display: flex;
            gap: 1rem;
        }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        /* Character counter */
        .char-counter {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>

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
        <?php
            $banner = $fun->getRandomBannerByPlacement('home_header');
            if (!empty($banner)) {
        ?>
        <div class="fls-rs-bl" style="width: 100%;  display: flex; justify-content:center; margin: 0 auto; padding:14px 16px; align-items: center;">
            <div class="bnn-mn-ct" style="width: 1000px; background-color: <?php echo $banner['bg_color']; ?>; overflow: hidden; position: relative; padding: 24px 16px;">
                <div class="fls-rs-bl" style="display: flex; justify-content: space-between; max-width: 1200px; background-color: <?php echo $banner['bg_color']; ?>; margin: 0 auto; padding: 24px 16px; align-items: center;">
                    <div class="fls-rs-bl" style="display: flex; align-items: center; gap: 16px; width: 70%;">
                        <div style="border: 2px solid transparent; background: url('<?php echo $banner['image']; ?>') no-repeat; background-size: contain; width: 200px; height: 120px;">
                        </div>
                        <div class="bnner-vt-txt" style="margin-left: 16px; width: 100%;">
                            <h2 style="font-size: 14px; font-weight: 600; color: <?php echo $banner['text_color']; ?>;"><?php echo $banner['title']; ?></h2>
                            <p style="font-size: 20px; font-weight: 700; color: <?php echo $banner['text_color']; ?>;"><?php echo $banner['description']; ?></p>
                        </div>
                    </div>
                    <div class="bnneer-btn-rs" style="display: flex; align-items: center; width: 30%;">
                        <?php if ($banner['btn_text'] && $banner['btn_url']) : ?>
                        <a href="<?php echo $banner['btn_url']; ?>" target="_blank"
                            style="background-color: <?php echo $banner['btn_color']; ?>; padding: 12px 24px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background-color 0.3s ease;">
                            <span style="font-size: 14px; font-weight: 500;"><?php echo $banner['btn_text']; ?></span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        ?>
        <div id="step1">
            <h2 class=" mb-4">Choose a Category</h2>
            <h6>Tell us what category you are posting in</h6>
            <div class="row g-4 justify-content-center">
                <div class="pst-inpt-serc">
                    <input type="text" id="categorySearch" placeholder="e.g. Cars, Sofas, Bikes, Laptops" oninput="filterCategories()">
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
            </div>
        </div>
        <div id="step3" class="hidden">
            <h2 class="text-center mb-4">Post an ad </h2>
            <form id="productForm" enctype="multipart/form-data">
                <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0 auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
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
                    <div class="form-group mb-3" style="padding: 20px; border: 1px dashed #d8d6d9; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <div class="upld-free-imag d-flex w-100">
                            <!-- The file input is hidden; we will manage files manually in JavaScript -->
                            <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                        </div>
                        <label for="gallery" class="custom-file-upload d-flex align-items-center justify-content-center">
                            <div>
                                <img src="custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce (1).png" alt="" style="width: 50px; height: 50px; cursor: pointer;">
                            </div>
                        </label>
                        <div id="imagePreview" class="image-preview d-flex flex-wrap" style="margin-top: 10px;"></div>
                        <div id="imageCounter" style="margin-top: 10px; font-weight: bold; text-align: center;">
                            Selected Images: 0
                        </div>
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
                        <label for="description" class="form-label">Description<span style="color: red;">*</span></label>
                        <textarea id="description" name="description"></textarea>
                        <div id="wordCounter" style="margin-top: 5px; font-size: 0.9em; color: #555;">
                            0 / 200 words
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country<span style="color: red;">*</span></label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="" disabled>Select Country</option>
                            <?php foreach ($countries as $val): ?>
                            <option value="<?= $security->decrypt($val['id']) ?>"><?= $security->decrypt($val['name']) ?></option>
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

                    <div class="boost-container">
                        <h3 class="boost-title">Boost Your Ad</h3>
                        <?php
                        $boostPlans = $fun->getBoostPlans();
                        if (!empty($boostPlans)) :
                            foreach ($boostPlans as $plan) : ?>
                                <div class="boost-package">
                                    <div class="boost-header">
                                        <span><?= $plan['name']; ?></span>
                                        <select class="form-select" style="width: auto;">
                                            <option value="" disabled selected>Select Option</option>
                                            <option value="basic">Basic</option>
                                            <option value="premium">Premium</option>
                                            <option value="ultimate">Ultimate</option>
                                        </select>
                                    </div>
                                    <p class="boost-description">
                                        <?= $plan['description'] ?? 'Short text explaining what this Boost Pack is all about.'; ?>
                                    </p>
                                </div>
                        <?php endforeach;
                        else : ?>
                            <p>No packages available.</p>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:30px;max-width: 100%;">
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Image Packages</h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="freeImages" value="free" disabled checked>
                                <label style="color: #555; font-size: 16px;" for="freeImages">
                                    Free Images Allowed: <?= $fun->getFieldData('free_images'); ?>
                                </label>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" name="extraImages" id="extraImages" value="6">
                                <label style="color: #555; font-size: 16px;" for="extraImages">
                                    Add <?= $fun->getFieldData('images_allowed'); ?> More Images for <?= $fun->getFieldData('paid_images_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Video Packages</h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" name="extraVideos" id="extraVideos" value="1">
                                <label style="color: #555; font-size: 16px;" for="extraVideos">
                                    Add <?= $fun->getFieldData('videos_allowed'); ?> Video for <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Website Redirect</h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" class="website-redict" type="checkbox" name="website-redict" id="websiteRedict" value="1">
                                <label style="color: #555; font-size: 16px;" for="websiteRedict">
                                    Add Website url for <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                                </label>
                            </div>
                            <div id="urlInputField" style="display: none; margin-top: 15px;">
                                <label style="display: block; margin-bottom: 5px; color: #555;" for="redirectUrl">Enter Redirect URL:</label>
                                <input style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;" type="url" id="redirectUrl" name="redirectUrl" placeholder="https://example.com">
                            </div>
                        </div>

                        <!-- <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Payment Options</h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="radio" name="paymentOption" id="paypal" value="paypal">
                                <label style="color: #555; font-size: 16px;" for="paypal">PayPal</label>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="radio" name="paymentOption" id="bitcoin" value="bitcoin">
                                <label style="color: #555; font-size: 16px;" for="bitcoin">Bitcoin</label>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="radio" name="paymentOption" id="onlinePayment" value="online">
                                <label style="color: #555; font-size: 16px;" for="onlinePayment">Online Transfer</label>
                            </div>
                        </div> -->
                    </div>
                    
                    <div class="btn-main-div" style="display: flex;justify-content: space-between;">
                        <button type="submit" class="btn btn-primary post-btn">Post Ad</button>
                        <button type="button" class="btn btn-secondary" onclick="goBackToSubcategory()">Back</button>
                        <div class="success-message" style="display: none; margin-top: 10px; color: green;"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $urlval ?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $urlval?>admin/asset/vendor/tinymce/tinymce.min.js"></script>
    
    <script>
    // Manage selected files in an array
    let selectedFiles = [];

    // ========== Category & Subcategory Functions ==========
    function selectCategory(categoryName, categoryId) {
        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('selectedCategory').innerText = categoryName;
        document.getElementById('finalCategory').value = categoryName;
        document.getElementById('finalCategoryId').value = categoryId;
        const subcategoryOptions = document.getElementById('subcategoryOptions');
        subcategoryOptions.innerHTML = '';
        if (categoryId) {
            fetch('<?= $urlval ?>admin/ajax/product/get_catjson.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'catId=' + encodeURIComponent(categoryId)
                })
                .then(response => {
                    if (!response.ok) { throw new Error('Network response was not ok'); }
                    return response.text();
                })
                .then(data => {
                    try {
                        const parsedData = JSON.parse(data);
                        if (parsedData.status === 'success') {
                            parsedData.data.forEach(subcategory => {
                                const colDiv = document.createElement('div');
                                colDiv.className = 'col-md-3';
                                colDiv.innerHTML = `
                                    <div class=" sbrct-prere w-100" 
                                         onclick="selectSubcategory('${subcategory.name}', '${subcategory.id}')">
                                         ${subcategory.name}
                                    </div>`;
                                subcategoryOptions.appendChild(colDiv);
                            });
                        } else {
                            alert(parsedData.message);
                        }
                    } catch (error) {
                        alert('Error parsing JSON: ' + error.message);
                    }
                })
                .catch(error => { alert('Error fetching subcategories: ' + error.message); });
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

    // ========== Image Preview & Sortable ==========
    // 1) Update the image preview to display current files in selectedFiles
    //    Each preview item is tagged with data-file-index to keep track of its original index.
    function updateImagePreview() {
        const imagePreview = document.getElementById('imagePreview');
        const imageCounter = document.getElementById('imageCounter');
        imagePreview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'image-item';
                // Assign the index to data-file-index:
                imgContainer.setAttribute('data-file-index', index);

                const img = document.createElement('img');
                img.src = e.target.result;
                
                const removeButton = document.createElement('button');
                removeButton.innerText = 'X';
                removeButton.onclick = function() {
                    selectedFiles.splice(index, 1);
                    updateImagePreview();
                };

                imgContainer.appendChild(img);
                imgContainer.appendChild(removeButton);
                imagePreview.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        });
        imageCounter.innerText = `Selected Images: ${selectedFiles.length}`;
    }
    
    // 2) Handle file input changes
    document.getElementById('gallery').addEventListener('change', function(event) {
        const filesArray = Array.from(event.target.files);
        // If needed, limit to 8 files (example)
        selectedFiles = filesArray.slice(0, 8);
        updateImagePreview();
    });
    
    // 3) Initialize Sortable on #imagePreview
    document.addEventListener('DOMContentLoaded', function() {
        new Sortable(document.getElementById('imagePreview'), {
            animation: 150,
            onEnd: function (evt) {
                // Rebuild selectedFiles according to the new DOM order
                const updatedFiles = [];
                const previewItems = document.querySelectorAll('#imagePreview .image-item');
                
                // Each .image-item has data-file-index from the original array
                previewItems.forEach((item) => {
                    const oldIndex = parseInt(item.getAttribute('data-file-index'), 10);
                    updatedFiles.push(selectedFiles[oldIndex]);
                });
                
                selectedFiles = updatedFiles;
                
                // OPTIONAL: Update data-file-index in the DOM to reflect new order
                previewItems.forEach((item, newIndex) => {
                    item.setAttribute('data-file-index', newIndex);
                });
            }
        });
    });

    // ========== City and Area AJAX ==========
    $(document).ready(function() {
        $('#country').on('change', function() {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '<?= $urlval ?>admin/ajax/product/get_cities.php',
                    type: 'POST',
                    data: { country_id: countryId },
                    success: function(data) { $('#city').html(data); },
                    error: function() { alert('Error fetching cities. Please try again.'); }
                });
            } else {
                $('#city').html('<option value="" disabled>Select City</option>');
            }
        });
    
        $('#city').on('change', function() {
            var cityId = $(this).val();
            if (cityId) {
                $.ajax({
                    url: '<?= $urlval ?>admin/ajax/product/get_areas.php',
                    type: 'POST',
                    data: { city_id: cityId },
                    success: function(data) { $('#aera').html(data); },
                    error: function() { alert('Error fetching areas. Please try again.'); }
                });
            } else {
                $('#aera').html('<option value="" disabled selected>Select an area</option>');
            }
        });
    
        // ========== Form Submission ==========
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            
            // Example: Decide URL based on any selected "boostPlan" radio
            let selectedPackage = $('input[name="boostPlan"]:checked').val();
            let url = (selectedPackage === 'standard') 
                        ? '<?= $urlval ?>ajax/addproduct.php' 
                        : '<?= $urlval ?>ajax/addproductpackige.php';

            let formData = new FormData(this);
            // Remove the original 'gallery[]' files from the form
            formData.delete('gallery[]');

            // Append each file from selectedFiles in the new order
            selectedFiles.forEach(file => {
                formData.append('gallery[]', file);
            });

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
                        $('.success-message').text(parsedResponse.message).fadeIn();
                        $('.post-btn').hide();
                        setTimeout(function() {
                            window.location.href = '<?= $urlval ?>success_page.php';
                        }, 5000);
                    } else if (parsedResponse.errors) {
                        handleErrors(parsedResponse.errors);
                    }
                },
                error: function() {
                    console.log("Error: Something went wrong with the AJAX request.");
                }
            });
        });
    
        function showMessage(type, message) {
            $('.form-message').remove();
            let messageBox = `<div class="form-message alert alert-${type}" style="margin-top: 10px;">${message}</div>`;
            $('.btn-main-div').after(messageBox);
        }
    
        function handleErrors(errors) {
            $('.text-danger').text('');
            for (let field in errors) {
                $(`#${field}Error`).text(errors[field]);
            }
        }
    });
    
    function toggleCheckmark(element) {
        document.querySelectorAll('.sbrct-prere').forEach(el => { el.classList.remove('active'); });
        element.classList.add('active');
    }
    
    function filterCategories() {
        let searchTerm = document.getElementById('categorySearch').value.toLowerCase();
        let categories = document.querySelectorAll('.category-item');
        categories.forEach(function(category) {
            let categoryName = category.getAttribute('data-name');
            category.style.display = categoryName.includes(searchTerm) ? 'block' : 'none';
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
    
    // ========== TinyMCE + Word Counting ==========
    document.addEventListener('DOMContentLoaded', function() {
        const wordLimit = 200;
        tinymce.init({
            selector: '#description',
            plugins: 'wordcount',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist',
            setup: function(editor) {
                editor.on('keyup', function() {
                    const content = editor.getContent({ format: 'text' });
                    const words = content.trim().split(/\s+/).filter(word => word.length > 0);
                    const wordCount = words.length;
                    const wordCounter = document.getElementById('wordCounter');
                    if (wordCount > wordLimit) {
                        const truncatedContent = words.slice(0, wordLimit).join(' ');
                        editor.setContent(truncatedContent);
                        wordCounter.textContent = `${wordLimit} / ${wordLimit} words (Limit reached)`;
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












<!-- <!DOCTYPE html>
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
 .boost-container {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .boost-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .boost-package {
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .boost-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .boost-description {
            font-size: 0.875rem;
            color: #666;
        }

        .submit-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 2rem;
        }

        .submit-button:hover {
            background-color: #16a34a;
        }

        .radio-group {
            display: flex;
            gap: 1rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .char-counter {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
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
        <?php
$banner = $fun->getRandomBannerByPlacement('home_header');
if (!empty($banner)) {
?>
        <div class="fls-rs-bl"
            style="width: 100%;  display: flex; justify-content:center; margin: 0 auto; padding:14px 16px; align-items: center;">
            <div class="bnn-mn-ct"
                style="width: 1000px; background-color: <?php echo $banner['bg_color']; ?>; overflow: hidden; position: relative; padding: 24px 16px;">
                <div class="fls-rs-bl"
                    style="display: flex; justify-content: space-between; max-width: 1200px; background-color: <?php echo $banner['bg_color']; ?>; margin: 0 auto; padding: 24px 16px; align-items: center;">

                    <div class="fls-rs-bl" style="display: flex; align-items: center; gap: 16px; width: 70%; ">

                        <div
                            style="border: 2px solid transparent; background: url('<?php echo $banner['image']; ?>') no-repeat; background-size: contain; width: 200px; height: 120px;">
                        </div>

                        <div class="bnner-vt-txt" style="margin-left: 16px; width: 100%;">

                            <h2 style="font-size: 14px; font-weight: 600; color: <?php echo $banner['text_color']; ?>;">
                                <?php echo $banner['title']; ?></h2>
                            <p style="font-size: 20px; font-weight: 700; color: <?php echo $banner['text_color']; ?>;">
                                <?php echo $banner['description']; ?></p>
                        </div>
                    </div>

                    <div class="bnneer-btn-rs" style="display: flex; align-items: center; width: 30%;">

                        <?php if ($banner['btn_text'] && $banner['btn_url']) : ?>
                        <a href="<?php echo $banner['btn_url']; ?>" target="_blank"
                            style="background-color: <?php echo $banner['btn_color']; ?>; padding: 12px 24px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background-color 0.3s ease;">
                            <span style="font-size: 14px; font-weight: 500;"><?php echo $banner['btn_text']; ?></span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
}
?>
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
            </div>
        </div>


        <div id="step3" class="hidden">
            <h2 class="text-center mb-4">Post an ad </h2>
            <form id="productForm" enctype="multipart/form-data">
            <div style="font-family: Arial, sans-serif; max-width: 100%; margin: 0 auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
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
                    style="padding: 20px; border: 1px dashed #d8d6d9; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <div class="upld-free-imag d-flex w-100">
                        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                    </div>
                    <label for="gallery" class="custom-file-upload d-flex align-items-center justify-content-center">
                        <div>
                            <img src="custom/asset/add-image-icon.ea516b80c0402f99dfb041ba4db057ce (1).png" alt=""
                                style="width: 50px; height: 50px; cursor: pointer;">
                        </div>
                    </label>
                    <div id="imagePreview" class="image-preview d-flex flex-wrap" style="margin-top: 10px;"></div>
                    <div id="imageCounter" style="margin-top: 10px; font-weight: bold; text-align: center;">
                        Selected Images: 0
                    </div>
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

                <div class="boost-container">
                <h3 class="boost-title">Boost Your Ad</h3>
                
                <?php
                $boostPlans = $fun->getBoostPlans();
                if (!empty($boostPlans)) :
                    foreach ($boostPlans as $plan) : ?>
                        <div class="boost-package">
                            <div class="boost-header">
                                <span><?= $plan['name']; ?></span>
                                <select class="form-select" style="width: auto;">
                                    <option value="" disabled selected>Select Option</option>
                                    <option value="basic">Basic</option>
                                    <option value="premium">Premium</option>
                                    <option value="ultimate">Ultimate</option>
                                </select>
                            </div>
                            <p class="boost-description">
                                <?= $plan['description'] ?? 'Short text explaining what this Boost Pack is all about. The short text must take 1 to 2 lines.'; ?>
                            </p>
                        </div>
                    <?php endforeach;
                else : ?>
                    <p>No packages available.</p>
                <?php endif; ?>
            </div>
            <div style="margin-top:30px;max-width: 100%; ">
                <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Image Packages</h5>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="checkbox" id="freeImages" value="free" disabled checked>
                        <label style="color: #555; font-size: 16px;" for="freeImages">
                            Free Images Allowed: <?= $fun->getFieldData('free_images'); ?>
                        </label>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="checkbox" name="extraImages" id="extraImages" value="6">
                        <label style="color: #555; font-size: 16px;" for="extraImages">
                            Add <?= $fun->getFieldData('images_allowed'); ?> More Images for
                            <?= $fun->getFieldData('paid_images_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Video Packages</h5>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="checkbox" name="extraVideos" id="extraVideos" value="1">
                        <label style="color: #555; font-size: 16px;" for="extraVideos">
                            Add <?= $fun->getFieldData('videos_allowed'); ?> Video for
                            <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Website Redirect</h5>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" class="website-redict" type="checkbox" name="website-redict" id="websiteRedict" value="1">
                        <label style="color: #555; font-size: 16px;" for="websiteRedict">
                            Add <?= $fun->getFieldData('videos_allowed'); ?> Website url for
                            <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                        </label>
                    </div>
                    <div id="urlInputField" style="display: none; margin-top: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #555;" for="redirectUrl">Enter Redirect URL:</label>
                        <input style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;" type="url" id="redirectUrl" name="redirectUrl" placeholder="https://example.com">
                    </div>
                </div>

                <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Payment Options</h5>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="radio" name="paymentOption" id="paypal" value="paypal">
                        <label style="color: #555; font-size: 16px;" for="paypal">
                            PayPal
                        </label>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="radio" name="paymentOption" id="bitcoin" value="bitcoin">
                        <label style="color: #555; font-size: 16px;" for="bitcoin">
                            Bitcoin
                        </label>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <input style="margin-right: 10px;" type="radio" name="paymentOption" id="onlinePayment" value="online">
                        <label style="color: #555; font-size: 16px;" for="onlinePayment">
                            Online Transfer
                        </label>
                    </div>
                </div>
               
                </div>

      
                <div class="btn-main-div" style="display: flex;justify-content: space-between;">
                    <button type="submit" class="btn btn-primary post-btn">Post Ad</button>
                    <button type="button" class="btn btn-secondary" onclick="goBackToSubcategory()">Back</button>
                    <div class="success-message" style="display: none; margin-top: 10px; color: green;"></div>
                </div>
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
    const imageCounter = document.getElementById('imageCounter');
    imagePreview.innerHTML = ''; 
    const files = event.target.files; 
    const filesArray = Array.from(files); 
    const filesToShow = filesArray.length > 8 ? filesArray.slice(-8) : filesArray; 

    const totalImages = filesArray.length;
    imageCounter.innerText = `Selected Images: ${totalImages > 8 ? 8 : totalImages}`;

    filesToShow.forEach(function(file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.style.position = 'relative';
            imgContainer.style.margin = '5px';
            imgContainer.style.width = '100px';
            imgContainer.style.height = '100px';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '5px';

            const removeButton = document.createElement('button');
            removeButton.innerText = 'X';
            removeButton.style.position = 'absolute';
            removeButton.style.top = '5px';
            removeButton.style.right = '5px';
            removeButton.style.background = 'red';
            removeButton.style.color = 'white';
            removeButton.style.border = 'none';
            removeButton.style.borderRadius = '50%';
            removeButton.style.width = '20px';
            removeButton.style.height = '20px';
            removeButton.style.padding = '0';
            removeButton.style.cursor = 'pointer';
            removeButton.style.fontSize = '12px';
            removeButton.style.lineHeight = '20px';
            removeButton.style.textAlign = 'center';

            removeButton.onclick = function() {
                imgContainer.remove();
                const remainingImages = imagePreview.childElementCount;
                imageCounter.innerText = `Selected Images: ${remainingImages}`;
            };

            imgContainer.appendChild(img);
            imgContainer.appendChild(removeButton);
            imagePreview.appendChild(imgContainer);
        };

        reader.readAsDataURL(file);
    });
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

            let selectedPackage = $('input[name="boostPlan"]:checked').val();

            let url = (selectedPackage === 'standard') ?
                '<?= $urlval ?>ajax/addproduct.php' :
                '<?= $urlval ?>ajax/addproductpackige.php';

            let formData = new FormData(this);

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

                        $('.success-message').text(parsedResponse.message).fadeIn();

                        $('.post-btn').hide();

                        setTimeout(function() {
                            window.location.href = '<?=$urlval?>success_page.php';
                        }, 5000); 
                    } else if (parsedResponse.errors) {
                        handleErrors(parsedResponse.errors);
                    }
                },
                error: function() {
                    console.log("Error: Something went wrong with the AJAX request.");
                }
            });
        });


        function showMessage(type, message) {
            $('.form-message').remove();

            let messageBox =
                `<div class="form-message alert alert-${type}" style="margin-top: 10px;">${message}</div>`;
            $('.btn-main-div').after(messageBox); 
        }

        function handleErrors(errors) {
            $('.text-danger').text('');
            for (let field in errors) {
                $(`#${field}Error`).text(errors[field]);
            }
        }

    });

    function toggleCheckmark(element) {
        document.querySelectorAll('.sbrct-prere').forEach(el => {
            el.classList.remove('active');
        });

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

</html> -->