<?php
require_once 'global.php';
// session_start(); // Already handled in global.php

// Enable error reporting for debugging (remove or disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and has a role of trader (role value 2)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    header("Location: index.php");
    exit();
}

// Load language settings
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';
$lan  = $fun->loadLanguage($lang);

// Check if $lan is successfully loaded
if (!isset($lan) || !is_array($lan)) {
    // Handle the error appropriately
    die("Language files not loaded properly.");
}

$userId = base64_decode($_SESSION['userid']);

// Fetch trader statistics using your custom function
$allAds = $productFun->getProductsForUserexp($userId, $lan, 'all');
$totalClassifiedsPosted = !empty($allAds) ? count($allAds) : 0;

// Get active ads using filter 'active'
$activeAds = $productFun->getProductsForUserexp($userId, $lan, 'active');
$currentActiveClassifieds = !empty($activeAds) ? count($activeAds) : 0;

// Get expired ads using filter 'expired'
$expiredAds = $productFun->getProductsForUserexp($userId, $lan, 'expired');
$totalExpiredClassifieds = !empty($expiredAds) ? count($expiredAds) : 0;

// Calculate classifieds expiring today based on each ad's created_at and extension
$classifiedsExpiringToday = 0;
$today = new DateTime(date("Y-m-d"));
if (!empty($allAds)) {
    foreach ($allAds as $ad) {
        // Make sure created_at field exists
        if (isset($ad['created_at'])) {
            $createdDate = new DateTime($ad['created_at']);
            // Determine expiry days based on extension value (assume 60 for extension == 1, otherwise 30)
            $expiryDays = ($ad['extension'] == 1) ? 60 : 30;
            $expiryDate = (clone $createdDate)->modify("+$expiryDays days");
            // If the computed expiry date is equal to today's date, increment counter
            if ($expiryDate->format("Y-m-d") == $today->format("Y-m-d")) {
                $classifiedsExpiringToday++;
            }
        }
    }
}

// Fetch account balance dynamically (ensure this method exists and returns a numeric value)
$accountBalance = $fun->getUserBalance($userId);

// Fetch unread messages count (handled via AJAX in footer.php)
// The stat box will be updated by the existing AJAX function

// Format currency using your site's currency symbol
$currencySymbol = $fun->getFieldData('site_currency') ?: '$';

$seoTitle             = $fun->getData('site_settings', 'value', 11);
$seoTitleEnabled      = $fun->getData('approval_parameters', 'seo_param_title', 1);

$seoDescription       = $fun->getData('site_settings', 'value', 12);
$seoDescriptionEnabled= $fun->getData('approval_parameters', 'seo_param_description', 1);

$seoKeywords          = $fun->getData('site_settings', 'value', 13);
$seoKeywordsEnabled   = $fun->getData('approval_parameters', 'seo_param_keyword', 1);
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <?php if (strtolower($seoTitleEnabled) === 'enabled'): ?>
        <title><?= htmlspecialchars($seoTitle . ' Trader Statistics') ?></title>
    <?php else: ?>
        <title>Fennec</title>
    <?php endif; ?>

    <?php if (strtolower($seoDescriptionEnabled) === 'enabled'): ?>
        <meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
    <?php endif; ?>

    <?php if (strtolower($seoKeywordsEnabled) === 'enabled'): ?>
        <meta name="keywords" content="<?= htmlspecialchars($seoKeywords) ?>">
    <?php endif; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo $urlval; ?>custom/asset/styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .stats-container {
            margin-top: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stat {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stat h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .stat p {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .stats-header {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-container {
                padding: 20px;
            }
            .stat {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'header.php'; ?>

    <div class="container stats-container">
        <div class="stats-header text-center">
            <h1><?php echo htmlspecialchars($lan['Trader_Stats'] ?? 'Trader Statistics'); ?></h1>
            <p class="text-muted"><?php echo htmlspecialchars($lan['Stats_Overview'] ?? 'Overview of your classified account activity'); ?></p>
        </div>

        <div class="row g-4">
            <!-- Total Classifieds Posted -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Total_Classifieds_Posted'] ?? 'Total Classifieds Posted'); ?></h3>
                    <p><?php echo htmlspecialchars($totalClassifiedsPosted); ?></p>
                </div>
            </div>

            <!-- Currently Active Classifieds -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Currently_Active_Classifieds'] ?? 'Currently Active Classifieds'); ?></h3>
                    <p><?php echo htmlspecialchars($currentActiveClassifieds); ?></p>
                </div>
            </div>

            <!-- Total Expired Classifieds -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Total_Expired_Classifieds'] ?? 'Total Expired Classifieds'); ?></h3>
                    <p><?php echo htmlspecialchars($totalExpiredClassifieds); ?></p>
                </div>
            </div>

            <!-- Classifieds Expiring Today -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Classifieds_Expiring_Today'] ?? 'Classifieds Expiring Today'); ?></h3>
                    <p><?php echo htmlspecialchars($classifiedsExpiringToday); ?></p>
                </div>
            </div>

            <!-- Account Balance -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Account_Balance'] ?? 'Account Balance'); ?></h3>
                    <p><?php echo htmlspecialchars($currencySymbol . number_format($accountBalance, 2)); ?></p>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="col-md-4">
                <div class="stat">
                    <h3><?php echo htmlspecialchars($lan['Unread_Messages'] ?? 'Unread Messages'); ?></h3>
                    <p id="unread-stat-count">0</p>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
