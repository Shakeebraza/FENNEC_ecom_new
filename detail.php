<?php
require_once 'global.php';
include_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $slug = $_GET['slug'] ?? null;
    if (!empty($slug)) {
        $userId = isset($_SESSION['userid']) ? base64_decode($_SESSION['userid']) : NULL;
        $productData = $productFun->getProductDetailsBySlug($slug, $userId);

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
    /* Slider Item Styling */
    .slider-item {
        position: relative;
    }

    .image-container {
        position: relative;
        width: 100%;
    }

    .image-container img {
        width: 100%;
        height: auto;
    }

    /* Overlay and Product Name Styling */
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        /* Semi-transparent background */
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
        top: 200%;
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
    <h2 class="mb-4">£<?php echo htmlspecialchars($productData['product']['price'] ?? '0.00'); ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card one mb-4" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="swiper-container2" style="margin-bottom: 20px; border-radius: 12px; overflow: hidden;">
                    <div class="swiper-wrapper">
                        <?php
                        if (isset($productData['gallery_images'][0])) {
                            foreach ($productData['gallery_images'] as $row) {
                                echo '
                                    <div class="swiper-slide">
                                        <img src="' . $urlval . $row . '" class="card-img-top" alt="Not found Image" style="width: 100%; height: 80%; object-fit: cover; border-radius: 12px;">
                                    </div>
                                ';
                            }
                        } else {
                            echo '
                                <div class="">
                                    <img src="' . $urlval . $productData['product']['proimage'] . '" class="card-img-top" alt="Not found Image" style="width: 100%; height: 80%; object-fit: cover; border-radius: 12px;">
                                </div>
                            ';
                        }

                        ?>
                    </div>
                    <div class="swiper-pagination" style="bottom: 415px;"></div>
                        </div>
                        <div class="card-body" style="padding: 1.5em; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                            <h5 class="card-title" style="font-size: 2em; font-weight: bold; color: #333; letter-spacing: 1px; margin-bottom: 0.8em; text-transform: uppercase;"><?= htmlspecialchars($productData['product']['product_name'] ?? 'Product Name'); ?></h5>
                            <p class="card-text" style="font-size: 1.1em; color: #777; line-height: 1.6; text-align: justify;">
                                <?= htmlspecialchars($productData['product']['product_description'] ?? 'No description available.'); ?>
                            </p>
                        </div>

                        <div class="product-details" style="padding: 2em 1.5em; background-color: #f7f7f7; border-radius: 12px; margin-top: 1.5em; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                            <div style="margin-bottom: 1.2em;">
                                <p style="font-size: 1.2em; color: #333; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.5em;">Brand:
                                    <span style="font-size: 1.1em; color: #555; font-weight: 400;">
                                        <?= isset($productData['product']['brand']) ? ucwords(strtolower($productData['product']['brand'])) : 'N/A'; ?>
                                    </span>
                                </p>
                            </div>
                            <div style="margin-bottom: 1.2em;">
                                <p style="font-size: 1.2em; color: #333; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.5em;">Condition:
                                    <span style="font-size: 1.1em; color: #555; font-weight: 400;">
                                        <?= isset($productData['product']['conditions']) ? ucwords(strtolower($productData['product']['conditions'])) : 'N/A'; ?>
                                    </span>
                                </p>
                            </div>
                            <div style="margin-bottom: 1.2em;">
                                <p style="font-size: 1.2em; color: #333; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.5em;">Product Type:
                                    <span style="font-size: 1.1em; color: #555; font-weight: 400;">
                                        <?= isset($productData['product']['product_type']) ? ucwords(strtolower($productData['product']['product_type'])) : 'N/A'; ?>
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p style="font-size: 1.2em; color: #333; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.5em;">Created At:
                                    <span style="font-size: 1.1em; color: #555; font-weight: 400;">
                                        <?= isset($productData['product']['prodate']) ? date('F j, Y', strtotime($productData['product']['prodate'])) : 'N/A'; ?>
                                    </span>
                                </p>
                            </div>
                </div>



            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= $lan['seller_information']?></h5>
                    <p class="card-text">
                        <i class="fas fa-user"></i> <?php
                                                    $usid = $productData['product']['user_id'];
                                                    $datauserid = $dbFunctions->getDatanotenc('users', "id='$usid'");
                                                    echo $datauserid[0]['username'] ?? "Not found..";
                                                    ?><br>
                        <small class="text-muted"><?= $lan['posting_month']?></small>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-check-circle text-success"></i> <?= $lan['email_address_verified']?>
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
                        echo '<a href="' . $urlval . 'LoginRegister.php" class="btn btn-success w-100 mb-2">'.$lan['chat'].'</a>';
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
                            <i class="far fa-heart"></i> '.$lan['Favourite'].'
                        </button>
                            
                            ';
                        } else {
                            echo '
                                <a class="btn buttonss w-100 mb-2" href="' . $urlval . 'LoginRegister.php">
                                    <i class="far fa-heart"></i> '.$lan['Favourite'].'
                                </a>
                            
                            ';
                        }
                    ?>

                    <?php endif; ?>
                    <button class="btn buttonss w-100" onclick="generatePDF()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            <div class="card mb-4">
                <!-- <div class="card-body">
                    <h5 class="card-title">Deliver this with AnyVan</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Unbeatable instant prices</li>
                        <li><i class="fas fa-check text-success"></i> Choose your date & time</li>
                    </ul>
                    <button class="btn ">Get instant price</button>
                </div> -->
            </div>
            <div id="map"></div>

            <div class="card card-body">

                <!-- Slick Slider -->

                <div class="slider">
                    <?php
                    $productMultipalinPre = $productFun->PoplarProductperMultipal();
                    if ($productMultipalinPre) {
                        foreach ($productMultipalinPre as $row) {
                            $imgproductpre = $urlval . $row['image'];
                            $detailsurl = $urlval . "detail.php?slug=" . $row['slug'];
                            $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

                            echo '
                    <div class="slider-item">
                        <a href="' . $detailsurl . '">
                                <div class="image-container">
                                    <img src="' . $imgproductpre . '" class="d-block w-100" alt="Image 1">
                                    <div class="image-overlay">
                                        <h6 class="product-name">' . $productName . '</h6>
                                    </div>
                                </div>
                        </a>
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
    <?php if($productData['product']['product_type'] != 'standard'):?>
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
                            <source src="<?= $urlval.htmlspecialchars($videoPath) ?>" type="video/mp4">
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
    <?php endif;?>

    <h3 class="mt-4 mb-3"><b><?= $lan['you_may_also_like']?></b></h3>
    <div class="swiper-container my-4" style="border-radius: 12px; overflow: hidden; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);">
        <div class="swiper-wrapper">
            <?php
            $relatedProducts = $productFun->getRelatedProducts($productData['product']['category_id'], $productData['product']['product_id']);
            foreach ($relatedProducts as $relatedProduct) {
                echo '
                <div class="swiper-slide d-flex flex-column align-items-center">
                    <div class="slide-content text-center p-3">
                    <a href="'.$urlval.'detail.php?slug='.$relatedProduct['slug'].'">
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
            slidesPerView: 1, // Default to 1 slide on mobile
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                480: {
                    slidesPerView: 1 // 1 slide for mobile
                },
                600: {
                    slidesPerView: 2 // 2 slides for tablets
                },
                1024: {
                    slidesPerView: 3 // 3 slides for desktops
                },
                // You can add more breakpoints here if needed
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
        $('.slider').slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dots: true,
            autoplay: true,
            autoplaySpeed: 2000,
            fade: true,
            speed: 1000
        });
    });

    async function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const username = "<?php echo $datauserid[0]['username'] ?? 'Not found..'; ?>";
    const emailVerified = "<?= $lan['email_address_verified']; ?>";
    const productTitle = "<?= htmlspecialchars($productData['product']['product_name'], ENT_QUOTES, 'UTF-8'); ?>";
    const productPrice = "<?= htmlspecialchars($productData['product']['price'], ENT_QUOTES, 'UTF-8'); ?>";
    const productDescription = "<?= htmlspecialchars($productData['product']['product_description'], ENT_QUOTES, 'UTF-8'); ?>";
    const profileImage = "<?= htmlspecialchars($productData['product']['proimage'], ENT_QUOTES, 'UTF-8'); ?>";
    const logo = "<?= $logo ?>";


    const productUrl = window.location.href;


    const galleryImages = <?= json_encode($productData['gallery_images']) ?>;


    async function loadImageToBase64(url) {
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.readAsDataURL(blob);
            });
        } catch (error) {
            console.error("Error loading image:", url, error);
            return null;
        }
    }


    const logoBase64 = await loadImageToBase64(logo);
    const profileImageBase64 = await loadImageToBase64(profileImage);


    if (logoBase64) {
        doc.addImage(logoBase64, 'JPEG', 10, 10, 40, 20);
    }


    doc.setFontSize(18);
    doc.setTextColor(0, 102, 204);
    doc.text("Product Details", 70, 30);


    doc.setLineWidth(0.5);
    doc.line(10, 35, 200, 35);


    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    let yOffset = 50;
    doc.text(`Seller: ${username}`, 20, yOffset);
    yOffset += 10;
    doc.text(`Email Verified: ${emailVerified}`, 20, yOffset);
    yOffset += 10;
    doc.text(`Product Title: ${productTitle}`, 20, yOffset);
    yOffset += 10;
    doc.text(`Price: ${productPrice}`, 20, yOffset);
    yOffset += 10;


    doc.setFontSize(12);
    doc.setTextColor(34, 34, 34);
    doc.text("Product URL:", 20, yOffset);
    yOffset += 10;
    doc.setFontSize(11);
    doc.text(productUrl, 20, yOffset, { maxWidth: 170 });
    yOffset += 20;


    doc.setFontSize(12);
    doc.text("Description:", 20, yOffset);
    yOffset += 10;
    doc.setFontSize(11);
    doc.text(productDescription, 20, yOffset, { maxWidth: 170 });
    yOffset += 20;


    if (profileImageBase64) {
        doc.addImage(profileImageBase64, 'JPEG', 150, 50, 40, 40);
    }


    if (Array.isArray(galleryImages) && galleryImages.length > 0) {
        let xOffset = 20;
        yOffset += 30; 
        const imagesPerRow = 3;
        const imageWidth = 50;
        const imageHeight = 40;
        let imagesInRow = 0;

        for (const imageUrl of galleryImages) {
            const trimmedUrl = imageUrl.trim();
            if (trimmedUrl) {
                const imageBase64 = await loadImageToBase64(trimmedUrl);
                if (imageBase64) {
                    doc.addImage(imageBase64, 'JPEG', xOffset, yOffset, imageWidth, imageHeight);
                    xOffset += imageWidth + 10; 
                    imagesInRow++;

                    if (imagesInRow >= imagesPerRow) {
                        xOffset = 20;
                        yOffset += imageHeight + 10;
                        imagesInRow = 0;
                    }


                    if (yOffset > 250) {
                        doc.addPage();
                        yOffset = 20;
                    }
                }
            }
        }
    }


    doc.setLineWidth(0.2);
    doc.line(10, 280, 200, 280);
    doc.setFontSize(10);
    doc.setTextColor(150);
    doc.text("Generated on " + new Date().toLocaleString(), 70, 290);


    const pdfBlob = doc.output("blob");
    const pdfUrl = URL.createObjectURL(pdfBlob);
    window.open(pdfUrl, '_blank');
}



function initMap() {

    const latitude = <?php echo json_encode($latitude); ?>;
    const longitude = <?php echo json_encode($longitude); ?>;
    const country = <?php echo json_encode($country); ?>;
    const city = <?php echo json_encode($city); ?>;
    const area = <?php echo json_encode($area); ?>;

    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
        zoom: 12
    });

    const marker = new google.maps.Marker({
        position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
        map: map,
        title: `${area}, ${city}, ${country}`
    });

    const infowindow = new google.maps.InfoWindow({
        content: `<h3>${area}</h3><p>${city}, ${country}</p>`
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}


google.maps.event.addDomListener(window, 'load', initMap);

</script>
</body>

</html>