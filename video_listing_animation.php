<!-- Include Silk Slider's CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/>

<!-- Inline CSS for the slider -->
<style>
  .unoqyw-slider-section {
    margin: 40px auto;
    max-width: 1200px;
    padding: 20px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }
  .unoqyw-slider-header {
    text-align: center;
    margin-bottom: 20px;
  }
  .unoqyw-slider-header h2 {
    font-size: 2em;
    color: #333;
    margin: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #ff5722;
    display: inline-block;
  }
  .unoqyw-slider {
    width: 100%;
  }
  .unoqyw-slide {
    background: #f9f9f9;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding: 10px;
  }
  .unoqyw-slide:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  }
  .unoqyw-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .unoqyw-thumbnail {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background-size: cover;
    background-position: center;
  }
  .unoqyw-thumbnail video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.3s ease;
    border: none;
    pointer-events: all;
  }
  .unoqyw-slide:hover .unoqyw-thumbnail video {
    opacity: 1;
  }
  .unoqyw-product-name {
    padding: 15px;
    font-size: 1.1em;
    text-align: center;
    font-weight: bold;
    background-color: #fff;
    border-top: 1px solid #eee;
  }
</style>

<section id="unoqyw-slider-section" class="unoqyw-slider-section">
  <header class="unoqyw-slider-header">
    <h2>Premium Ads Video Listing</h2>
  </header>
  <div class="unoqyw-slider">
    <?php
    $getVideoGalleryData = $productFun->getPremiumProductsWithVideos();
    if (!empty($getVideoGalleryData)) {
        foreach ($getVideoGalleryData as $product) {
            if (!empty($product['videos'])) {
                $randomVideo = $product['videos'][array_rand($product['videos'])];
                ?>
                <div class="unoqyw-slide">
                  <a href="detail.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="unoqyw-link">
                    <div class="unoqyw-thumbnail" style="background-image: url('<?= htmlspecialchars(trim($product['image'])) ?>');">
                      <video class="unoqyw-video" controls preload="metadata" poster="<?= htmlspecialchars(trim($product['image'])) ?>">
                        <source src="<?= htmlspecialchars(trim($randomVideo)) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                      </video>
                    </div>
                    <div class="unoqyw-product-name">
                      <?= htmlspecialchars($product['name']) ?>
                    </div>
                  </a>
                </div>
                <?php
            }
        }
    } else {
        echo '<div class="unoqyw-no-videos" style="text-align:center; padding:20px; color:#777;">No premium videos available at the moment.</div>';
    }
    ?>
  </div>
</section>

<!-- Include Silk Slider's JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>

<script>
  $(document).ready(function(){
    $('.unoqyw-slider').slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 0,
      speed: 5000,
      cssEase: 'linear',
      infinite: true,
      arrows: false,
      dots: false,
      pauseOnHover: true,
      responsive: [
        {
          breakpoint: 1200,
          settings: { slidesToShow: 2 }
        },
        {
          breakpoint: 768,
          settings: { slidesToShow: 1 }
        }
      ]
    });

    // Pause slider on video hover
    $('.unoqyw-video').hover(function(){
      $('.unoqyw-slider').slick('slickPause');
      this.play();
    }, function(){
      this.pause();
      this.currentTime = 0;
      $('.unoqyw-slider').slick('slickPlay');
    });
  });
</script>
