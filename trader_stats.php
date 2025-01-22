<?php
require_once 'global.php';
// Ensure session is started in global.php or elsewhere
if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    header("Location: index.php");
    exit();
}

// Fetch trader statistics from your database or functions here
// For demonstration purposes, I'm using static placeholder values.
// Replace these with your dynamic values.
$totalClassifiedsPosted    = 9;
$currentActiveClassifieds  = 9;
$totalExpiredClassifieds   = '--';  // replace with actual value if available
$classifiedsExpiringToday  = '--';  // replace with actual value if available
$accountBalance            = 999962.30; // example numeric value
$totalMessagesInbox        = 1;
$totalMessagesOutbox       = 4;
$newMessages               = 0;  // or the appropriate value

// Format currency (use your site's currency symbol)
$currencySymbol = $fun->getFieldData('site_currency') ?: '$';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trader Stats</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- Custom Styles (if any) -->
    <link rel="stylesheet" href="<?php echo $urlval; ?>custom/asset/styles.css" />
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
    </style>
</head>
<body>
    <?php include_once 'header.php'; ?>

    <div class="container stats-container">
        <div class="stats-header text-center">
            <h1>Trader Statistics</h1>
            <p class="text-muted">Overview of your classified account activity</p>
        </div>

        <div class="row g-4">
            <!-- Total Classifieds Posted -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Total Classifieds Posted</h3>
                    <p><?php echo $totalClassifiedsPosted; ?></p>
                </div>
            </div>

            <!-- Currently Active Classifieds -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Currently Active Classifieds</h3>
                    <p><?php echo $currentActiveClassifieds; ?></p>
                </div>
            </div>

            <!-- Total Expired Classifieds -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Total Expired Classifieds</h3>
                    <p><?php echo $totalExpiredClassifieds; ?></p>
                </div>
            </div>

            <!-- Classifieds Expiring Today -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Classifieds Expiring Today</h3>
                    <p><?php echo $classifiedsExpiringToday; ?></p>
                </div>
            </div>

            <!-- Account Balance -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Account Balance</h3>
                    <p><?php echo $currencySymbol . number_format($accountBalance, 2); ?></p>
                </div>
            </div>

            <!-- Total Messages (Inbox) -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Total Messages (Inbox)</h3>
                    <p><?php echo $totalMessagesInbox; ?></p>
                </div>
            </div>

            <!-- Total Messages (Outbox) -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>Total Messages (Outbox)</h3>
                    <p><?php echo $totalMessagesOutbox; ?></p>
                </div>
            </div>

            <!-- New Messages -->
            <div class="col-md-4">
                <div class="stat">
                    <h3>New Messages</h3>
                    <p><?php echo $newMessages; ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
