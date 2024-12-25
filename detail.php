<?php
require_once 'global.php';
include_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $slug = $_GET['slug'] ?? null;
    if (!empty($slug)) {
        $userId = isset($_SESSION['userid']) ? base64_decode($_SESSION['userid']) : NULL;
        $productData = $productFun->getProductDetailsBySlugsort($slug, $userId);

        if (empty($productData)) {
            header('Location: index.php');
            exit();
        }
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
$latitude = $productData['city_latitude'];
$longitude = $productData['city_longitude'];
$country = $productData['country'];
$city = $productData['city'];
$area = $productData['area'];

?>
<style>
        .btn.toggle-btn {
        transition: background-color 0.3s, color 0.3s;
    }

    .btn.toggle-btn.active {
        background-color: #00494f;
        color: white;
    }
    /* Slider Item Styling */
    .slider-item {
        position: relative;
    }

    .image-container {
        position: relative;
        width: 100%;
        height: 200px;
    }

    .image-container img {
        width: 100%;
        height: 100%;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .image-container:hover .image-overlay {
        opacity: 1;

    }

    .product-name {
        color: #fff;
        font-size: 1.5rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);

    }

    .swiper-pagination.secoundpage.swiper-pagination-clickable.swiper-pagination-bullets.swiper-pagination-horizontal {
        top: 190%;
    }


    @media screen and (max-width: 768px) {
        .swiper-pagination.secoundpage.swiper-pagination-clickable.swiper-pagination-bullets.swiper-pagination-horizontal {
            top: 275%;
        }
    }


    @media screen and (max-width: 480px) {
        .swiper-pagination.secoundpage.swiper-pagination-clickable.swiper-pagination-bullets.swiper-pagination-horizontal {
            top: 275%;
        }
    }

    .gallery-container {
        padding: 30px;
        max-width: 1200px;
        margin: 0 auto;
        text-align: center;
    }

    /* Gallery Title */
    .gallery-title {
        font-size: 2em;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-transform: uppercase;
        border-bottom: 2px solid #ff5733;
        display: inline-block;
        padding-bottom: 5px;
    }

    /* Gallery Grid */
    .gallery-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    /* Gallery Item */
    .gallery-item {
        position: relative;
        width: 300px;
        height: 200px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    /* Video */
    .gallery-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .gallery-item {
            width: 100%;
        }
    }

    .form-check-label a {
        color: #0d6efd;
    }

    .form-check-label a:hover {
        text-decoration: underline;
    }

    .btn-report {
        background: #65ff00;
        border: none;
        color: #000;
    }

    .btn-report:hover {
        background: #5ce600;
    }

    .share__wrapper {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }

    .share__title {
        font-size: 1.2rem;
        margin-bottom: 15px;
        font-weight: bold;
        color: #333;
    }

    .share__list {
        list-style: none;
        padding: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .share__item {
        display: inline-block;
    }

    .share__link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        color: white;
        font-size: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .share__link:hover {
        transform: scale(1.1);
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .share__link--facebook {
        background-color: #3b5998;
    }

    .share__link--twitter {
        background-color: #1da1f2;
    }

    .share__link--linkedin {
        background-color: #0077b5;
    }

    .share__link--mail {
        background-color: #d44638;
    }

    .share__link--whatsapp {
        background-color: #25d366;
    }

    .share__link i {
        pointer-events: none;
    }
    .slick-prev, .slick-next {
    color: black; 
}

.slick-prev:before, .slick-next:before {
    color: black !important;
}
    
</style>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $urlval ?>">Home</a></li>

            <li class="breadcrumb-item" aria-current="page">
                <a href="<?= $urlval ?>category.php?slug=<?= urlencode($productData['product']['catslug'] ?? 'Not Found'); ?>">
                    <?php echo htmlspecialchars($productData['product']['category_name'] ?? 'Not Found'); ?>
                </a>
            </li>

            <?php if (!empty($productData['product']['subcategory_name'])): ?>
                <li class="breadcrumb-item" aria-current="page">
                    <?php echo htmlspecialchars($productData['product']['subcategory_name']); ?>

                </li>
            <?php endif; ?>
        </ol>
    </nav>

    <h1 class="mb-2"><?php echo htmlspecialchars($productData['product']['product_name'] ?? 'Product Title'); ?></h1>
    <p class="text-muted mb-2"><?php echo htmlspecialchars($productData['location'] ?? 'Location'); ?></p>
    <p class="mb-2">
        <?php 
        if (!empty($productData['product']['prodate'])) {
            echo '<span style="color: #000; font-weight: bold;">Posted date:</span> ';
            echo '<span style="color: #6c757d;">' . htmlspecialchars(date('Y-m-d', strtotime($productData['product']['prodate']))) . '</span>';
        } else {
            echo '<span style="color: #6c757d;">Not find a Date</span>';
        }
        ?>
    </p>
    <h2 class="mb-4"><?php echo  $fun->getFieldData('site_currency') ?><?php echo htmlspecialchars($productData['product']['price'] ?? '0.00'); ?></h2>

    <div class="row">
    <div class="col-md-8">
        <div style="display: flex; height: 120px; justify-content: space-around; padding: 1em 0; background-color: #f7f7f7; border: 1px solid #00494f; gap:20px;margin-bottom: 20px;border-radius: 10px;">
            <button id="showGallery" class="btn toggle-btn active" style="width: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1em 0; background-color: white; color: #00494f;">
                <span style="font-size: 1.2em; margin-bottom: 0.5em;">Images</span>
                <i class="fa fa-image" style="font-size: 1.5em;"></i>
            </button>
            <button id="showMap" class="btn toggle-btn" style="width: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1em 0; background-color: white; color: #00494f;">
                <span style="font-size: 1.2em; margin-bottom: 0.5em;">Map</span>
                <i class="fa fa-map-marker-alt" style="font-size: 1.5em;"></i>
            </button>
         </div>
    <div class="card one mb-4" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    

  
    <div id="galleryContainer" class="owl-carousel owl-loaded owl-drag" style="margin-bottom: 20px; border-radius: 12px; overflow: hidden; position: relative;">
    <?php
    $totalImages = count($productData['gallery_images'] ?? []) ?: 1;
    if (!empty($productData['gallery_images'])) {
        if (isset($productData['gallery_images'][0]) && is_array($productData['gallery_images'][0])) {
            usort($productData['gallery_images'], function ($a, $b) {
                return $a['sort'] <=> $b['sort'];
            });

            foreach ($productData['gallery_images'] as $row) {
                echo '
                <div class="item" style="position: relative;">
                    <a href="' . $urlval . $row['image_path'] . '" class="popup-image">
                        <img src="' . $urlval . $row['image_path'] . '" class="card-img-top" alt="Not found Image" 
                        style="width: 100%; height: 80%; object-fit: cover; border-radius: 12px;">
                    </a>
                    <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 5px 10px; border-radius: 20px; font-size: 14px; z-index: 1000;">
                        <i class="fas fa-camera"></i> ' . $totalImages . '
                    </div>
                    <button class="view-button" 
                style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff; border: none; padding: 5px 15px; border-radius: 20px; cursor: pointer; z-index: 1000;"
                data-mfp-src="' . $urlval . $row['image_path'] . '">
            View
        </button>
                </div>';
            }
        } else {
            foreach ($productData['gallery_images'] as $imagePath) {
                echo '
                <div class="item" style="position: relative;">
                    <a href="' . $urlval . $imagePath . '" class="popup-image">
                        <img src="' . $urlval . $imagePath . '" class="card-img-top" alt="Not found Image" 
                        style="width: 100%; height: 80%; object-fit: cover; border-radius: 12px;">
                    </a>
                    <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 5px 10px; border-radius: 20px; font-size: 14px; z-index: 1000;">
                        <i class="fas fa-camera"></i> ' . $totalImages . '
                    </div>
                    <button class="view-button" 
                style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff; border: none; padding: 5px 15px; border-radius: 20px; cursor: pointer; z-index: 1000;"
                data-mfp-src="' . $urlval . $imagePath . '">
            View
        </button>
                </div>';
            }
        }
    } else {
        echo '
        <div class="item" style="position: relative;">
            <a href="' . $urlval . $productData['product']['proimage'] . '" class="popup-image">
                <img src="' . $urlval . $productData['product']['proimage'] . '" class="card-img-top" alt="Not found Image" 
                style="width: 100%; height: 80%; object-fit: cover; border-radius: 12px;">
            </a>
            <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 5px 10px; border-radius: 20px; font-size: 14px; z-index: 1000;">
                <i class="fas fa-camera"></i> ' . $totalImages . '
            </div>
            <button class="view-button" 
                style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff; border: none; padding: 5px 15px; border-radius: 20px; cursor: pointer; z-index: 1000;"
                data-mfp-src="' . $urlval . $productData['product']['proimage'] . '">
            View
        </button>
        </div>';
    }
    ?>
</div>





 
        <div id="mapContainer" style="display: none; margin-bottom: 20px; border-radius: 12px; overflow: hidden;">
        <?php 
        $cleanedLocation = preg_replace('/\|+\s*/', ',', $productData['location']);  
        $cleanedLocation = trim($cleanedLocation, ', ');  
        $encodedLocation = urlencode($cleanedLocation); 
        ?>
        <iframe width="100%" height="450" loading="lazy" allowfullscreen src="https://www.google.com/maps/embed/v1/place?key=<?php echo $fun->getSiteSettingValue('google_map_key') ?>&q=<?php echo $encodedLocation ?>&maptype=roadmap"></iframe>

        </div>

        <div class="card-body" style="padding: 1.5em; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
            <h5 class="card-title" style="font-size: 2em; font-weight: bold; color: #333; letter-spacing: 1px; margin-bottom: 0.8em; text-transform: uppercase;">
                <?= htmlspecialchars($productData['product']['product_name'] ?? 'Product Name'); ?>
            </h5>
            <div class="product-description-section">
            <?php
            $fullDescription = $productData['product']['product_description'] ?? 'No description available.';
            $wordLimit = 100;
            if (str_word_count($fullDescription) > $wordLimit) {
                $descriptionWords = explode(' ', $fullDescription);
                $shortDescription = implode(' ', array_slice($descriptionWords, 0, $wordLimit)) . '...';
                $isTruncated = true;
            } else {
                $shortDescription = $fullDescription;
                $isTruncated = false;
            }
            ?>

                <h3 class="description-heading" style="color: #333; margin-bottom: 10px;">Description</h3>
                <p style="line-height: 1.6; text-align: justify;" id="description-text">
                    <?= htmlspecialchars($shortDescription); ?>
                </p>
                <?php if ($isTruncated): ?>
                    <button id="readMoreButton" style="background: none; border: none; color: #00494f; cursor: pointer; ">Read more</button>
                    <p id="fullDescription" style="display: none; line-height: 1.6; text-align: justify;">
                        <?= htmlspecialchars($fullDescription); ?>
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>


        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                <h5 class="card-title"><?= $lan['seller_information'] ?></h5>

                <?php
                        $usid = $productData['product']['user_id'];
                        $datauserid = $dbFunctions->getDatanotenc('users', "id='$usid'");
                        $username = $datauserid[0]['username'] ?? "Not found..";
                        $firstLetter = strtoupper($username[0] ?? 'N'); 
                        $profileLink = $urlval . "user_profile.php?username=" . $username; 
                    ?>

                    <a href="<?= $profileLink ?>" class="text-decoration-none text-dark">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle text-white bg-secondary d-flex align-items-center justify-content-center" 
                                style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">
                                <?= $firstLetter ?>
                            </div>
                            <span class="ms-2" style="font-size: 1.1rem; font-weight: bold;"><?= $username ?></span>
                        </div>
                    </a>

                        <p class="card-text">
                            <small class="text-muted"><?= $lan['posting_month'] ?></small>
                        </p>

                        <p class="card-text">
                            <i class="fas fa-check-circle text-success"></i> <?= $lan['email_address_verified'] ?>
                        </p>
                    <?php
                    if (isset($_SESSION['userid'])) {
                        $sessionUserId = base64_decode($_SESSION['userid']);
                        $productId = $productData['product']['product_id'];

                        if ($sessionUserId != $productId) {
                            $encryptedProductId = $security->encrypt($productData['product']['product_id']);


                    ?>
                            <button onclick="startChat('<?= $encryptedProductId ?>')" class="btn btn-success w-100 mb-2"><?= $lan['chat'] ?></button>
                    <?php
                        }
                    } else {
                        echo '<a href="' . $urlval . 'LoginRegister.php" class="btn btn-success w-100 mb-2">' . $lan['chat'] . '</a>';
                    }
                    ?>
                    <?php

                    if ($productData['is_favorited'] == 1): ?>
                        <button class="btn buttonss w-100 mb-2" data-productid="<?php echo $productData['product']['product_id']; ?>" id="favorite-button">
                            <i class="<?php echo $productData['is_favorited'] ? 'fas' : 'far'; ?> fa-heart"></i>
                            <?php echo $productData['is_favorited'] ? $lan['Favorited'] : $lan['Favourite']; ?>
                        </button>
                    <?php else:
                        if (isset($_SESSION['userid'])) {
                            echo '
                        <button class="btn buttonss w-100 mb-2" data-productid="' . $productData['product']['product_id'] . '" id="favorite-button">
                            <i class="far fa-heart"></i> ' . $lan['Favourite'] . '
                        </button>
                            
                            ';
                        } else {
                            echo '
                                <a class="btn buttonss w-100 mb-2" href="' . $urlval . 'LoginRegister.php">
                                    <i class="far fa-heart"></i> ' . $lan['Favourite'] . '
                                </a>
                            
                            ';
                        }
                    ?>

                    <?php endif; ?>
                    <!-- <button class="btn buttonss w-100" onclick="generatePDF()">
                        <i class="fas fa-print"></i> Print
                    </button> -->
                    <!-- Action Buttons -->

                    <button class="btn btn-outline-secondary btn buttonss w-100 mt-2" id="toggleReportBtn">
                        <i class="bi bi-flag"></i> Report
                    </button>
                    <div class="container mt-5">



                    <form id="reportForm" style="display: none;">
                    <input type="hidden" name="productid" value="<?= $productData['product']['product_id'] ?>">
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reportReason" id="illegal" value="illegal">
                        <label class="form-check-label" for="illegal">
                            This is illegal/fraudulent
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reportReason" id="spam" value="spam">
                        <label class="form-check-label" for="spam">
                            This ad is spam
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reportReason" id="duplicate" value="duplicate">
                        <label class="form-check-label" for="duplicate">
                            This ad is a duplicate
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reportReason" id="wrongCategory" value="wrong_category">
                        <label class="form-check-label" for="wrongCategory">
                            This ad is in the wrong category
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="reportReason" id="rules" value="against_rules">
                        <label class="form-check-label" for="rules">
                            The ad goes against <a href="#" class="posting-rules">posting rules</a>
                        </label>
                    </div>

                    <!-- Text Area -->
                    <textarea class="form-control mb-3" rows="3" name="additionalInfo" placeholder="Please provide more information"></textarea>

                    <!-- Footer Buttons -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50" id="cancelReportBtn">Cancel</button>
                        <?php if (isset($_SESSION['userid'])): ?>
                            <button type="submit" class="btn btn-report w-50">Send report</button>
                        <?php else: ?>
                            <a href="<?= $urlval ?>LoginRegister.php" class="btn btn-report w-50">Login</a>
                        <?php endif; ?>
                    </div>
                </form>

                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="card mb-4">
                    <div class="share__wrapper">
                        <p class="share__title">Share this content:</p>
                        <ul class="share__list">
                            <li class="share__item">
                                <button class="share__link share__link--facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    <span class="sr-only">Share on Facebook</span>
                                </button>
                            </li>
                            <li class="share__item">
                                <button class="share__link share__link--twitter">
                                    <i class="fab fa-twitter"></i>
                                    <span class="sr-only">Share on Twitter</span>
                                </button>
                            </li>
                            <li class="share__item">
                                <button class="share__link share__link--linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                    <span class="sr-only">Share on LinkedIn</span>
                                </button>
                            </li>
                            <li class="share__item">
                                <button class="share__link share__link--mail">
                                    <i class="far fa-envelope"></i>
                                    <span class="sr-only">Share via Mail</span>
                                </button>
                            </li>
                            <!-- <li class="share__item">
                                <button class="share__link share__link--whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    <span class="sr-only">Share on WhatsApp</span>
                                </button>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
            

            <div class="card card-body">

                <!-- Slick Slider -->

                <div class="owl-carousel owl-theme">
    <?php
    $productMultipalinPre = $productFun->PoplarProductperMultipal();
    if ($productMultipalinPre) {
        foreach ($productMultipalinPre as $row) {
            $imgproductpre = $urlval . $row['image'];
            $detailsurl = $urlval . "detail.php?slug=" . $row['slug'];
            $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            echo '
            <div class="item">
                <a href="' . $detailsurl . '">
                    <div class="image-container">
                        <img src="' . $imgproductpre . '" class="d-block w-100" alt="Image 1">
                        <div class="image-overlay">
                            <h6 class="product-name">' . $productName . '</h6>
                        </div>
                    </div>
                </a>
            </div>';
        }
    } else {
        echo '
        <div>
            <h6 class="text-center" style="color: #198754;">Not a single product</h6>
        </div>';
    }
    ?>
</div>



            </div>

        </div>
    </div>

    <!-- <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Deliver this with AnyVan</h2>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2">
                                <i class="fas fa-check check-icon"></i>
                                Unbeatable instant prices
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check check-icon"></i>
                                Choose your date & time
                            </li>
                        </ul>
                        <button class="btn btn-get-price text-white w-100 mb-4">
                            Get instant price
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <img src="https://www.gumtree.com/assets/partnership-ads/anyvan-background-1.ef9693bb727a2143b522a1f8105a9ada.png" alt="AnyVan delivery truck" class="img-fluid">
            </div>
        </div>
    </div> -->
    <?php if ($productData['product']['product_type'] != 'standard'): ?>
        <div id="video-gallery" class="gallery-container">
            <h2 class="gallery-title"><?= $lan['video_gallery'] ?></h2>
            <div class="gallery-grid">
                <?php
                $proid = $productData['product']['product_id'];
                $getVideoGalleryData = $dbFunctions->getDatanotenc('product_videos', "product_id ='$proid'");

                if (!empty($getVideoGalleryData)) {
                    $videoPaths = explode(',', $getVideoGalleryData[0]['video_paths']); // Split the video paths

                    foreach ($videoPaths as $videoPath) {
                ?>
                        <div class="gallery-item">
                            <video class="gallery-video" controls>
                                <source src="<?= $urlval . htmlspecialchars($videoPath) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No videos available for this product.</p>";
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <h3 class="mt-4 mb-3"><b><?= $lan['you_may_also_like'] ?></b></h3>
    <div class="swiper-container my-4" style="border-radius: 12px; overflow: hidden; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);">
        <div class="swiper-wrapper">
            <?php
            $relatedProducts = $productFun->getRelatedProducts($productData['product']['category_id'], $productData['product']['product_id']);
            foreach ($relatedProducts as $relatedProduct) {
                echo '
                <div class="swiper-slide d-flex flex-column align-items-center">
                    <div class="slide-content text-center p-3">
                    <a href="' . $urlval . 'detail.php?slug=' . $relatedProduct['slug'] . '">
                        <img 
                            src="' . htmlspecialchars($urlval . $relatedProduct['image']) . '" 
                            alt="' . htmlspecialchars($relatedProduct['title']) . '" 
                            class="img-fluid rounded" 
                            style="width: 250px; height: 150px; object-fit: cover;">
                        <h5 class="mt-2" style="font-size: 1.2em; color: #333;">' . htmlspecialchars($relatedProduct['title']) . '</h5>
                        <p class="font-weight-bold text-success" style="font-size: 1.1em; margin-top: 5px;">£' . htmlspecialchars($relatedProduct['price']) . '</p>
                    </a>
                        </div>
                </div>
                ';
            }
            ?>
        </div>
        <div class="swiper-pagination secoundpage"></div>
    </div>

</div>

<?php
include_once 'footer.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    document.getElementById('toggleReportBtn').addEventListener('click', function() {
        const reportForm = document.getElementById('reportForm');
        reportForm.style.display = reportForm.style.display === 'none' ? 'block' : 'none';
    });

    document.getElementById('cancelReportBtn').addEventListener('click', function() {
        document.getElementById('reportForm').style.display = 'none';
    });
    document.addEventListener('DOMContentLoaded', function() {
        const mainSwiper = new Swiper('.swiper-container2', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
        });

        const relatedProductsSwiper = new Swiper('.swiper-container', {
            slidesPerView: 1, 
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                480: {
                    slidesPerView: 1 
                },
                600: {
                    slidesPerView: 2 
                },
                1024: {
                    slidesPerView: 3 
                },
              
            }
        });

        const favoriteButton = document.getElementById('favorite-button');

        favoriteButton.addEventListener('click', function() {
            const productId = this.getAttribute('data-productid');

            fetch('<?= $urlval ?>ajax/favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: productId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        favoriteButton.innerHTML = data.isFavorited ?
                            '<i class="fas fa-heart"></i> Favorited' :
                            '<i class="far fa-heart"></i> Favourite';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    function startChat(productId) {
        $.ajax({
            url: '<?= $urlval ?>ajax/start_chat.php',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response && response.success) {
                    window.location.href = '<?= $urlval ?>msg.php';
                } else {
                    alert(response.message || 'Could not start chat.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error connecting to chat. Please try again.');
            }
        });
    }

    $(document).ready(function() {
    $(".owl-carousel").owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 2000,
        autoplayHoverPause: true,
        nav: false, 
        dots: true 
    });
});






   
    document.addEventListener('DOMContentLoaded', function () {
        const showGalleryBtn = document.getElementById('showGallery');
        const showMapBtn = document.getElementById('showMap');
        const galleryContainer = document.getElementById('galleryContainer');
        const mapContainer = document.getElementById('mapContainer');

        function toggleView(buttonToActivate, buttonToDeactivate, containerToShow, containerToHide) {
            buttonToActivate.classList.add('active');
            buttonToActivate.style.backgroundColor = '#00494f';
            buttonToActivate.style.color = 'white';

            buttonToDeactivate.classList.remove('active');
            buttonToDeactivate.style.backgroundColor = 'white';
            buttonToDeactivate.style.color = '#00494f';

            containerToShow.style.display = 'block';
            containerToHide.style.display = 'none';
        }

        showGalleryBtn.addEventListener('click', () => {
            toggleView(showGalleryBtn, showMapBtn, galleryContainer, mapContainer);
        });

        showMapBtn.addEventListener('click', () => {
            toggleView(showMapBtn, showGalleryBtn, mapContainer, galleryContainer);
        });
    });

    document.getElementById('showMap').addEventListener('click', function() {
        document.getElementById('galleryContainer').style.display = 'none';
        document.getElementById('mapContainer').style.display = 'block';
    });
    document.addEventListener('DOMContentLoaded', function() {
        const reportForm = document.getElementById('reportForm');
        const cancelReportBtn = document.getElementById('cancelReportBtn');


        cancelReportBtn.addEventListener('click', () => {
            reportForm.style.display = 'none';
        });


        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(reportForm);

            fetch('<?= $urlval ?>ajax/processReport.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Your report has been submitted successfully.');
                        reportForm.style.display = 'none';
                    } else {
                        alert('Failed to submit the report. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    });

    window.onload = setShareLinks;

    function setShareLinks() {
        var pageUrl = encodeURIComponent(document.URL);
        var pageTitle = encodeURIComponent(document.title);

        document.addEventListener('click', function(event) {
            let url = null;

            if (event.target.classList.contains('share__link--facebook')) {
                url = "https://www.facebook.com/sharer.php?u=" + pageUrl;
                socialWindow(url, 570, 570);
            }

            if (event.target.classList.contains('share__link--twitter')) {
                url = "https://twitter.com/intent/tweet?url=" + pageUrl + "&text=" + pageTitle;
                socialWindow(url, 570, 300);
            }

            if (event.target.classList.contains('share__link--linkedin')) {
                url = "https://www.linkedin.com/shareArticle?mini=true&url=" + pageUrl;
                socialWindow(url, 570, 570);
            }

            if (event.target.classList.contains('share__link--whatsapp')) {
                url = "whatsapp://send?text=" + pageTitle + "%20" + pageUrl;
                socialWindow(url, 570, 450);
            }

            if (event.target.classList.contains('share__link--mail')) {
                url = "mailto:?subject=%22" + pageTitle + "%22&body=Read%20the%20article%20%22" + pageTitle + "%22%20on%20" + pageUrl;
                socialWindow(url, 570, 450);
            }

        }, false);
    }

    function socialWindow(url, width, height) {
        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;
        var params = "menubar=no,toolbar=no,status=no,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left;
        window.open(url, "", params);
    }
    document.addEventListener('DOMContentLoaded', function() {
        const readMoreButton = document.getElementById('readMoreButton');
        const fullDescription = document.getElementById('fullDescription');
        const shortDescription = document.getElementById('description-text');
        
        if (readMoreButton) {
            readMoreButton.addEventListener('click', function() {
                shortDescription.style.display = 'none';
                readMoreButton.style.display = 'none';
                fullDescription.style.display = 'block';
            });
        }
    });
    $(document).ready(function () {
    // Initialize Owl Carousel
    $("#galleryContainer").owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 2500,
        autoplayHoverPause: true,
        nav: true,
        navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
        dots: true,
        margin: 10
    });

    // Initialize Magnific Popup for View button
    $('.view-button').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});
</script>
</body>

</html>