<?php
require_once("../../global.php");
include_once("../header.php");

// Role check: allow only admins (roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token.
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Get all values from the form.
        $paypal_enabled = isset($_POST['paypal_enabled']) ? 1 : 0;
        $paypal_id = trim($_POST['paypal_id'] ?? '');
        
        $checkout_enabled = isset($_POST['checkout_enabled']) ? 1 : 0;
        $checkout_id = trim($_POST['checkout_id'] ?? '');
        
        $authorize_enabled = isset($_POST['authorize_enabled']) ? 1 : 0;
        $authorize_login = trim($_POST['authorize_login'] ?? '');
        $authorize_transaction_key = trim($_POST['authorize_transaction_key'] ?? '');
        
        $egold_enabled = isset($_POST['egold_enabled']) ? 1 : 0;
        $egold_payee_account = trim($_POST['egold_payee_account'] ?? '');
        $egold_payee_name = trim($_POST['egold_payee_name'] ?? '');
        // For pass phrase, verify that the two password fields match.
        $egold_pass_phrase = trim($_POST['egold_pass_phrase'] ?? '');
        $egold_confirm_pass_phrase = trim($_POST['egold_confirm_pass_phrase'] ?? '');
        if ($egold_pass_phrase !== $egold_confirm_pass_phrase) {
            $csrfError = "E-gold pass phrases do not match.";
        }
        $egold_payment_units = trim($_POST['egold_payment_units'] ?? 'US Dollar (USD)');
        $egold_metal_id = trim($_POST['egold_metal_id'] ?? 'Buyer’s Choice');
        
        // Stripe Payments
        $stripe_enabled = isset($_POST['stripe_enabled']) ? 1 : 0;
        $stripe_secret_key = trim($_POST['stripe_secret_key'] ?? '');
        $stripe_publishable_key = trim($_POST['stripe_publishable_key'] ?? '');
        
        $offline_enabled = isset($_POST['offline_enabled']) ? 1 : 0;
        $offline_details = $_POST['offline_details'] ?? '';
        
        $billing_terms_enabled = isset($_POST['billing_terms_enabled']) ? 1 : 0;
        $billing_terms = $_POST['billing_terms'] ?? '';
        
        if (empty($csrfError)) {
            // Update the settings (assuming a single row with id = 1)
            $stmt = $pdo->prepare("UPDATE billing_settings SET
                paypal_enabled = ?,
                paypal_id = ?,
                checkout_enabled = ?,
                checkout_id = ?,
                authorize_enabled = ?,
                authorize_login = ?,
                authorize_transaction_key = ?,
                egold_enabled = ?,
                egold_payee_account = ?,
                egold_payee_name = ?,
                egold_pass_phrase = ?,
                egold_payment_units = ?,
                egold_metal_id = ?,
                stripe_enabled = ?,
                stripe_secret_key = ?,
                stripe_publishable_key = ?,
                offline_enabled = ?,
                offline_details = ?,
                billing_terms_enabled = ?,
                billing_terms = ?,
                updated_at = ?
                WHERE id = 1
            ");
            $result = $stmt->execute([
                $paypal_enabled,
                $paypal_id,
                $checkout_enabled,
                $checkout_id,
                $authorize_enabled,
                $authorize_login,
                $authorize_transaction_key,
                $egold_enabled,
                $egold_payee_account,
                $egold_payee_name,
                $egold_pass_phrase,  // In production, consider encrypting sensitive data.
                $egold_payment_units,
                $egold_metal_id,
                $stripe_enabled,
                $stripe_secret_key,
                $stripe_publishable_key,
                $offline_enabled,
                $offline_details,
                $billing_terms_enabled,
                $billing_terms,
                date("Y-m-d H:i:s")
            ]);
            if ($result) {
                $message = "Billing settings updated successfully.";
            } else {
                $csrfError = "Failed to update billing settings.";
            }
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve current billing settings (assuming row id = 1)
$stmt = $pdo->prepare("SELECT * FROM billing_settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// If no settings are found, use defaults to avoid warnings.
if (!$settings) {
    $settings = [
        'paypal_enabled'            => 0,
        'paypal_id'                 => '',
        'checkout_enabled'          => 0,
        'checkout_id'               => '',
        'authorize_enabled'         => 0,
        'authorize_login'           => '',
        'authorize_transaction_key' => '',
        'egold_enabled'             => 0,
        'egold_payee_account'       => '',
        'egold_payee_name'          => '',
        'egold_pass_phrase'         => '',
        'egold_payment_units'       => 'US Dollar (USD)',
        'egold_metal_id'            => 'Buyer’s Choice',
        'stripe_enabled'            => 0,
        'stripe_secret_key'         => '',
        'stripe_publishable_key'    => '',
        'offline_enabled'           => 0,
        'offline_details'           => '',
        'billing_terms_enabled'     => 0,
        'billing_terms'             => ''
    ];
}
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Billing Settings</h3>
            <p class="mb-0">Configure your payment gateway details and billing terms below.</p>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Payment Gateways Section -->
                <div class="row">
                    <!-- PayPal Payments -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>PayPal Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="paypal_enabled" name="paypal_enabled" value="1" <?= ($settings['paypal_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="paypal_enabled">Enable payments through PayPal</label>
                                </div>
                                <div class="mb-3">
                                    <label for="paypal_id" class="form-label">PayPal ID</label>
                                    <input type="text" class="form-control" id="paypal_id" name="paypal_id" value="<?= htmlspecialchars($settings['paypal_id'] ?? '') ?>">
                                    <div class="form-text">Your PayPal email address registered as your login id.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 2Checkout Payments -->
                    <!-- <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>2Checkout Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="checkout_enabled" name="checkout_enabled" value="1" <?= ($settings['checkout_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="checkout_enabled">Enable payments through 2Checkout</label>
                                </div>
                                <div class="mb-3">
                                    <label for="checkout_id" class="form-label">2Checkout ID</label>
                                    <input type="text" class="form-control" id="checkout_id" name="checkout_id" value="<?= htmlspecialchars($settings['checkout_id'] ?? '') ?>">
                                    <div class="form-text">Your 2Checkout seller id.</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>

                <div class="row">
                    <!-- Authorize.net Payments -->
                    <!-- <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>Authorize.net Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="authorize_enabled" name="authorize_enabled" value="1" <?= ($settings['authorize_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="authorize_enabled">Enable payments through Authorize.net</label>
                                </div>
                                <div class="mb-3">
                                    <label for="authorize_login" class="form-label">Login</label>
                                    <input type="text" class="form-control" id="authorize_login" name="authorize_login" value="<?= htmlspecialchars($settings['authorize_login'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="authorize_transaction_key" class="form-label">Transaction Key</label>
                                    <input type="text" class="form-control" id="authorize_transaction_key" name="authorize_transaction_key" value="<?= htmlspecialchars($settings['authorize_transaction_key'] ?? '') ?>">
                                    <div class="form-text">Your login and transaction key configured at Authorize.net.</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- E-gold Payments -->
                    <!-- <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>E-gold Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="egold_enabled" name="egold_enabled" value="1" <?= ($settings['egold_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="egold_enabled">Enable payments through E-gold</label>
                                </div>
                                <div class="mb-3">
                                    <label for="egold_payee_account" class="form-label">Payee Account</label>
                                    <input type="text" class="form-control" id="egold_payee_account" name="egold_payee_account" value="<?= htmlspecialchars($settings['egold_payee_account'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="egold_payee_name" class="form-label">Payee Name</label>
                                    <input type="text" class="form-control" id="egold_payee_name" name="egold_payee_name" value="<?= htmlspecialchars($settings['egold_payee_name'] ?? '') ?>">
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="egold_pass_phrase" class="form-label">Pass Phrase</label>
                                        <input type="password" class="form-control" id="egold_pass_phrase" name="egold_pass_phrase" value="">
                                    </div>
                                    <div class="col">
                                        <label for="egold_confirm_pass_phrase" class="form-label">Confirm Pass Phrase</label>
                                        <input type="password" class="form-control" id="egold_confirm_pass_phrase" name="egold_confirm_pass_phrase" value="">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="egold_payment_units" class="form-label">Payment Units</label>
                                    <select name="egold_payment_units" id="egold_payment_units" class="form-control">
                                        <option value="US Dollar (USD)" <?= ($settings['egold_payment_units'] === 'US Dollar (USD)') ? 'selected' : '' ?>>US Dollar (USD)</option>
                                        
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="egold_metal_id" class="form-label">Metal Id</label>
                                    <input type="text" class="form-control" id="egold_metal_id" name="egold_metal_id" value="<?= htmlspecialchars($settings['egold_metal_id'] ?? 'Buyer’s Choice') ?>">
                                    <div class="form-text">If payment units are Troy Ounces or Grams, do not set to Buyer’s Choice.</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>

                <div class="row">
                    <!-- Stripe Payments -->
                    <!-- <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>Stripe Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="stripe_enabled" name="stripe_enabled" value="1" <?= ($settings['stripe_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="stripe_enabled">Enable payments through Stripe</label>
                                </div>
                                <div class="mb-3">
                                    <label for="stripe_secret_key" class="form-label">Stripe Secret Key</label>
                                    <input type="text" class="form-control" id="stripe_secret_key" name="stripe_secret_key" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="stripe_publishable_key" class="form-label">Stripe Publishable Key</label>
                                    <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="<?= htmlspecialchars($settings['stripe_publishable_key'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Offline Payments -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header"><strong>Offline Payments</strong></div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="offline_enabled" name="offline_enabled" value="1" <?= ($settings['offline_enabled']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="offline_enabled">Enable offline payments</label>
                                </div>
                                <div class="mb-3">
                                    <label for="offline_details" class="form-label">Offline Details</label>
                                    <textarea class="form-control" id="offline_details" name="offline_details" rows="4"><?= htmlspecialchars($settings['offline_details'] ?? '') ?></textarea>
                                    <div class="form-text">Enter your bank details, account number, and address.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Terms -->
                <div class="card mb-3">
                    <div class="card-header"><strong>Billing Terms</strong></div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="billing_terms_enabled" name="billing_terms_enabled" value="1" <?= ($settings['billing_terms_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="billing_terms_enabled">Display billing terms</label>
                        </div>
                        <div class="mb-3">
                            <label for="billing_terms" class="form-label">Billing Terms</label>
                            <textarea class="form-control" id="billing_terms" name="billing_terms" rows="6"><?= htmlspecialchars($settings['billing_terms'] ?? '') ?></textarea>
                            <div class="form-text">These terms will be displayed when adding money to the account (if enabled).</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Save Billing Settings</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    // Auto-hide alert messages after 5 seconds.
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }
});
</script>

<?php include_once("../footer.php"); ?>
