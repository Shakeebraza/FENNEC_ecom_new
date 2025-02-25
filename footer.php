<?php
// Retrieve the video option setting from approval_parameters (assuming id = 1)
$videoOption = $fun->getData('approval_parameters', 'video_option_throughout', 1);

// Check if the video option is enabled (case-insensitive comparison)
if (strtolower($videoOption) === 'enabled') :
include 'video_listing_animation.php';
?>
<!-- <div id="premium-video-section" class="premium-container" style="margin-bottom: 40px;">
    <h2 class="premium-title"><?= $lan['premium_video_listing'] ?></h2>
    <div class="premium-grid">
        <?php
        $getVideoGalleryData = $productFun->getPremiumProductsWithVideos();

        if (!empty($getVideoGalleryData)) {
            foreach ($getVideoGalleryData as $product) {
                if (!empty($product['videos'])) {
                    $randomVideo = $product['videos'][array_rand($product['videos'])];
                    ?>
                    <div class="premium-item">
                        <div class="video-thumbnail" style="background-image: url('<?= htmlspecialchars(trim($product['image'])) ?>');">
                            <video class="premium-video" controls>
                                <source src="<?= htmlspecialchars(trim($randomVideo)) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <a href="detail.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="premium-product-link">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </div>
                    <?php
                }
            }
        } else {
            echo "<p>No premium videos available at the moment.</p>";
        }
        ?>
    </div>
</div> -->
<?php
endif;
?>




<footer class="text-light pt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4">
        <a class="navbar-brand" href="index.php" style="text-decoration: none;">
          <img
            src="<?php echo $logo ?>"
            alt="Fennec Logo"
            style="max-width: 50%; margin-right: 10px;
              height: 40px; padding-bottom: 10px;" />
          <span style="font-size: 2rem; font-weight: bold; color: white; height: 90px;"><?= $title ?></span>
        </a>
        <div class="line">
          <p style="color:white;" ><?= $phara ?></p>
        </div>
        <div class="d-flex space-x-3 icon-sty d-flex-new">
          <a href="<?php echo $fun->getSiteSettingValue('facebook_link') ?>" class="text-light hover:text-white"><i class="fab fa-facebook-f"></i></a>
          <a href="<?php echo $fun->getSiteSettingValue('instagram_link') ?>" class="text-light hover:text-white"><i class="fab fa-instagram"></i></a>

          <a href="<?php echo $fun->getSiteSettingValue('twitter_link') ?>" class="text-light hover:text-white"><i class="fab fa-twitter"></i></a>

        </div>
      </div>
      <?php
      $menus = $fun->getMenus();
      ?>
      <?php
      if (isset($menus)) {
        foreach ($menus as $menuData):
          // var_dump($menuData);
      ?>
          <div class="col-md-4 mb-4">
            <h4 class="text-white font-weight-bold"><?php echo htmlspecialchars($menuData['menu']['name']); ?></h4>
            <ul class="list-unstyled line">
              <?php foreach ($menuData['items'] as $item):
                if (!empty($item['link'])) {
                  $link = $item['link'];
                } else {
                  $link = $urlval . 'page.php?slug=' . $item['slug'];
                }
              ?>
                <li>
                  <a href="<?= $link ?>" class="text-light hover:text-white">
                    <?php echo htmlspecialchars($item['name']); ?>
                  </a>
                </li>
              <?php 
            endforeach; 
            
            if($menuData['menu']['id'] == 2){
              echo '
              
                            <li>
                  <a href="'.$urlval.'/contactus.php" class="text-light hover:text-white">
                    Contact us
                  </a>
                </li>
              ';
            }
            ?>


            </ul>
          </div>
      <?php endforeach;
      } ?>
    </div>
    <hr>

    <div class="mt-4 pb-3 text-center">
      <p style="color:white;" ><?= $lan['copyright']?></p>
    </div>
  </div>
</footer>



<script src="<?php echo $urlval?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>



<script src="<?php echo $urlval?>custom/js/index.js"></script>
<script src="<?php echo $urlval?>custom/js/forsale.js"></script>
<script src="<?php echo $urlval?>custom/js/script.js"></script>

<script>
/**
 * Change site language
 */
function changeLanguage(languageFile) {
    // Adjust if your language switch logic differs
    window.location.href = '?lang=' + languageFile;
}

/**
 * Open side navigation (mobile)
 */
function openNav() {
    document.getElementById("mySidebar").style.width = "70%";
}

/**
 * Close side navigation (mobile)
 */
function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
}

/**
 * Get URL parameter by name
 */
function getUrlParameter(name) {
    var url = new URL(window.location.href);
    var params = new URLSearchParams(url.search);
    return params.get(name);
}

$(document).ready(function() {
    // =============================================
    // 1. FETCH UNREAD MESSAGES (Badge in Menu)
    // =============================================
    function fetchUnreadMessages() {
        $.ajax({
            url: "<?= $urlval?>ajax/unread_messages.php",
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Update menu badge
                if (response.unread_count > 0) {
                    $("#unread-count").text(response.unread_count).show();
                } else {
                    $("#unread-count").hide();
                }

                // Update trader stats page
                if ($("#unread-stat-count").length) {
                    if (response.unread_count > 0) {
                        $("#unread-stat-count").text(response.unread_count).show();
                    } else {
                        $("#unread-stat-count").text('0');
                    }
                }
            },
            error: function() {
                console.error("Error fetching unread messages.");
            }
        });
    }
    // Immediately fetch unread messages
    fetchUnreadMessages();

    // =============================================
    // 2. NAV MENU HOVER (Desktop)
    // =============================================
    $('.nav-men-sub-ct-inn ul li').hover(
        function() {
            $(this).find('.nav-main-dwdisnmn').stop(true, true).slideDown(200);
        },
        function() {
            $(this).find('.nav-main-dwdisnmn').stop(true, true).slideUp(200);
        }
    );

    // =============================================
    // 3. AUTOCOMPLETE SUGGESTIONS WHEN TYPING
    // =============================================

    // 1) AUTOCOMPLETE: If user hits Enter => we do not rely on relative path
    $('#searchInput').on('input', function() {
        let query = $(this).val();
        let location = getUrlParameter('location');

        if (query.length > 0) {
            $.ajax({
                url: '<?php echo $urlval; ?>ajax/search.php', // absolute path
                type: 'GET',
                data: { q: query, location: location },
                success: function(data) {
                    $('#searchResults').html(data).show();
                }
            });
        } else {
            $('#searchResults').hide();
        }
    });

    // =============================================
    // 4. PRESS ENTER => REDIRECT TO FIRST SUGGESTION
    // =============================================
    // $('#searchInput').on('keypress', function(e) {
    //     if (e.which === 13) {
    //         let firstResult = $('#searchResults .suggestion-item a').first();
    //         if (firstResult.length) {
    //             e.preventDefault();
    //             window.location.href = firstResult.attr('href');
    //         }
    //     }
    // });

    // =============================================
    // 5. CLICK OUTSIDE => HIDE SUGGESTIONS
    // =============================================
    $(document).on('click', function(event) {
        // Hide if click outside search form
        if (!$(event.target).closest('#searchForm').length) {
            $('#searchResults').hide();
        }
    });

    // =============================================
    // 6. EXPLICIT SEARCH BUTTON
    // =============================================
    $('#searchButton').on('click', function() {
        // Build the absolute URL for search results
        var cityId      = $('#locationSelect').val();
        var searchQuery = $('#searchInput').val().trim();
        var url         = '<?php echo $urlval; ?>category.php?'; // <--- absolute reference

        if (cityId) {
            url += 'location=' + encodeURIComponent(cityId) + '&';
        }
        if (searchQuery) {
            url += 'search=' + encodeURIComponent(searchQuery);
        }

        window.location.href = url;
    });
    // =============================================
    // 7. LOCATION SELECT => AUTO REDIRECT
    //    (If you only want the Search Button to finalize,
    //    remove or comment out this event listener.)
    // =============================================
    document.getElementById('locationSelect').addEventListener('change', function() {
        var cityId = this.value;
        // ...
        // Build absolute path
        var url = '<?php echo $urlval; ?>category.php?location=' + cityId;

        var pid = getUrlParameter('pid');
        var searchQuery = document.getElementById('searchInput').value;
        if (pid) {
            url += '&pid=' + pid;
        }
        if (searchQuery) {
            url += '&search=' + encodeURIComponent(searchQuery);
        }

        if (cityId) {
            window.location.href = url;
        }
    });
});
</script>
