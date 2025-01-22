<?php
// Ensure session is started (typically in global.php)
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';
$lan  = $fun->loadLanguage($lang);

// Retrieve the user’s balance if logged in
$userBalance = 0; // Default balance
if (isset($_SESSION['userid'])) {
    $decodedUserId = base64_decode($_SESSION['userid']);
    $userBalance = $fun->getUserBalance($decodedUserId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fennec</title>
    <!-- Bootstrap CSS & Other External Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $urlval; ?>custom/asset/styles.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/magnific-popup.min.css">
    
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    
    <?php
    $googleAddScript   = $fun->getSiteSettingValue('google_add_script');
    $google_map_script = $fun->getSiteSettingValue('google_map_script');

    if (!empty($googleAddScript) && strpos($googleAddScript, '<script') !== false) {
        echo $googleAddScript;
    }
    if (!empty($google_map_script) && strpos($google_map_script, '<script') !== false) {
        echo $google_map_script;
    }
    ?>
    
    <style>
        /* Custom styles */
        #dropdownMenuButton {
            height: 47px;
            overflow-y: hidden;
            padding: 0px 20px;
        }
        .btn-header {
            background-color: rgb(240, 185, 4);
            border: 1px solid #008000;
            color: #008000;
            padding: 8px 12px;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 14px;
        }
        .btn-header:hover {
            background-color: #008000;
            border: 1px solid rgb(240, 185, 4);
            color: rgb(240, 185, 4);
        }
        .language-switcher {
            margin: 20px;
            display: flex;
        }
        .language-switcher select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .language-switcher select:hover {
            background-color: #e2e2e2;
        }
        /* Additional custom styles as needed */
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $urlval; ?>" style="text-decoration: none;">
            <?php
            $logoData = $fun->getBox('box1');
            $logo     = $urlval . $logoData[0]['image'];
            $title    = $logoData[0]['heading'];
            ?>
            <img src="<?php echo $logo; ?>" alt="Fennec Logo" style="max-width: 100%; margin-right: 10px;" />
            <span style="font-size: 1.7rem; font-weight: bold; color: inherit;"><?php echo $title; ?></span>
        </a>
        <button id="menuToggle" class="navbar-toggler" type="button" onclick="openNav()" style="display: none">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- SEARCH FORM -->
        <form id="searchForm" class="d-flex mx-lg-auto my-lg-0 flex-column flex-lg-row w-100 justify-content-center custom-form" onsubmit="return false">
            <?php
            $selectedLocation = isset($_GET['location']) ? $_GET['location'] : '';
            $search           = isset($_GET['search']) ? $_GET['search'] : '';
            ?>
            <div class="input-group w-50 me-lg-1 mb-2 mb-lg-0 custom-form">
                <span class="input-group-text bg-white border-0 rounded-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input id="searchInput" class="form-control p-2 rounded-0 search-input" type="search" placeholder="<?php echo $lan['Search_fennec']; ?>" aria-label="Search" value="<?php echo htmlspecialchars($search); ?>" />
            </div>
            <div class="input-group w-25 mb-2 mb-lg-0 custom-form-location">
                <span class="input-group-text rounded-0 bg-light border-0">
                    <i class="fa-solid fa-location-dot me-2"></i>
                </span>
                <select class="form-select rounded-0 location-select custom-select" id="locationSelect">
                    <option value="" selected><?php echo $lan['Select_country']; ?></option>
                    <?php
                    $countryPairs = $productFun->getCountries();
                    foreach ($countryPairs as $country) {
                        $isSelected = ($selectedLocation == $country['country_id']) ? 'selected' : '';
                        echo '<option value="' . $country['country_id'] . '" ' . $isSelected . '>' . htmlspecialchars($country['country_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="button" class="btn btn-primary btn-header mb-2 mb-lg-0" id="searchButton" style="margin-left: 10px;">
                <?php echo $lan['Search'] ?? 'Search'; ?>
            </button>
        </form>
        <div id="searchResults" class="searchResults mt-3"></div>

        <!-- LOGIN / REGISTER / USER MENU -->
        <div class="d-flex custom-loginRegister">
            <a href="<?php
            if (isset($_SESSION['userid'])) {
                echo $urlval . 'post.php';
            } else {
                echo $urlval . 'LoginRegister/';
            }
            ?>" class="btn custom-btn me-2 mb-lg-0 d-flex flex-column align-items-center">
                <i class="fa-solid fa-dollar-sign mb-1 fa-plus-circle"></i>
                <span class="new-btn"><?php echo $lan['sell']; ?></span>
            </a>

            <?php
            if (isset($_SESSION['userid'])) {
                echo '
                <div class="d-flex">
                    <a class="btn btn-outline-light me-2 position-relative" href="' . $urlval . 'Myaccount.php#Messages">
                        <i class="fas fa-envelope"></i> ' . $lan['messages'] . '
                        <span id="unread-count" class="position-absolute badge rounded-pill bg-danger" style="top: 3%; left: 57%; display: none;">0</span>
                    </a>
                    <div class="dropdown" style="top:-7px;">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bars"></i><br>
                            <p>' . $lan['menu'] . '</p>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#view-products">' . $lan['view_job_ads'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#Messages">' . $lan['messages'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#favourite">' . $lan['favourites'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#details">' . $lan['my_details'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'transaction_history.php">' . $lan['transaction_history'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'addBalance.php">Add Balance</a></li>';
                // Add Trader Stats if user is a trader (role == 2)
                if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
                    echo '<li><a class="dropdown-item" href="' . $urlval . 'trader_stats.php">Trader Stats</a></li>';
                }
                echo '<li><a class="dropdown-item" href="' . $urlval . 'logout.php">' . $lan['logout'] . '</a></li>
                        </ul>
                    </div>
                </div>';
            } else {
                echo '<a href="' . $urlval . 'LoginRegister.php" class="btn custom-btn d-flex flex-column align-items-center">
                    <i class="fa-solid fa-user mb-1"></i>
                    <span class="new-btn">' . $lan['login'] . '</span>
                </a>';
            }
            ?>
        </div>
    </div>

    <!-- LANGUAGE SWITCHER and Balance Display -->
    <div class="language-switcher">
        <?php if (isset($_SESSION['userid'])): ?>
            <div class="me-2 d-flex align-items-center text-white fw-bold">
                <span style="margin-right: 8px;"><?php echo $lan['balance'] ?? 'Balance'; ?>:</span>
                <span style="color: #FFEB3B; font-size: 1rem;"><?php echo $fun->getFieldData('site_currency') . number_format($userBalance, 2); ?></span>
                <a href="<?php echo $urlval; ?>addBalance.php" class="btn btn-success ms-2" style="padding: 5px 10px; display: inline-flex; align-items: center;">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        <?php endif; ?>
        <select id="languageSelect" onchange="changeLanguage(this.value)">
            <option value="en" <?php echo ($lang == 'en') ? 'selected' : ''; ?>>English</option>
            <?php
            $languages = $fun->FindAllLan();
            if ($languages) {
                foreach ($languages as $language) {
                    $fileName = pathinfo(basename($language['file_path']), PATHINFO_FILENAME);
                    echo '<option value="' . $fileName . '" ' . ($lang == $fileName ? 'selected' : '') . '>' . $language['language_name'] . '</option>';
                }
            }
            ?>
        </select>
    </div>
</nav>

<!-- MOBILE SIDEBAR -->
<div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="<?php echo $urlval; ?>"><?php echo $lan['home']; ?></a>
    <a href="<?php echo (isset($_SESSION['userid'])) ? $urlval . 'post.php' : $urlval . 'LoginRegister.php'; ?>">
        <?php echo $lan['post']; ?>
    </a>
    <?php if (isset($_SESSION['userid'])): ?>
        <a href="<?php echo $urlval; ?>Myaccount.php#view-products"><?php echo $lan['manage_ads']; ?></a>
        <a href="<?php echo $urlval; ?>msg.php"><?php echo $lan['messages']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#favourite"><?php echo $lan['favourites']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#details"><?php echo $lan['my_details']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#view-products"><?php echo $lan['view_job_ads']; ?></a>
        <a href="<?php echo $urlval; ?>addBalance.php">Add Balance</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
            <a href="<?php echo $urlval; ?>trader_stats.php">Trader Stats</a>
        <?php endif; ?>
    <?php endif; ?>
    <a href="<?php echo $urlval; ?>LoginRegister.php"><?php echo $lan['login']; ?></a>
</div>

<!-- NAV SUB MENU and RESPONSIVE MENU sections (if applicable) -->
<div class="nav-sub-menu-ct">
    <!-- Your existing submenu code goes here -->
</div>
<div class="respopnsive-menu321">
    <!-- Your existing responsive menu code goes here -->
</div>

<!-- Include JS libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle password visibility for login form
  function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = event.target;
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      toggleButton.textContent = "<?= 'hide' ?>";
    } else {
      passwordField.type = 'password';
      toggleButton.textContent = "<?= $lan['show'] ?>";
    }
  }

  // Toggle password visibility for register form
  function togglePassword2() {
    const passwordField = document.getElementById('registerPassword');
    const toggleButton = event.target;
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      toggleButton.textContent = "<?= 'hide' ?>";
    } else {
      passwordField.type = 'password';
      toggleButton.textContent = "<?= $lan['show'] ?>";
    }
  }

  // Handle registration AJAX submission
  $(document).on('submit', '#registerForm', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          $('#alert-message2')
            .removeClass('d-none alert-danger')
            .addClass('alert-success')
            .find('#message-content').text('Registration successful!');
          setTimeout(function() {
            window.location.href = 'index.php';
          }, 2000);
        } else {
          $('#alert-message2')
            .removeClass('d-none alert-success')
            .addClass('alert-danger')
            .find('#message-content').text(response.errors || 'An error occurred. Please try again.');
        }
      },
      error: function() {
        $('#alert-message2')
          .removeClass('d-none alert-success')
          .addClass('alert-danger')
          .find('#message-content').text('An unexpected error occurred. Please try again.');
      }
    });
  });

  // Handle login AJAX submission with role-based redirection
  $(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
      event.preventDefault();
      let url = $(this).data('url');
      $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
          $('#alert-message').removeClass('d-none fade alert-danger alert-success');
          if (response.status === 'success') {
            $('#alert-message').addClass('alert-success show');
            $('#message-content').text('Login successful! Redirecting...');
            setTimeout(function() {
              if (response.role == 2) {
                window.location.href = '<?= $urlval ?>trader_stats.php';
              } else {
                window.location.href = '<?= $urlval ?>index.php';
              }
            }, 2000);
          } else {
            $('#alert-message').addClass('alert-danger show');
            $('#message-content').text(response.message);
          }
        },
        error: function() {
          $('#alert-message').removeClass('d-none fade').addClass('alert-danger show');
          $('#message-content').text('An unexpected error occurred.');
        }
      });
    });
  });
  
  // Function to change language
  function changeLanguage(lang) {
    window.location.href = '?lang=' + lang;
  }
</script>
</body>
</html>
