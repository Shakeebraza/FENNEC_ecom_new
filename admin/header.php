<?php
$setSession = $fun->isSessionSetAdmin();

$redirectUrl = $urlval . 'admin/logout.php';

// Ensure session is set
if (!$setSession) {
    echo '<script>window.location.href = "' . htmlspecialchars($redirectUrl) . '";</script>';
    exit();
}

// Check role [1,3,4]
if (!in_array($_SESSION['arole'], [1, 3, 4])) {
    echo '<script>window.location.href = "' . htmlspecialchars($redirectUrl) . '";</script>';
    exit();
}

// Define role details
$roleDetails = [
    1 => ['label' => 'SA',  'color' => 'danger',  'icon' => 'fa-shield-alt'], // Super Admin
    3 => ['label' => 'ADM', 'color' => 'success', 'icon' => 'fa-user-cog'],  // Admin
    4 => ['label' => 'MOD', 'color' => 'warning', 'icon' => 'fa-user-tie'],  // Moderator
];
$roleInfo = $roleDetails[$_SESSION['arole']] ?? ['label' => 'NA', 'color' => 'secondary', 'icon' => 'fa-user'];

// For highlighting menu items
$current_url = $_SERVER['REQUEST_URI'];
function isActive($link) {
    global $current_url;
    return (strpos($current_url, $link) !== false) ? 'active' : '';
}

$defaultProfile = $urlval . 'images/admin.jpg';

if (!empty($_SESSION['aprofile']) && file_exists($_SESSION['aprofile'])) {
    $profile = $_SESSION['aprofile'];
} else {
    $profile = $defaultProfile;
}


$seoTitle             = $fun->getData('site_settings', 'value', 11);
$seoTitleEnabled      = $fun->getData('approval_parameters', 'seo_param_title', 1);

$seoDescription       = $fun->getData('site_settings', 'value', 12);
$seoDescriptionEnabled= $fun->getData('approval_parameters', 'seo_param_description', 1);

$seoKeywords          = $fun->getData('site_settings', 'value', 13);
$seoKeywordsEnabled   = $fun->getData('approval_parameters', 'seo_param_keyword', 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">


    <!-- <title>Dashboard</title> -->
    <!-- SEO Meta Tags -->
    <?php if (strtolower($seoTitleEnabled) === 'enabled'): ?>
        <title><?= htmlspecialchars($seoTitle . ' Dashboard') ?></title>
    <?php else: ?>
        <title>Fennec</title>
    <?php endif; ?>

    <?php if (strtolower($seoDescriptionEnabled) === 'enabled'): ?>
        <meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
    <?php endif; ?>

    <?php if (strtolower($seoKeywordsEnabled) === 'enabled'): ?>
        <meta name="keywords" content="<?= htmlspecialchars($seoKeywords) ?>">
    <?php endif; ?>


    <link href="<?php echo $urlval?>admin/asset/css/font-face.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet"
        media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet"
        media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/mdi-font/css/material-design-iconic-font.min.css"
        rel="stylesheet" media="all">


    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <link href="<?php echo $urlval?>admin/asset/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css"
        rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet"
        media="all">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link href="<?php echo $urlval?>admin/asset/css/theme.css" rel="stylesheet" media="all">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css" />
    <style>
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='black' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");

    }

    @media (max-width: 991px) {
        .mobileview {
            display: flex;
            justify-content: space-between;
        }
    }
    </style>

</head>

<body class="animsition">
    <div class="page-wrapper">
        <?php include_once('sidebar.php');?>

        <header class="header-desktop">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="header-wrap">
                        <!-- Search bar -->
                        <form class="form-header" action="" method="POST">
                            <input class="au-input au-input--xl" type="text" name="search"
                                placeholder="Search for datas &amp; reports..." />
                            <button class="au-btn--submit" type="submit">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </form>

                        <div class="header-button">
                            <div class="noti-wrap">
                                <a href="<?php echo htmlspecialchars($urlval) ?>admin/messange.php">
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-comment-more <?= isActive('/admin/messange.php'); ?>"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="account-wrap">
                                <div class="account-item clearfix js-item-menu">
                                    <div class="image">
                                        <img src="<?php echo htmlspecialchars($profile); ?>" alt="Profile" />
                                    </div>
                                    <div class="content">
                                        <!-- Show username & role badge inline -->
                                        <a class="js-acc-btn" href="#">
                                            <?php echo htmlspecialchars($_SESSION['ausername']); ?>
                                        </a>
                                        <!-- small badge next to name -->
                                        <span class="badge-role bg-<?php echo htmlspecialchars($roleInfo['color']); ?>"
                                            style="padding:3px 7px; border-radius:5px; margin-left:5px;">
                                            <i class="fas <?php echo htmlspecialchars($roleInfo['icon']); ?>"></i>
                                            <?php echo htmlspecialchars($roleInfo['label']); ?>
                                        </span>
                                    </div>
                                    <div class="account-dropdown js-dropdown">
                                        <div class="info clearfix">
                                            <div class="image">
                                                <a href="#">
                                                    <img src="<?php echo htmlspecialchars($profile); ?>"
                                                        alt="Profile" />
                                                </a>
                                            </div>
                                            <div class="content">
                                                <h5 class="name">
                                                    <a
                                                        href="#"><?php echo htmlspecialchars($_SESSION['ausername']); ?></a>
                                                </h5>
                                                <span
                                                    class="email"><?php echo htmlspecialchars($_SESSION['aemail']); ?></span>
                                                <div class="mt-2">
                                                    <span
                                                        class="badge-role bg-<?php echo htmlspecialchars($roleInfo['color']); ?>">
                                                        <i
                                                            class="fas <?php echo htmlspecialchars($roleInfo['icon']); ?>"></i>
                                                        <?php echo htmlspecialchars($roleInfo['label']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="account-dropdown__body">
                                            <div class="account-dropdown__item">
                                                <a href="<?= htmlspecialchars($urlval) ?>admin/account.php">
                                                    <i class="zmdi zmdi-account"></i>Account
                                                </a>
                                            </div>
                                            <div class="account-dropdown__item">
                                                <a href="<?= htmlspecialchars($urlval) ?>admin/changepassword.php">
                                                    <i class="zmdi zmdi-settings"></i>Change Password
                                                </a>
                                            </div>
                                            <!-- <div class="account-dropdown__item">
                                                <a href="#">
                                                    <i class="zmdi zmdi-settings"></i>Setting
                                                </a>
                                            </div>
                                            <div class="account-dropdown__item">
                                                <a href="#">
                                                    <i class="zmdi zmdi-money-box"></i>Billing
                                                </a>
                                            </div> -->
                                        </div>
                                        <div class="account-dropdown__footer">
                                            <a href="<?= htmlspecialchars($urlval) ?>admin/logout.php">
                                                <i class="zmdi zmdi-power"></i>Logout
                                            </a>
                                        </div>
                                    </div><!-- .account-dropdown -->
                                </div><!-- .account-item -->
                            </div><!-- .account-wrap -->
                        </div><!-- .header-button -->
                    </div><!-- .header-wrap -->
                </div><!-- .container-fluid -->
            </div><!-- .section__content -->
        </header>
        <!-- rest of your content -->
    </div><!-- .page-wrapper -->