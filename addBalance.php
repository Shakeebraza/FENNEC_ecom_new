<?php
// 1) Include global and header for site-wide code, database class, session, etc.
require_once 'global.php';      // Contains session_start, $fun, $urlval, etc.
require_once 'dbcon/Database.php';
include_once 'header.php';      // Renders the navbar and <body> tag

// 2) Confirm user is logged in (similar to your other pages)
$setSession = $fun->isUserSessionSet();
if (!$setSession) {
    // If not logged in, redirect
    $redirectUrl = $urlval . 'index.php';
    echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
    exit();
}

// 3) Get user ID from session
$userId = intval(base64_decode($_SESSION['userid'])) ?? 0;

// PayPal configuration
// $paypalUrl = "https://www.paypal.com/cgi-bin/webscr";

$paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr"; // Use production URL for live payments
$businessEmail = $fun->getSiteSettingValue('paypal_email_address');
?>

<!-- Main Content -->
<div class="container my-5">
    <!-- Page Heading -->
    <div class="text-center mb-4">
        <h1 class="display-6 fw-bold">Add Balance</h1>
        <p class="text-muted">Add funds to your account to pay for premium ads or other services.</p>
    </div>

    <!-- Card Wrapper -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <!-- Add Balance Form -->
                    <form id="paypalForm" method="POST" action="<?= $paypalUrl ?>">
                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Amount to Add</label>
                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                class="form-control"
                                step="0.01"
                                min="0.01"
                                required
                            >
                        </div>

                        <!-- PayPal Hidden Fields -->
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="business" value="<?= $businessEmail ?>">
                        <input type="hidden" name="item_name" value="Add Balance">
                        <input type="hidden" name="item_number" value="<?= $userId ?>">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="return" value="<?= $urlval ?>success.php?user_id=<?= $userId ?>">
                        <input type="hidden" name="cancel_return" value="<?= $urlval ?>cancel.php">

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="Myaccount.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-wallet me-1"></i> Proceed to PayPal
                            </button>
                        </div>
                    </form>
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->

<?php
include_once 'footer.php';
?>
