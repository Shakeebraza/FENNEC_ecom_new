<?php
require_once("../../global.php");
include_once("../header.php");

// Role check: allow only admins (roles 1 and 3) to access this page.
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
        // Retrieve fee values and the associated enabled flags.
        $signup_bonus         = trim($_POST['signup_bonus'] ?? '0.00');
        $signup_bonus_enabled = isset($_POST['signup_bonus_enabled']) ? 1 : 0;
        
        $posting_fee          = trim($_POST['posting_fee'] ?? '0.00');
        $posting_fee_enabled  = isset($_POST['posting_fee_enabled']) ? 1 : 0;
        $apply_same_posting_fee = isset($_POST['apply_same_posting_fee']) ? 1 : 0;
        
        $extension_fee        = trim($_POST['extension_fee'] ?? '0.00');
        $extension_fee_enabled= isset($_POST['extension_fee_enabled']) ? 1 : 0;
        
        $buy_now_fee          = trim($_POST['buy_now_fee'] ?? '0.00');
        $buy_now_fee_enabled  = isset($_POST['buy_now_fee_enabled']) ? 1 : 0;
        
        $fee_bold             = trim($_POST['fee_bold'] ?? '0.00');
        $fee_bold_enabled     = isset($_POST['fee_bold_enabled']) ? 1 : 0;
        
        $fee_featured         = trim($_POST['fee_featured'] ?? '0.00');
        $fee_featured_enabled = isset($_POST['fee_featured_enabled']) ? 1 : 0;
        
        $fee_front_featured   = trim($_POST['fee_front_featured'] ?? '0.00');
        $fee_front_featured_enabled = isset($_POST['fee_front_featured_enabled']) ? 1 : 0;
        
        $fee_image_gallery_featured = trim($_POST['fee_image_gallery_featured'] ?? '0.00');
        $fee_image_gallery_featured_enabled = isset($_POST['fee_image_gallery_featured_enabled']) ? 1 : 0;
        
        $fee_video_gallery_featured = trim($_POST['fee_video_gallery_featured'] ?? '0.00');
        $fee_video_gallery_featured_enabled = isset($_POST['fee_video_gallery_featured_enabled']) ? 1 : 0;
        
        $fee_highlight        = trim($_POST['fee_highlight'] ?? '0.00');
        $fee_highlight_enabled= isset($_POST['fee_highlight_enabled']) ? 1 : 0;
        
        // Update the fees (assuming a single row with id = 1)
        $stmt = $pdo->prepare("UPDATE billing_fees SET
            signup_bonus = ?,
            signup_bonus_enabled = ?,
            posting_fee = ?,
            posting_fee_enabled = ?,
            apply_same_posting_fee = ?,
            extension_fee = ?,
            extension_fee_enabled = ?,
            buy_now_fee = ?,
            buy_now_fee_enabled = ?,
            fee_bold = ?,
            fee_bold_enabled = ?,
            fee_featured = ?,
            fee_featured_enabled = ?,
            fee_front_featured = ?,
            fee_front_featured_enabled = ?,
            fee_image_gallery_featured = ?,
            fee_image_gallery_featured_enabled = ?,
            fee_video_gallery_featured = ?,
            fee_video_gallery_featured_enabled = ?,
            fee_highlight = ?,
            fee_highlight_enabled = ?,
            updated_at = ?
            WHERE id = 1
        ");
        $result = $stmt->execute([
            $signup_bonus,
            $signup_bonus_enabled,
            $posting_fee,
            $posting_fee_enabled,
            $apply_same_posting_fee,
            $extension_fee,
            $extension_fee_enabled,
            $buy_now_fee,
            $buy_now_fee_enabled,
            $fee_bold,
            $fee_bold_enabled,
            $fee_featured,
            $fee_featured_enabled,
            $fee_front_featured,
            $fee_front_featured_enabled,
            $fee_image_gallery_featured,
            $fee_image_gallery_featured_enabled,
            $fee_video_gallery_featured,
            $fee_video_gallery_featured_enabled,
            $fee_highlight,
            $fee_highlight_enabled,
            date("Y-m-d H:i:s")
        ]);
        if ($result) {
            $message = "Billing fees updated successfully.";
        } else {
            $csrfError = "Failed to update billing fees.";
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve current billing fees (assuming row id = 1)
$stmt = $pdo->prepare("SELECT * FROM billing_fees WHERE id = 1");
$stmt->execute();
$fees = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$fees) {
    // Provide default values to avoid warnings.
    $fees = [
        'signup_bonus' => '0.00',
        'signup_bonus_enabled' => 0,
        'posting_fee'  => '0.00',
        'posting_fee_enabled' => 0,
        'apply_same_posting_fee' => 0,
        'extension_fee' => '0.00',
        'extension_fee_enabled' => 0,
        'buy_now_fee' => '0.00',
        'buy_now_fee_enabled' => 0,
        'fee_bold' => '0.00',
        'fee_bold_enabled' => 0,
        'fee_featured' => '0.00',
        'fee_featured_enabled' => 0,
        'fee_front_featured' => '0.00',
        'fee_front_featured_enabled' => 0,
        'fee_image_gallery_featured' => '0.00',
        'fee_image_gallery_featured_enabled' => 0,
        'fee_video_gallery_featured' => '0.00',
        'fee_video_gallery_featured_enabled' => 0,
        'fee_highlight' => '0.00',
        'fee_highlight_enabled' => 0
    ];
}
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Configure Billing Fees</h3>
            <p class="mb-0">Set your fees for signup bonus, posting, extension, Buy Now, and attribute fees. Use the checkboxes to enable or disable each fee (0 means free).</p>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Signup Bonus -->
                <div class="mb-4">
                    <h5>Signup Bonus</h5>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="signup_bonus" class="form-control" value="<?= htmlspecialchars($fees['signup_bonus']) ?>" required>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="signup_bonus_enabled" name="signup_bonus_enabled" value="1" <?= ($fees['signup_bonus_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="signup_bonus_enabled">Enable Signup Bonus</label>
                    </div>
                    <small class="form-text text-muted">
                        This amount will be added automatically to the member's account upon signup. Set to 0 or disable to remove the bonus.
                    </small>
                </div>
                
                <!-- Posting Fee -->
                <div class="mb-4">
                    <h5>Posting Fee</h5>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="apply_same_posting_fee" name="apply_same_posting_fee" value="1" <?= ($fees['apply_same_posting_fee']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="apply_same_posting_fee">Apply same fee for all categories</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="posting_fee" class="form-control" value="<?= htmlspecialchars($fees['posting_fee']) ?>" required>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="posting_fee_enabled" name="posting_fee_enabled" value="1" <?= ($fees['posting_fee_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="posting_fee_enabled">Enable Posting Fee</label>
                    </div>
                    <small class="form-text text-muted">
                        This amount will be deducted from a member's account upon posting a classified. Set to 0 or disable to make posting free.
                    </small>
                </div>
                
                <!-- Extension Fee -->
                <div class="mb-4">
                    <h5>Extension Fee (per day)</h5>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="extension_fee" class="form-control" value="<?= htmlspecialchars($fees['extension_fee']) ?>" required>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="extension_fee_enabled" name="extension_fee_enabled" value="1" <?= ($fees['extension_fee_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="extension_fee_enabled">Enable Extension Fee</label>
                    </div>
                    <small class="form-text text-muted">
                        This fee will be charged per day when a member extends a classified's duration. Set to 0 or disable to make extensions free.
                    </small>
                </div>
                
                <!-- Buy Now Fee -->
                <div class="mb-4">
                    <h5>Buy Now Fee</h5>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="buy_now_fee" class="form-control" value="<?= htmlspecialchars($fees['buy_now_fee']) ?>" required>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="buy_now_fee_enabled" name="buy_now_fee_enabled" value="1" <?= ($fees['buy_now_fee_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="buy_now_fee_enabled">Enable Buy Now Fee</label>
                    </div>
                    <small class="form-text text-muted">
                        This fee will be charged when the Buy Now option is activated. Set to 0 or disable to make it free.
                    </small>
                </div>
                
                <!-- Attribute Fees -->
                <div class="mb-4">
                    <h5>Attribute Fees</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bold ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_bold" class="form-control" value="<?= htmlspecialchars($fees['fee_bold']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_bold_enabled" name="fee_bold_enabled" value="1" <?= ($fees['fee_bold_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_bold_enabled">Enable Bold Fee</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Featured ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_featured" class="form-control" value="<?= htmlspecialchars($fees['fee_featured']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_featured_enabled" name="fee_featured_enabled" value="1" <?= ($fees['fee_featured_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_featured_enabled">Enable Featured Fee</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Front Featured ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_front_featured" class="form-control" value="<?= htmlspecialchars($fees['fee_front_featured']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_front_featured_enabled" name="fee_front_featured_enabled" value="1" <?= ($fees['fee_front_featured_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_front_featured_enabled">Enable Front Featured Fee</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image Gallery Featured ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_image_gallery_featured" class="form-control" value="<?= htmlspecialchars($fees['fee_image_gallery_featured']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_image_gallery_featured_enabled" name="fee_image_gallery_featured_enabled" value="1" <?= ($fees['fee_image_gallery_featured_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_image_gallery_featured_enabled">Enable Image Gallery Fee</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video Gallery Featured ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_video_gallery_featured" class="form-control" value="<?= htmlspecialchars($fees['fee_video_gallery_featured']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_video_gallery_featured_enabled" name="fee_video_gallery_featured_enabled" value="1" <?= ($fees['fee_video_gallery_featured_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_video_gallery_featured_enabled">Enable Video Gallery Fee</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Highlight ($):</label>
                            <input type="number" step="0.01" min="0" name="fee_highlight" class="form-control" value="<?= htmlspecialchars($fees['fee_highlight']) ?>" required>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input" id="fee_highlight_enabled" name="fee_highlight_enabled" value="1" <?= ($fees['fee_highlight_enabled']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_highlight_enabled">Enable Highlight Fee</label>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        These fees will be deducted upon activation of special listing attributes (Bold, Featured, etc.). Set a fee to 0 or disable to make that attribute free.
                    </small>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Save Billing Fees</button>
                </div>
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
