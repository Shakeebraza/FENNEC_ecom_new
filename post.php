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
        .total-fee-container {
            background: linear-gradient(135deg, #fdfbfb, #ebedee);
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            margin: 20px auto; /* Centers the container */
        }
        .total-fee-label {
            font-size: 1.8rem;
            color: #666;
            margin-bottom: 10px;
        }
        .total-fee-amount {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }
        #step1 {
            padding: 20px;
        }
        .category-item {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .category-item:hover .category-btn {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .category-btn.selected {
            border-color: #007bff;
            background-color: #e9f5ff;
        }

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
            <h2 class="mb-4">Choose a Category</h2>
            <h6 class="mb-3">Tell us what category you are posting in</h6>

            <!-- Search Input -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <input
                        type="text"
                        id="categorySearch"
                        class="form-control"
                        placeholder="e.g. Cars, Sofas, Bikes, Laptops"
                        oninput="filterCategories()"
                    >
                </div>
            </div>

            <!-- Category Cards Container -->
            <div class="row g-4 justify-content-center" id="categoryContainer">
                <?php
                    $findCate = $categoryManager->getAllCategoriesHeaderMenu();
                    if ($findCate['status'] == 'success') {
                        foreach ($findCate['data'] as $category) {
                            // Old style: onclick="selectCategory('categoryName','categoryId')"
                            echo '
                            <div class="col-md-2 col-6 category-item" data-name="' . strtolower($category['category_name']) . '">
                                <div
                                    class="category-btn text-center p-3 border rounded"
                                    onclick="selectCategory(\'' . $category['category_name'] . '\', \'' . $category['id'] . '\')"
                                >
                                    <i class="fas ' . $category['icon'] . ' fa-2x mb-2"></i>
                                    <div>' . $category['category_name'] . '</div>
                                </div>
                            </div>
                            ';
                        }
                    } else {
                        echo '<div class="col-12 text-center text-muted">No categories available</div>';
                    }
                ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center mt-3 text-muted" style="display: none;">
                No matching categories found.
            </div>
        </div>



        <!-- JavaScript for Filtering and Selection -->
        <script>
            // 1) Filter categories as the user types in the search input
            function filterCategories() {
                const input = document.getElementById('categorySearch');
                const filter = input.value.toLowerCase();
                const categoryItems = document.querySelectorAll('#categoryContainer .category-item');
                let foundMatch = false;

                categoryItems.forEach(function(item) {
                    const name = item.getAttribute('data-name');
                    if (name.indexOf(filter) > -1) {
                        item.style.display = '';
                        foundMatch = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                document.getElementById('noResults').style.display = foundMatch ? 'none' : 'block';
            }

            // 2) Called when user clicks a category
            //    The old code passes only (categoryName, categoryId).
            //    We'll grab the clicked element via event.currentTarget.
            function selectCategory(categoryName, categoryId) {
                // The clicked .category-btn element
                const el = event.currentTarget;

                // Remove 'selected' class from all category buttons
                const allBtns = document.querySelectorAll('#categoryContainer .category-btn');
                allBtns.forEach(function(btn) {
                    btn.classList.remove('selected');
                });

                // Add 'selected' class to the clicked button
                el.classList.add('selected');

                // Do whatever you need with categoryName/categoryId
                console.log('Selected Category:', categoryName, 'ID:', categoryId);
                // e.g. store in hidden fields, or proceed to next step
            }
        </script>

        <div class="container">
            <div id="step2" class="hidden">
                <h2 class=" mb-4">Choose a Subcategory for <span id="selectedCategory"></span></h2>
                <div class="row d-block sb-cytr-opt " id="subcategoryOptions"></div>
            </div>
        </div>
        <div id="step3" class="hidden">
            <h2 class="text-center mb-4">Post an ad</h2>
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
                        <label for="youtube_url" class="form-label">Enter Youtube URL:</label>
                        <input type="url" class="form-control" id="youtube_url" name="youtube_url">
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

                    <!-- Updated Description Box with Bootstrap form-control + a fixed height -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description<span style="color: red;">*</span></label>
                        <textarea id="description" name="description" class="form-control" style="height: 200px;"></textarea>
                        <!-- <div id="wordCounter" style="margin-top: 5px; font-size: 0.9em; color: #555;">
                            0 / 200 words
                        </div> -->
                    </div>
                    <!-- End of Updated Description Box -->

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

                    <!-- Boost Plans: single radio choice among standard/premium/gold, etc. -->
                    <div class="boost-container">
                        <h3 class="boost-title">Boost Your Ad</h3>
                        <?php
                        $boostPlans = $fun->getBoostPlans(); 
                        // e.g. each $plan has [ 'id'=>X, 'name'=>'Gold', 'price'=>50, 'product_type'=>'gold', 'description'=>'...' ]
                        if (!empty($boostPlans)) :
                            foreach ($boostPlans as $plan) : 
                                $planId   = $plan['id'];
                                $planName = $plan['name'];
                                $planDesc = $plan['description'] ?? '';
                                $planPrice= $plan['price'];
                                $planType = $plan['slug']; // 'standard', 'premium', 'gold', etc.
                        ?>
                            <div class="boost-package">
                                <input
                                  type="radio"
                                  name="product_type"
                                  class="boostPlanRadio"
                                  id="plan-<?= $planId ?>"
                                  value="<?= htmlspecialchars($planType) ?>"
                                  data-price="<?= htmlspecialchars($planPrice) ?>"
                                  style="margin-right: 10px;"
                                />
                                <label for="plan-<?= $planId ?>" style="margin: 0;">
                                    <strong><?= htmlspecialchars($planName) ?></strong>
                                    (<?= htmlspecialchars($planPrice) ?> <?= $fun->getFieldData('site_currency'); ?>)
                                    <br>
                                    <small><?= htmlspecialchars($planDesc) ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                            <p>No packages available.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Image Packages -->
                    <div style="margin-top:30px;max-width: 100%;">
                        <!-- Only show "Image Gallery Featured" if fee_image_gallery_featured_enabled == 1 -->
                        <?php if($fun->getbilling_feesData('fee_image_gallery_featured_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Image Packages
                            </h5>
                            <!-- <div style="margin-bottom: 10px;">
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
                            </div> -->

                            
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="imageGallery" name="imageGallery"
                                    value="<?= $fun->getbilling_feesData('fee_image_gallery_featured'); ?>">
                                <label style="color: #555; font-size: 16px;" for="imageGallery">
                                    Image Gallery Featured – Fee
                                    <?= $fun->getbilling_feesData('fee_image_gallery_featured'); ?> <?= $fun->getFieldData('site_currency'); ?><br />
                                    <small>
                                        Classified will appear in the Image Gallery Featured Classifieds section on the main page.
                                        At least one image must be uploaded with the classified.
                                    </small>
                                </label>
                            </div>
                            
                        </div>
                        <?php endif; ?>

                        <!-- Video Packages -->
                        <!-- Only show "Video Gallery Featured" if fee_video_gallery_featured_enabled == 1 -->
                        <?php if($fun->getbilling_feesData('fee_video_gallery_featured_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Video Packages
                            </h5>
                            <!-- <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" name="extraVideos" id="extraVideos" value="1">
                                <label style="color: #555; font-size: 16px;" for="extraVideos">
                                    Add <?= $fun->getFieldData('videos_allowed'); ?> Video for
                                    <?= $fun->getFieldData('paid_videos_price'); ?> <?= $fun->getFieldData('site_currency'); ?>
                                </label>
                            </div> -->

                            
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="videoGallery" name="videoGallery"
                                    value="<?= $fun->getbilling_feesData('fee_video_gallery_featured'); ?>">
                                <label style="color: #555; font-size: 16px;" for="videoGallery">
                                    Video Gallery Featured – Fee
                                    <?= $fun->getbilling_feesData('fee_video_gallery_featured'); ?> <?= $fun->getFieldData('site_currency'); ?><br />
                                    <small>
                                        Classified will appear in the Video Gallery Featured Classifieds section on the main page.
                                        At least one video must be uploaded with the classified.
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Website Redirect -->
                        <!-- <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Website Redirect
                            </h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" class="website-redict"
                                    type="checkbox" name="websiteRedirect" id="websiteRedirect" value="1">
                                <label style="color: #555; font-size: 16px;" for="websiteRedirect">
                                    Add Website URL for <?= $fun->getFieldData('paid_videos_price'); ?>
                                    <?= $fun->getFieldData('site_currency'); ?>
                                </label>
                            </div>
                            <div id="urlInputField" style="display: none; margin-top: 15px;">
                                <label style="display: block; margin-bottom: 5px; color: #555;" for="redirectUrl">
                                    Enter Redirect URL:
                                </label>
                                <input style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
                                            font-size: 16px;"
                                    type="url" id="redirectUrl" name="redirectUrl" placeholder="https://example.com">
                            </div>
                        </div> -->

                        <!-- Bold Option (only if fee_bold_enabled == 1) -->
                        <?php if($fun->getbilling_feesData('fee_bold_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Bold Option
                            </h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="bold" name="bold"
                                    value="<?= $fun->getbilling_feesData('fee_bold'); ?>">
                                <label style="color: #555; font-size: 16px;" for="bold">
                                    Bold – Fee <?= $fun->getbilling_feesData('fee_bold'); ?> <?= $fun->getFieldData('site_currency'); ?><br />
                                    <small>
                                        Classified will appear in bold font.
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Featured Option (only if fee_featured_enabled == 1) -->
                        <?php if($fun->getbilling_feesData('fee_featured_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Featured Option
                            </h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="featured" name="featured"
                                    value="<?= $fun->getbilling_feesData('fee_featured'); ?>">
                                <label style="color: #555; font-size: 16px;" for="featured">
                                    Featured – Fee <?= $fun->getbilling_feesData('fee_featured'); ?> <?= $fun->getFieldData('site_currency'); ?><br />
                                    <small>
                                        Classified will appear as featured (for example in search results).
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Front Page Featured Option (only if fee_front_featured_enabled == 1) -->
                        <?php if($fun->getbilling_feesData('fee_front_featured_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Front Page Featured
                            </h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="frontFeatured" name="frontFeatured"
                                    value="<?= $fun->getbilling_feesData('fee_front_featured'); ?>">
                                <label style="color: #555; font-size: 16px;" for="frontFeatured">
                                    Front Page Featured – Fee
                                    <?= $fun->getbilling_feesData('fee_front_featured'); ?> <?= $fun->getFieldData('site_currency'); ?><br/>
                                    <small>
                                        Classified will appear on the front page featured section.
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Highlighted Option (only if fee_highlight_enabled == 1) -->
                        <?php if($fun->getbilling_feesData('fee_highlight_enabled') == 1): ?>
                        <div style="margin-bottom: 25px; background-color: #ffffff; padding: 20px;
                                    border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h5 style="color: #333; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
                                Highlighted Option
                            </h5>
                            <div style="margin-bottom: 10px;">
                                <input style="margin-right: 10px;" type="checkbox" id="highlighted" name="highlighted"
                                    value="<?= $fun->getbilling_feesData('fee_highlight'); ?>">
                                <label style="color: #555; font-size: 16px;" for="highlighted">
                                    Highlighted – Fee <?= $fun->getbilling_feesData('fee_highlight'); ?> <?= $fun->getFieldData('site_currency'); ?><br />
                                    <small>
                                        Classified will appear highlighted (for example with a different background color in search results).
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Total Fee Display -->
                    <div class="total-fee-container mt-3">
                    <div class="total-fee-label">Total Fee</div>
                    <div class="total-fee-amount">
                        <span id="totalFee">0.00</span> <?= $fun->getFieldData('site_currency'); ?>
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

    
    <?php
    // Retrieve the video option setting from approval_parameters (assuming id = 1)
    $videoOption = $fun->getData('approval_parameters', 'video_option_posting', 1);
    if (strtolower($videoOption) === 'enabled') :
    include_once 'video_listing_animation.php';
    ?>  
    <?php
    endif;
    ?>

    <!-- Include jQuery and Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $urlval ?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $urlval?>admin/asset/vendor/tinymce/tinymce.min.js"></script>

    <script>

    $(document).ready(function () {

    // 1) Create a function to update the total whenever something changes
    function updateTotalFee() {
        let total = 0;

        // A) Add the plan price (if a plan is selected)
        const selectedPlan = $('input[name="product_type"]:checked');
        if (selectedPlan.length > 0) {
            let planPrice = parseFloat(selectedPlan.data('price')) || 0;
            total += planPrice;
        }

        // B) Check each feature checkbox and add its value if checked
        //    Adjust these IDs to match your actual element IDs
        const featureCheckboxes = [
            '#imageGallery',
            '#videoGallery',
            '#bold',
            '#featured',
            '#frontFeatured',
            '#highlighted'
        ];

        featureCheckboxes.forEach(function(selector) {
            let checkbox = $(selector);
            if (checkbox.is(':checked')) {
                let feeValue = parseFloat(checkbox.val()) || 0;
                total += feeValue;
            }
        });

        // (Optional) If you also want to include the item’s “Price” field 
        // in the total fees, just uncomment below:
        // const itemPrice = parseFloat($('#price').val()) || 0;
        // total += itemPrice;

        // C) Update the “Total Fee” display
        $('#totalFee').text(total.toFixed(2));
    }

    // 2) Attach the update function to each plan radio and each checkbox
    $('input[name="product_type"]').on('change', updateTotalFee);
    $('#imageGallery, #videoGallery, #bold, #featured, #frontFeatured, #highlighted')
    .on('change', updateTotalFee);

    // 3) Call once at page load, in case any defaults are set
    updateTotalFee();
    });
    // Manage selected files with unique IDs
    let selectedFiles = [];
    let fileIdCounter = 0; // To assign unique IDs to each file

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
            .then(response => response.text())
            .then(data => {
                try {
                    const parsedData = JSON.parse(data);
                    if (parsedData.status === 'success') {
                        parsedData.data.forEach(subcategory => {
                            const colDiv = document.createElement('div');
                            colDiv.className = 'col-md-3';
                            colDiv.innerHTML =
                                `<div class="sbrct-prere w-100" onclick="selectSubcategory('${subcategory.name}', '${subcategory.id}')">
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

    function goBackToSubcategory() {
        document.getElementById('step3').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('step1').classList.remove('hidden');
    }

    // ========== Image Preview & Sortable ==========
    function updateImagePreview() {
        const imagePreview = document.getElementById('imagePreview');
        const imageCounter = document.getElementById('imageCounter');
        imagePreview.innerHTML = '';

        selectedFiles.forEach(fileObj => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'image-item';
                imgContainer.setAttribute('data-file-id', fileObj.id);

                const img = document.createElement('img');
                img.src = e.target.result;

                const removeButton = document.createElement('button');
                removeButton.innerText = 'X';
                removeButton.onclick = function() {
                    selectedFiles = selectedFiles.filter(f => f.id !== fileObj.id);
                    updateImagePreview();
                };

                imgContainer.appendChild(img);
                imgContainer.appendChild(removeButton);
                imagePreview.appendChild(imgContainer);
            };
            reader.readAsDataURL(fileObj.file);
        });

        imageCounter.innerText = `Selected Images: ${selectedFiles.length}`;
    }

    document.getElementById('gallery').addEventListener('change', function(event) {
        const filesArray = Array.from(event.target.files);
        // Example limit: 8 files
        const filesToAdd = filesArray.slice(0, 8 - selectedFiles.length).map(file => {
            return { id: fileIdCounter++, file: file };
        });
        selectedFiles = selectedFiles.concat(filesToAdd);
        updateImagePreview();
        // Reset the file input
        event.target.value = '';
    });

    document.addEventListener('DOMContentLoaded', function() {
        new Sortable(document.getElementById('imagePreview'), {
            animation: 150,
            onEnd: function (evt) {
                // Rebuild selectedFiles based on new DOM order
                const updatedFiles = [];
                const previewItems = document.querySelectorAll('#imagePreview .image-item');
                previewItems.forEach(item => {
                    const fileId = parseInt(item.getAttribute('data-file-id'), 10);
                    const fileObj = selectedFiles.find(f => f.id === fileId);
                    if (fileObj) {
                        updatedFiles.push(fileObj);
                    }
                });
                selectedFiles = updatedFiles;
            }
        });
    });

    // ========== City and Area AJAX ==========
    $(document).ready(function() {
        $('#country').on('change', function() {
            let countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '<?= $urlval ?>admin/ajax/product/get_cities.php',
                    type: 'POST',
                    data: { country_id: countryId },
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
            let cityId = $(this).val();
            if (cityId) {
                $.ajax({
                    url: '<?= $urlval ?>admin/ajax/product/get_areas.php',
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

        // ========== Form Submission ==========
        $('#productForm').on('submit', function(e) {
            e.preventDefault();

            // Decide which endpoint to use (example logic)
            // If there's a certain radio or condition, pick 'addproductpackige.php', else 'addproduct.php'
            // For demonstration, we assume standard:
            let url = '<?= $urlval ?>ajax/addproduct.php'; 
            // Or if we detect a package:
            // let url = '<?= $urlval ?>ajax/addproductpackige.php';

            let formData = new FormData(this);
            // Remove the original 'gallery[]' files from the form
            formData.delete('gallery[]');

            // Append each file from selectedFiles in the new order
            selectedFiles.forEach(fileObj => {
                formData.append('gallery[]', fileObj.file);
            });

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let parsedResponse;
                    try {
                        parsedResponse = JSON.parse(response);
                    } catch (e) {
                        console.error('Could not parse JSON:', response);
                        return;
                    }

                    if (parsedResponse.success) {
                        console.log(parsedResponse);
                        $('.success-message').text(parsedResponse.message).fadeIn();
                        $('.post-btn').hide();
                        setTimeout(function() {
                            window.location.href = '<?= $urlval ?>success_page.php';
                        }, 3000);
                    } else if (parsedResponse.errors) {
                        handleErrors(parsedResponse.errors);
                    } else {
                        console.log(parsedResponse);
                    }
                },
                error: function() {
                    console.log("Error: Something went wrong with the AJAX request.");
                }
            });
        });

        function handleErrors(errors) {
            $('.text-danger').text('');
            for (let field in errors) {
                $('#' + field + 'Error').text(errors[field]);
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

    document.getElementById('websiteRedirect').addEventListener('change', function() {
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
