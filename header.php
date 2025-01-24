<?php

// Ensure session is started somewhere above this code (e.g., in global.php).
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';
$lan  = $fun->loadLanguage($lang);

// -----------------------------------------------------------
// Optional: retrieve the user’s balance if the user is logged in
$userBalance = 0; // Default
if (isset($_SESSION['userid'])) {
    // Convert from base64 (based on your code style)
    $decodedUserId = base64_decode($_SESSION['userid']);

    // If you have a method like $fun->getUserBalance($decodedUserId),
    // use it here:
    $userBalance = $fun->getUserBalance($decodedUserId);
    // $userBalance = 100.00;
    // Or if your $dbFunctions can fetch the user record:
    //   $result      = $dbFunctions->getDatanotenc('users', "id = '$decodedUserId'");
    //   $userBalance = $result[0]['wallet_balance'] ?? 0;
    
    // For demonstration, if you don’t yet have a method:
    // $userBalance = 123.45; // example placeholder
}
// -----------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fennec</title>
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
    $googleAddScript     = $fun->getSiteSettingValue('google_add_script');
    $google_map_script   = $fun->getSiteSettingValue('google_map_script');

    if (!empty($googleAddScript) && strpos($googleAddScript, '<script') !== false) {
        echo $googleAddScript;
    }
    if (!empty($google_map_script) && strpos($google_map_script, '<script') !== false) {
        echo $google_map_script;
    }
    ?>

    <style>
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
            display: inline-block;
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
        .watermark {
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: bold;
            text-align: center;
            pointer-events: none;
            user-select: none;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 2px;
            -webkit-text-stroke: 1px rgba(0, 0, 0, 0.7);
        }
        .premium-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .premium-item {
            width: calc(25% - 20px);
            position: relative;
            overflow: hidden;
        }
        .video-thumbnail {
            width: 100%;
            padding-top: 56.25%;
            background-size: cover;
            background-position: center;
            position: relative;
            cursor: pointer;
            border-radius: 10px;
            height: 80%;
        }
        .premium-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            pointer-events: none;
        }
        .video-thumbnail:hover .premium-video {
            opacity: 1;
            pointer-events: auto;
        }
        @media (max-width: 768px) {
            .premium-item {
                width: 100%;
            }
            .nav-men-sub-res-ct-inn ul {
                padding: 10px !important;
                width: 98vw !important;
            }
            .nav-men-sub-res-ct-inn ul li {
                text-wrap: nowrap !important;
                text-decoration: none;
            }
            span.input-group-text.bg-white.border-0.rounded-0 {
                height: 40px !important;
            }
        }
        div#mySidebar {
            color: white !important;
        }
        div#mySidebar a {
            color: white !important;
        }
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
            $phara    = $logoData[0]['phara'];
            ?>
            <img src="<?php echo $logo; ?>" alt="Fennec Logo" style="max-width: 100%; margin-right: 10px;" />
            <span style="font-size: 1.7rem; font-weight: bold; color: inherit;"><?php echo $title; ?></span>
        </a>
        <button id="menuToggle" class="navbar-toggler" type="button" onclick="openNav()" style="display: none">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- SEARCH FORM -->
        <form
            id="searchForm"
            class="d-flex mx-lg-auto my-lg-0 flex-column flex-lg-row w-100 justify-content-center custom-form"
            onsubmit="return false"
        >
            <?php
            $selectedLocation = isset($_GET['location']) ? $_GET['location'] : '';
            $search           = isset($_GET['search']) ? $_GET['search'] : '';
            ?>
            <!-- Search Input Group -->
            <div class="input-group w-50 me-lg-1 mb-2 mb-lg-0 custom-form">
                <span class="input-group-text bg-white border-0 rounded-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input
                    id="searchInput"
                    class="form-control p-2 rounded-0 search-input"
                    type="search"
                    placeholder="<?php echo $lan['Search_fennec']; ?>"
                    aria-label="Search"
                    value="<?php echo !empty($search) ? htmlspecialchars($search) : ''; ?>"
                />
            </div>

            <!-- Location Select Group -->
            <div class="input-group w-25 mb-2 mb-lg-0 custom-form-location">
                <span class="input-group-text rounded-0 bg-light border-0">
                    <i class="fa-solid fa-location-dot me-2"></i>
                </span>
                <select
                    class="form-select rounded-0 location-select custom-select"
                    id="locationSelect"
                >
                    <option value="" selected><?php echo $lan['Select_country']; ?></option>
                    <?php
                    $countryPairs = $productFun->getCountries();
                    foreach ($countryPairs as $country) {
                        $isSelected = ($selectedLocation == $country['country_id']) ? 'selected' : '';
                        echo '<option value="' . $country['country_id'] . '" ' . $isSelected . '>'
                            . htmlspecialchars($country['country_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Search Button -->
            <button
                type="button"
                class="btn btn-primary btn-header mb-2 mb-lg-0"
                id="searchButton"
                style="margin-left: 10px;"
            >
                <?php echo $lan['Search'] ?? 'Search'; ?>
            </button>
        </form>

        <!-- Container to show the auto-complete suggestions -->
        <div id="searchResults" class="searchResults mt-3"></div>

        <!-- LOGIN / REGISTER / USER MENU -->
        <div class="d-flex custom-loginRegister">
            <a href="<?php
            if (isset($_SESSION['userid'])) {
                echo $urlval . 'post.php';
            } else {
                echo $urlval . 'LoginRegister/';
            }
            ?>"
               class="btn custom-btn me-2 mb-lg-0 d-flex flex-column align-items-center">
                <i class="fa-solid fa-dollar-sign mb-1 fa-plus-circle"></i>
                <span class="new-btn"><?php echo $lan['sell']; ?></span>
            </a>

           <?php
            // If user is logged in, show balance + messages + dropdown
            if (isset($_SESSION['userid'])) {

                echo '
                <div class="d-flex">
                    <!-- Messages Button with Badge -->
                    <a class="btn btn-outline-light me-2 position-relative" href="' . $urlval . 'Myaccount.php#Messages">
                        <i class="fas fa-envelope"></i> ' . $lan['messages'] . '
                        <span id="unread-count" 
                              class="position-absolute badge rounded-pill bg-danger" 
                              style="top: 3%; left: 57%; display: none;">
                              0
                        </span>
                    </a>
                    
                    <!-- Dropdown Menu -->
                    <div class="dropdown" style="top:-7px;">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bars"></i> 
                            <br>
                            <p>' . $lan['menu'] . '</p>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#view-products">' . $lan['view_job_ads'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#Messages">' . $lan['messages'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#favourite">' . $lan['favourites'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'Myaccount.php#details">' . $lan['my_details'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'transaction_history.php">' . $lan['transaction_history'] . '</a></li>
                            <li><a class="dropdown-item" href="' . $urlval . 'addBalance.php">Add Balance</a></li>
                            
                            <!-- Trader Stats Link (only for role = 2) -->
                            ';
                if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
                    echo '<li><a class="dropdown-item" href="' . $urlval . 'trader_stats.php">Trader Stats</a></li>';
                }
                echo '
                            <li><a class="dropdown-item" href="' . $urlval . 'logout.php">' . $lan['logout'] . '</a></li>
                        </ul>
                    </div>
                </div>
                ';
            } else {
                // If user not logged in
                echo '
                <a href="' . $urlval . 'LoginRegister.php" class="btn custom-btn d-flex flex-column align-items-center">
                    <i class="fa-solid fa-user mb-1 "></i>
                    <span class="new-btn">' . $lan['login'] . '</span>
                </a>
                ';
            }
            ?>
        </div>
    </div>

    <!-- LANGUAGE SWITCHER + BALANCE SNIPPET -->
    <div class="language-switcher">
        <?php if (isset($_SESSION['userid'])): ?>
            <!-- Show user balance with a plus button -->
            <div class="me-2 d-flex align-items-center text-white fw-bold">
                <span style="margin-right: 8px;"><?php echo $lan['balance'] ?? 'Balance'; ?>:</span>
                <span style="color: #FFEB3B; font-size: 1rem;">
                    <?php echo $fun->getFieldData('site_currency') . number_format($userBalance, 2); ?>
                </span>
                
                <!-- Plus button linking to addBalance.php -->
                <a 
                    href="<?php echo $urlval; ?>addBalance.php" 
                    class="btn btn-success ms-2" 
                    style="padding: 5px 10px; display: inline-flex; align-items: center;"
                >
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        <?php endif; ?>

        <!-- Existing language dropdown remains the same -->
        <select id="languageSelect" onchange="changeLanguage(this.value)">
            <option value="en" <?php echo ($lang == 'en') ? 'selected' : ''; ?>>
                English
            </option>
            <?php
            $languages = $fun->FindAllLan();
            if ($languages) {
                foreach ($languages as $language) {
                    $fileName = pathinfo(basename($language['file_path']), PATHINFO_FILENAME);
                    echo '<option value="' . $fileName . '" ' . ($lang == $fileName ? 'selected' : '') . '>'
                        . $language['language_name'] . '</option>';
                }
            }
            ?>
        </select>
    </div>

</nav>

<!-- MOBILE SIDEBAR -->
 <!-- MOBILE SIDEBAR -->
<div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="<?php echo $urlval; ?>"><?php echo $lan['home']; ?></a>
    <a href="<?php echo isset($_SESSION['userid']) ? $urlval . 'post.php' : $urlval . 'LoginRegister.php'; ?>">
        <?php echo $lan['post']; ?>
    </a>
    <?php if (isset($_SESSION['userid'])): ?>
        <a href="<?php echo $urlval; ?>Myaccount.php#upload"><?php echo $lan['manage_ads']; ?></a>
        <a href="<?php echo $urlval; ?>msg.php"><?php echo $lan['messages']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#favourite"><?php echo $lan['favourites']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#details"><?php echo $lan['my_details']; ?></a>
        <a href="<?php echo $urlval; ?>Myaccount.php#view-products"><?php echo $lan['view_job_ads']; ?></a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
            <a href="<?php echo $urlval; ?>trader_stats.php">Trader Stats</a>
        <?php endif; ?>
    <?php endif; ?>
    <a href="<?php echo $urlval; ?>logout.php"><?php echo $lan['logout']; ?></a>
    
    <!-- Trader Stats Link (visible only for role = 2) -->

</div>



<!-- NAV SUB MENU -->
<div class="nav-sub-menu-ct">
    <div class="nav-menu-32323">
        <div class="nav-menu-3344343">
            <div class="nav-sub-menu-inn1">
                <div class="nav-men-sub-ct-inn">
                    <ul>
                        <?php
                        $browse_by = $lan['browse_by'] ?? 'Browse by';
                        $findCate  = $categoryManager->getAllCategoriesHeaderMenu();
                        if ($findCate['status'] == 'success') {
                            foreach ($findCate['data'] as $category) {
                                echo '
                                <li class="' . htmlspecialchars($category['slug']) . '">
                                    <a href="' . $urlval . 'category/?slug=' . $category['slug'] . '">'
                                        . htmlspecialchars($category['category_name']) . '
                                    </a>
                                    <div class="nav-main-dwdisnmn" style="display:none;">
                                        <div class="nav-snm-innnn">
                                            <h2>' . $browse_by . '</h2>
                                            <div class="div-nv-sb-menu">
                                                <ul>';

                                $duncatdata = $categoryManager->getAllSubCategoriesHeaderMenu($category['id']);
                                foreach ($duncatdata['data'] as $val) {
                                    echo '<li class="lihpoverset">
                                            <a href="' . $urlval . 'category/?slug=' . $category['slug']
                                                . '&subcategory=' . htmlspecialchars($val['id']) . '">'
                                                . htmlspecialchars(ucwords(strtolower($val['subcategory_name'])))
                                            . '</a>
                                          </li>';
                                }

                                $productPremium = $productFun->PoplarProductper();

                                echo '
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="div-img-right-submenu" style="width:20%;">';

                                if (!empty($productPremium)) {
                                    echo '<a href="' . $urlval . 'detail/?slug=' . $productPremium['slug'] . '">
                                            <img src="' . $urlval . $productPremium['image'] . '" alt="" style="width:100%">
                                          </a>';
                                } else {
                                    echo '<img src="https://www.gumtree.com/assets/frontend/cars-guide.84c7d8c8754c04a88117e49a84535413.png" alt="">';
                                }
                                echo '
                                        </div>
                                    </div>
                                </li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RESPONSIVE MENU -->
<div class="respopnsive-menu321">
    <div class="nav-menu-res-3344343">
        <div class="nav-sub-menu-res-inn1">
            <div class="nav-men-sub-res-ct-inn">
                <ul>
                    <?php
                    $findCate = $categoryManager->getAllCategoriesHeaderMenu();
                    if ($findCate['status'] == 'success') {
                        foreach ($findCate['data'] as $category) {
                            echo '
                            <li class="car-vhcl-menu-res" data-id="' . htmlspecialchars($category['id']) . '">'
                                . htmlspecialchars($category['category_name']) . '
                            </li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="remenu-sub">
        <?php
        $browse_by = $lan['browse_by'] ?? 'Browse by';
        if ($findCate['status'] == 'success') {
            foreach ($findCate['data'] as $category) {
                ?>
                <div class="remenu-main-dw" data-id="<?php echo htmlspecialchars($category['id']); ?>" style="display:none;">
                    <div class="remenu-innnn">
                        <div class="div-sub-321">
                            <img class="crs-end"
                                 src="<?php echo $urlval; ?>custom/asset/delete-button.png"
                                 alt="Delete Button"
                            >
                            <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                        </div>
                        <h2><?php echo $browse_by; ?></h2>
                        <ul>
                            <?php
                            $duncatdata = $categoryManager->getAllSubCategoriesHeaderMenu($category['id']);
                            if (!empty($duncatdata['data'])) {
                                foreach ($duncatdata['data'] as $val) {
                                    ?>
                                    <li>
                                        <a href="<?php echo $urlval; ?>category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>&subcategory=<?php echo $val['id']; ?>">
                                            <?php echo htmlspecialchars($val['subcategory_name']); ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                            } else {
                                echo '<li>' . $lan['No_subcategories_found'] . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
