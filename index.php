<?php
require_once 'global.php';
include_once 'header.php';


?>
<style>
/* Popup Overlay */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
}

/* Popup Content */
.popup-content {
    background: linear-gradient(145deg, #fff, #f1f1f1);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 90%;
    max-width: 450px;
    position: relative;
    transform: translateY(-50px);
    transition: transform 0.4s ease;
}

/* Show Animation */
.popup-overlay.show {
    opacity: 1;
    visibility: visible;
}

.popup-content.show {
    transform: translateY(0);
}

/* Close Button */
.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    color: #ff4d4d;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close-btn:hover {
    color: #e63946;
}

/* Product Image */
.product-image {
    width: 100%;
    height: auto;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Headings and Text */
h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 15px;
}

h3 {
    font-size: 22px;
    color: #666;
    margin-bottom: 10px;
}

p {
    font-size: 16px;
    color: #777;
    margin-bottom: 20px;
}

/* Button Styling */
.btn2 {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 18px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn2:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
}

.sidebar-box {
    margin-bottom: 30px;
    border-radius: 8px;
}

.sidebar-box .slider {
    margin-top: 20px;
}

.sidebar-box .slider img {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.sidebar-box .slider img:hover {
    transform: scale(1.05);
    /* Slight zoom effect on hover */
}

.sidebar-box h6 {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    color: #333;
}

@media (max-width: 768px) {
    .sidebar-box .slider {
        margin-top: 10px;
    }

    .sidebar-box h6 {
        font-size: 12px;
    }

    .carousel-indicators {
        display: none;
    }

    .fls-rs-bl {
        display: block !important;
    }

    .bnn-mn-ct {
        width: 100%;
        max-width: 100%;
        height: auto;
        overflow: hidden;
        position: relative;
    }

    .bnner-vt-txt {
        margin-left: 0px !important;
    }

    .bnneer-btn-rs {
        width: 100% !important;
    }

}

.carousel-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
}
.carousel-control-prev, .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.5); 
        width: 50px;
        height: 78%;
        border-radius: 5px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carousel-control-prev:hover, .carousel-control-next:hover {
        background-color: rgba(0, 0, 0, 0.7); 
    }

 
</style>


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
<?php
// Retrieve the video option setting from approval_parameters (assuming id = 1)
$videoOption = $fun->getData('approval_parameters', 'video_option_home', 1);
if (strtolower($videoOption) === 'enabled') :
include_once 'video_listing_animation.php';
?>  
<?php
endif;
?> 
<div class="custom-slider-container">

    <div class="custom-slide custom-fade">
        <img class="custom-slide-image" src="custom/asset/car1.jpg">
    </div>

    <div class="custom-slide custom-fade">
        <img class="custom-slide-image"
            src="https://images.unsplash.com/photo-1682687220566-5599dbbebf11?ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1075&q=80">

    </div>

    <div class="custom-slide custom-fade">
        <img class="custom-slide-image"
            src="https://images.unsplash.com/photo-1682685797828-d3b2561deef4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80">
    </div>

</div>

<div class="container">


    <div class="row">
        <div class="col-md-12">
            <div class="container mt-5">


                <h3 class="mt-5"><?= $lan['Featured_Categories'] ?></h3>
                <div class="container mt-5">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $findCate = $categoryManager->getAllCategoriesHeaderMenu();

            if ($findCate['status'] == 'success') {
                $active = true;
                $count = 0;

                foreach ($findCate['data'] as $index => $category) {
                    if ($count % 4 == 0) {
                        if ($count > 0) {
                            echo '</div></div>';
                        }
                        echo '<div class="carousel-item ' . ($active ? 'active' : '') . '" data-bs-interval="10000">';
                        echo '<div class="row">';
                        $active = false;
                    }
            ?>
            <div class="col-md-3">
                <div class="card crt-timg-hm">
                    <a href="<?= $urlval ?>category/?slug=<?= $category['slug'] ?>">
                        <img src="<?php echo htmlspecialchars($urlval . $category['category_image']); ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($category['category_name']); ?>" />
                    </a>
                    <div class="card-body">
                        <p class="card-text crt-txt-hm">
                            <?php echo htmlspecialchars($category['category_name']); ?></p>
                    </div>
                </div>
            </div>
            <?php
                    $count++;
                    if ($count == count($findCate['data'])) {
                        echo '</div></div>';
                    }
                }
            } else {
                echo '<p>No categories available</p>';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

            </div>
            <?php
            $box2 = $fun->getBox('box2');
            $image2 = $urlval . $box2[0]['image'];

            $productMultipalinPrebanner = $productFun->PoplarProductperMultipal();
                                    
            ?>
            <div class="container mt-4">
                <h5 class="text-center mb-5">
                    <b><?= $lan['top_products'] ?></b>
                </h5>
                <?php if($productMultipalinPrebanner):?>
                <div id="customCarousel" class="carousel slide" data-bs-ride="carousel">

                    <div class="carousel-indicators">
                        <?php foreach ($productMultipalinPrebanner as $index => $item): ?>
                        <button type="button" data-bs-target="#customCarousel" data-bs-slide-to="<?= $index; ?>"
                            class="<?= $index === 0 ? 'active' : ''; ?>" aria-current="true"
                            aria-label="Slide <?= $index + 1; ?>"></button>
                        <?php endforeach; ?>
                    </div>


                    <div class="carousel-inner">
                        <?php foreach ($productMultipalinPrebanner as $index => $item): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                            <div class="banner" style="box-shadow: 4px 3px 6px #A4A4A485;">
                                <div class="row align-items-center p">
                                    <div class="col-md-3 mb-3 mb-md-0 p-0">
                                        <img src="<?= $item['image']; ?>" alt="Slide <?= $index + 1; ?>"
                                            class="img-fluid" />
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h3><?= $item['name']; ?></h3>
                                        <p><?= $item['description']; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <?php $detailsurl = $urlval . "p/" . urlencode($item['slug']) . '/' . urlencode($item['name']) . '/' . urlencode($item['description']);?>
                                        <a href="<?= $detailsurl ?>" class="btn btn-success w-50">Click now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#customCarousel"
                        data-bs-slide="prev"
                        style="background-color: #00000091;width: 31px; border-radius: 50%; background-size: 100%; width: 40px;  height: 40px; margin-top: 104px;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#customCarousel"
                        data-bs-slide="next"
                        style="background-color: #00000091;width: 31px; border-radius: 50%; background-size: 100%; width: 40px;  height: 40px; margin-top: 104px;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <?php
                endif;
                ?>
                <div class="row mt-5">
                    <!-- Sidebar Section -->
                    <div class="col-md-3 mb-4">
                        <!-- Premium Products Slider -->
                        <?php
                                $productMultipalinPre = $productFun->PoplarProductperMultipal();
                                if($productMultipalinPre):
                                ?>
                        <div class="sidebar-box"
                            style="box-shadow: 4px 3px 6px #A4A4A485; padding: 20px; background-color: white; border: 2px solid #198754;">
                            <h5 class="text-center" style="color: #198754;"><?= $lan['premium_products'] ?></h5>
                            <div class="slider" style="background-color: #fef5e6; padding: 10px;">
                                <?php
                               
                                if ($productMultipalinPre) {
                                    foreach ($productMultipalinPre as $row) {
                                        $imgproductpre = $urlval . $row['image'];
                                        $detailsurl = $urlval . "p/" . urlencode($row['slug']) . '/' . urlencode($row['name']) . '/' . urlencode($row['description']);
                                        $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

                                        echo '
                                            <div>
                                                <a href="' . $detailsurl . '">
                                                    <img src="' . $imgproductpre . '" alt="' . $productName . '" class="img-fluid">
                                                </a>
                                                <h6 class="text-center" style="color: #198754;">' . $productName . '</h6>
                                            </div>
                                        ';
                                                            }
                                                        } else {
                                                            echo '
                                        <div>
                                            <h6 class="text-center" style="color: #198754;">Not a single product</h6>
                                        </div>
                                    ';
                                                        }


                                ?>
                            </div>
                        </div>
                        <?php
                              endif;
                                ?>

                        <!-- Gold Products Slider -->
                        <div class="sidebar-box"
                            style="box-shadow: 4px 3px 6px #A4A4A485; padding: 20px; background-color: white; border: 2px solid #198754;">
                            <h5 class="text-center" style="color: #198754;"><?= $lan['top_products'] ?></h5>
                            <div class="slider" style="background-color: #fef5e6; padding: 10px;">
                                <?php
                                $productMultipalinPre = $productFun->PoplarProductMuultipal();
                                if ($productMultipalinPre) {
                                    foreach ($productMultipalinPre as $row) {
                                        $imgproductpre = $urlval . $row['image'];
                                        $detailsurl = $urlval . "p/" . urlencode($row['slug']) . '/' . urlencode($row['name']) . '/' . urlencode($row['description']);
                                        $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

                                        echo '
                                                <div>
                                                    <a href="' . $detailsurl . '">
                                                        <img src="' . $imgproductpre . '" alt="' . $productName . '" class="img-fluid">
                                                    </a>
                                                    <h6 class="text-center" style="color: #198754;">' . $productName . '</h6>
                                                </div>
                                            ';
                                                                }
                                                            } else {
                                                                echo '
                                            <div>
                                                <h6 class="text-center" style="color: #198754;">Not a single product</h6>
                                            </div>
                                        ';
                                }


                                ?>
                            </div>
                        </div>

                        <!-- Top Products Slider -->
                        <div class="sidebar-box"
                            style="box-shadow: 4px 3px 6px #A4A4A485; padding: 20px; background-color: white; border: 2px solid #198754;">
                            <h5 class="text-center" style="color: #198754;"><?= $lan['gold_products'] ?></h5>
                            <div class="slider" style="background-color: #fef5e6; padding: 10px;">
                                <?php
                                $productMultipalinPre = $productFun->PoplarProductgoldMultipal();
                                if ($productMultipalinPre) {
                                    foreach ($productMultipalinPre as $row) {
                                        $imgproductpre = $urlval . $row['image'];
                                        $detailsurl = $urlval . "p/" . urlencode($row['slug']) . '/' . urlencode($row['name']) . '/' . urlencode($row['description']);
                                        $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

                                        echo '
                                            <div>
                                                <a href="' . $detailsurl . '">
                                                    <img src="' . $imgproductpre . '" alt="' . $productName . '" class="img-fluid">
                                                </a>
                                                <h6 class="text-center" style="color: #198754;">' . $productName . '</h6>
                                            </div>
                                        ';
                                                            }
                                                        } else {
                                                            echo '
                                        <div>
                                            <h6 class="text-center" style="color: #198754;">Not a single product</h6>
                                        </div>
                                    ';
                                                        }


                                ?>
                            </div>
                        </div>
                        <?php
                        $banner = $fun->getRandomBannerByPlacement('home_sidebar');
                        if (!empty($banner)) {
                        ?>

                        <div
                            style="width: 100% !important; background-color: <?php echo $banner['bg_color']; ?>; color: <?php echo $banner['text_color']; ?>; min-height: 30vh; position: relative;width: 15vw; display: flex; flex-direction: column; padding: 2rem;">
                            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                                <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $banner['title']; ?></h1>
                                <h2
                                    style="font-size: 3rem; font-weight: 300;  color: <?php echo $banner['text_color']; ?>">
                                    <?php echo $banner['description']; ?></h2>
                                <img src="<?php echo $banner['image']; ?>" alt=""
                                    style="width: 100%; height: 280px; margin-bottom:20px; border-radius:7px;">
                            </div>


                            <?php if ($banner['btn_text'] && $banner['btn_url']) : ?>
                            <a href="<?php echo $banner['btn_url']; ?>"
                                style="background-color: <?php echo $banner['btn_color']; ?>; padding: 1rem 2rem; color: black; text-decoration: none; display: flex; align-items: center; justify-content: space-between; width: fit-content; margin-top: auto; transition: background-color 0.3s ease;">
                                <?php echo $banner['btn_text']; ?>
                                <span style="margin-left: 1rem;">→</span>
                            </a>
                            <?php endif; ?>
                        </div>

                        <?php
                        }
                        ?>
                    </div>



                    <!-- Products Section -->
                    <div class="col-md-9">



                        <div class="row">
                            <?php
                            $productFind = $productFun->getProductsWithDetails(1, 15, []);

                            if (!empty($productFind)) {
                                foreach ($productFind['products'] as $product) {
                                    $setSession = $fun->isSessionSet();
                                    $fav = "";

                                    if ($setSession == true) {
                                        $uid = base64_decode($_SESSION['userid']);
                                        $pid = $product['id'];
                                        $isFav = $dbFunctions->getDatanotenc('favorites', "user_id = '$uid' AND product_id = '$pid'");

                                        if ($isFav) {
                                            $fav = "style='color: red'";
                                        }
                                    }
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="product-card position-relative">
                                    <?php if ($product['product_type'] != "standard") { ?>
                                    <div class="badge bg-success position-absolute top-0 start-0 m-2">
                                        <?php echo $product['product_type']; ?>
                                    </div>
                                    <?php } ?>
                                    <a
                                        href="<?= $urlval.'p/'.urlencode($product['slug']).'/'.urlencode($product['name']).'/'.urlencode($product['description']) ?>">
                                        <img src="<?php echo $product['image']; ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            class="product-image w-100" />
                                        <?php
                                                if ($product['product_type'] == "standard") {
                                                    echo '<div class="watermark">' . $title . '</div>';
                                                }
                                                ?>
                                    </a>

                                    <?php if ($setSession == true) { ?>
                                    <a class="heart-icon icon_heart" data-productid="<?php echo $product['id']; ?>"
                                        id="favorite-button-<?php echo $product['id']; ?>">
                                        <i class="fas fa-heart" <?php echo $fav; ?>></i>
                                    </a>
                                    <?php } else { ?>
                                    <a class="heart-icon" href="<?= $urlval ?>LoginRegister">
                                        <i class="fas fa-heart"></i>
                                    </a>
                                    <?php } ?>

                                    <div class="p-3">
                                        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="price"><?php echo  $fun->getFieldData('site_currency') ?>
                                            <?php echo htmlspecialchars($product['price']); ?></p>
                                        <p class="location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($product['country']); ?> |
                                            <?php echo htmlspecialchars($product['city'] ?? ''); ?>
                                        </p>
                                        <p class="date">
                                            <i class="far fa-clock"></i>
                                            <?php echo htmlspecialchars($product['date']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo '<p>No products found.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal" style="display: none">

            </div>
        </div>
    </div>
</div>
<div class="container mt-4 mb-5">
    <span class="tp-lctn-mn">
        <h5 class="text-center mb-5 tp-lct-icn" id="topLocationsToggle" style="cursor: pointer;">
            <b><?= $lan['top_locations'] ?></b>
        </h5>
    </span>
    <div id="topLocationsContent" style="display: none;">
        <!-- <h6 class="text-center mb-2"><b>Top Cities</b></h6> -->
        <?php
        $topLocations = $fun->TopLocations();
        if ($topLocations['status'] === 'success') {
            $data = $topLocations['data'];
            $locationsByCountry = [];

            // Group cities by country
            foreach ($data as $location) {
                $countryName = $location['country_name'];
                $cityName = $location['city_name'];
                $cityId = $location['city_id'];  // Access the city_id now

                if (!isset($locationsByCountry[$countryName])) {
                    $locationsByCountry[$countryName] = [];
                }
                if (!empty($cityName)) {
                    $locationsByCountry[$countryName][] = [
                        'name' => $cityName,
                        'id' => $cityId
                    ];
                }
            }
        ?>

        <h6 class="text-center mb-2"><b><?= $lan['top_cities'] ?></b></h6>

        <?php foreach ($locationsByCountry as $country => $cities): ?>
        <h6 class="text-center"><b><?php echo htmlspecialchars($country); ?></b></h6>
        <div class="row text-center justify-content-center mb-3">
            <div class="col-md-10">
                <p class="location-list">
                    <?php
                            // Create links for each city
                            $cityLinks = array_map(function ($city) use ($urlval) {
                                return '<a href="' . htmlspecialchars($urlval) . 'category/?location=' . htmlspecialchars($city['id']) . '">' . htmlspecialchars($city['name']) . '</a>';
                            }, $cities);
                            echo implode(' | ', $cityLinks);
                            ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>

        <?php } else { ?>
        <div class="alert alert-danger text-center">
            <strong>Error:</strong> <?php echo htmlspecialchars($topLocations['message']); ?>
        </div>
        <?php } ?>

    </div>
</div>

<!-- Popup Modal -->
<!-- <div id="popupModal" class="popup-overlay">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>

        <h2><?= $lan['popular_product'] ?></h2>
        <div class="product-details">
            <?php
            $product = $productFun->PoplarProduct();

            if ($product) {
                echo "<p>{$product['description']}</p>";
                echo '<img src="' . $urlval . $product['image'] . '" alt="' . $product['name'] . '" class="product-image">';
                echo '<h3>' . $product['name'] . '</h3>';
                echo '<p>' . $product['description'] . '</p>';
                echo '<a href="detail.php?slug=' . $product['slug'] . '" class="btn2">View Product</a>';
            } else {
                echo "<p>" . $lan['popular_product_error'] . "</p>";
            }

            ?>


        </div>
    </div>
</div> -->

<?php
include_once 'footer.php';
?>

<script>
document.querySelectorAll('.icon_heart').forEach(favoriteButton => {
    favoriteButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default anchor behavior
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
                    this.innerHTML = data.isFavorited ?
                        '<i class="fas fa-heart" style="color: red;"></i>' :
                        '<i class="far fa-heart" style="color: red;"></i>';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});

document.getElementById('topLocationsToggle').addEventListener('click', function() {
    var content = document.getElementById('topLocationsContent');
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
    } else {
        content.style.display = 'none';
    }
});

// Function to show the popup with smooth animation
// function showPopup() {
//   const popupOverlay = document.getElementById('popupModal');
//   const popupContent = popupOverlay.querySelector('.popup-content');

//   popupOverlay.classList.add('show');
//   setTimeout(() => {
//     popupContent.classList.add('show');
//   }, 100);
// }

// Function to close the popup
// function closePopup() {
//   const popupOverlay = document.getElementById('popupModal');
//   const popupContent = popupOverlay.querySelector('.popup-content');

//   popupContent.classList.remove('show');
//   setTimeout(() => {
//     popupOverlay.classList.remove('show');
//   }, 400);
// }

// Automatically show the popup after a few seconds


$(document).ready(function() {
    $('.slider').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        autoplay: true,
        autoplaySpeed: 2000,
    });
});
</script>
</body>

</html>