
<!-- Include Swiper's CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<!-- Inline CSS for the slider -->
<style>
  /* Container for the slider section */
  .unoqyw-slider-section {
    margin: 40px auto;
    max-width: 1200px;
    padding: 20px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }
  /* Header styling */
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
  /* Swiper container styling */
  .swiper-container.unoqyw-slider {
    width: 100%;
    padding-bottom: 20px;
  }
  /* Individual slide styling */
  .swiper-slide.unoqyw-slide {
    background: #f9f9f9;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .unoqyw-slide:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  }
  /* Link styling */
  .unoqyw-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }
  /* Thumbnail styling with a 16:9 aspect ratio */
  .unoqyw-thumbnail {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 ratio */
    background-size: cover;
    background-position: center;
  }
  /* Video element styling */
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
  /* Fade in video on slide hover */
  .unoqyw-slide:hover .unoqyw-thumbnail video {
    opacity: 1;
  }
  /* Product name styling */
  .unoqyw-product-name {
    padding: 15px;
    font-size: 1.1em;
    text-align: center;
    font-weight: bold;
    background-color: #fff;
    border-top: 1px solid #eee;
  }
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .unoqyw-slider-section {
      padding: 10px;
    }
  }
</style>

<section id="unoqyw-slider-section" class="unoqyw-slider-section">
  <header class="unoqyw-slider-header">
    <h2>Premium ADs</h2>
  </header>
  <!-- Swiper Slider -->
  <div class="swiper-container unoqyw-slider">
    <div class="swiper-wrapper">
      <?php
      $getVideoGalleryData = $productFun->getPremiumProductsWithVideos();
      if (!empty($getVideoGalleryData)) {
          foreach ($getVideoGalleryData as $product) {
              if (!empty($product['videos'])) {
                  // Select a random video from available videos
                  $randomVideo = $product['videos'][array_rand($product['videos'])];
                  ?>
                  <div class="swiper-slide unoqyw-slide">
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
  </div>
</section>

<!-- Include Swiper's JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
  // Initialize Swiper with continuous autoplay settings.
  // Note: We removed pauseOnMouseEnter so we can control it manually.
  var swiper = new Swiper('.unoqyw-slider', {
    loop: true,
    speed: 5000, // duration of the transition (continuous scrolling)
    autoplay: {
      delay: 0,  // no delay between transitions
      disableOnInteraction: false
    },
    slidesPerView: 3,
    spaceBetween: 20,
    freeMode: true,
    freeModeMomentum: false,
    allowTouchMove: true,
    breakpoints: {
      768: { slidesPerView: 1 },
      992: { slidesPerView: 2 },
      1200: { slidesPerView: 3 }
    }
  });

  // Functions to immediately freeze or resume the slider
  function stopSliderImmediately() {
    swiper.autoplay.stop();
    // Immediately cancel any in-progress transition by forcing 0ms duration
    swiper.wrapperEl.style.transitionDuration = '0ms';
  }

  function resumeSliderAnimation() {
    // Remove the forced style so that Swiper can manage transitions again
    swiper.wrapperEl.style.transitionDuration = '';
    swiper.autoplay.start();
  }

  // Listen on the entire slider container to freeze movement on hover
  var sliderContainer = document.querySelector('.unoqyw-slider');
  sliderContainer.addEventListener('mouseenter', stopSliderImmediately);
  sliderContainer.addEventListener('mouseleave', resumeSliderAnimation);

  // Additionally, for each video element: play on hover and ensure the slider is stopped
  document.querySelectorAll('.unoqyw-video').forEach(function(video) {
    video.addEventListener('mouseenter', function() {
      stopSliderImmediately();
      video.play();
    });
    video.addEventListener('mouseleave', function() {
      video.pause();
      video.currentTime = 0;
      resumeSliderAnimation();
    });
  });
</script>

