<?php
require_once("../../../global.php");

// Enable error reporting for debugging (remove in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Define log file path.
$logFile = __DIR__ . '/user_details.log';

// Helper function to log messages.
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Get the encrypted user id from POST.
$encryptedId = $_POST['id'] ?? '';

// Log the request.
logMessage("Request received for encryptedId: $encryptedId");

// Decrypt the ID.
$userId = $security->decrypt($encryptedId);
if (!$userId) {
    logMessage("Invalid user identifier. Decryption failed for encryptedId: $encryptedId");
    echo json_encode(['success' => false, 'message' => 'Invalid user identifier.']);
    exit;
}

$accountData = false;
$sourceTable = '';

// First, try to fetch from the users table.
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$accountData = $stmt->fetch(PDO::FETCH_ASSOC);
if ($accountData) {
    $sourceTable = 'users';
} else {
    // If not found in users, try the admins table.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $accountData = $stmt->fetch(PDO::FETCH_ASSOC);
    $sourceTable = 'admins';
}

if (!$accountData) {
    logMessage("Account not found for user ID: $userId in both tables");
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Map role value to descriptive text.
$roleValue = isset($accountData['role']) ? intval($accountData['role']) : 0;
$roleText = 'User';
switch ($roleValue) {
    case 1:
        $roleText = 'Super Admin';
        break;
    case 2:
        $roleText = 'Trader';
        break;
    case 3:
        $roleText = 'Admin';
        break;
    case 4:
        $roleText = 'Moderator';
        break;
    default:
        $roleText = 'User';
        break;
}

logMessage("Account found in $sourceTable: ID $userId, Username: " . $accountData['username']);

// Determine if wallet info and extra statistics should be shown.
// These should be displayed only if the account is from the "users" table and the role is 0 or 2.
$showExtra = ($sourceTable === 'users' && in_array($roleValue, [0, 2]));

// Fetch extra details from the appropriate detail table.
$detailData = [];
if ($sourceTable === 'users') {
    $detailDataArr = $dbFunctions->getDatanotenc('user_detail', "userid = '$userId'");
} else {
    $detailDataArr = $dbFunctions->getDatanotenc('admin_detail', "userid = '$userId'");
}
if ($detailDataArr && is_array($detailDataArr)) {
    $detailData = $detailDataArr[0];
}

// Calculate wallet balance details (only for users meeting criteria).
$walletDeposit = $walletBalance = $walletSpent = 0;
if ($showExtra) {
    $walletDeposit = isset($accountData['wallet_deposit']) ? floatval($accountData['wallet_deposit']) : 0;
    $walletBalance = isset($accountData['wallet_balance']) ? floatval($accountData['wallet_balance']) : 0;
    $walletSpent   = $walletDeposit - $walletBalance;
}

// Initialize extra counts (only for users meeting criteria).
$transactionCount = $reviewsCount = $reportsCount = $classifiedCount = $favoritesCount = 0;
if ($showExtra) {
    $transactionCount = $dbFunctions->getCount('transactions', '*', "user_id = " . intval($userId));
    $reviewsCount     = $dbFunctions->getCount('reviews', '*', "user_id = " . intval($userId));
    $reportsCount     = $dbFunctions->getCount('reports', '*', "user_id = " . intval($userId));
    $classifiedCount  = $dbFunctions->getCount('products', '*', "user_id = " . intval($userId));
    $favoritesCount   = $dbFunctions->getCount('favorites', '*', "user_id = " . intval($userId));
}

ob_start();
?>
<!-- Enhanced UI using Bootstrap card layout -->
<div class="card shadow my-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Account Information</h4>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4 text-center">
                <?php if (!empty($accountData['profile'])): ?>
                    <img src="<?php echo htmlspecialchars($urlval . $accountData['profile']); ?>" alt="Profile Image" class="img-fluid rounded-circle" style="max-width: 150px;">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($accountData['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($accountData['email']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($roleText); ?></p>
                <p><strong>Status:</strong> <?php echo (isset($accountData['status']) && $accountData['status'] == 1) ? 'Activated' : 'Blocked'; ?></p>
                <p><strong>Account Type:</strong> <?php echo ucfirst($sourceTable); ?></p>
            </div>
        </div>
        <?php if ($showExtra): ?>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <h6>Wallet Info</h6>
                <p><strong>Wallet Balance:</strong> <?php echo number_format($walletBalance, 2); ?></p>
                <p><strong>Deposited:</strong> <?php echo number_format($walletDeposit, 2); ?></p>
                <p><strong>Spent:</strong> <?php echo number_format($walletSpent, 2); ?></p>
            </div>
            <div class="col-md-8">
                <h6>Extra Details</h6>
                <?php if (!empty($detailData)): ?>
                    <?php if (isset($detailData['number'])): ?>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($detailData['number']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($detailData['address'])): ?>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($detailData['address']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($detailData['country'])): ?>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($detailData['country']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($detailData['city'])): ?>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($detailData['city']); ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No additional detail information available.</p>
                <?php endif; ?>
            </div>
        </div>
        <hr>
        <!-- Enhanced statistics layout -->
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 col-6">
                    <div class="stat-box p-2">
                        <h6 class="mb-1">Total Transactions</h6>
                        <p class="mb-0"><?php echo intval($transactionCount); ?></p>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="stat-box p-2">
                        <h6 class="mb-1">Total Reviews</h6>
                        <p class="mb-0"><?php echo intval($reviewsCount); ?></p>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="stat-box p-2">
                        <h6 class="mb-1">Total Reports</h6>
                        <p class="mb-0"><?php echo intval($reportsCount); ?></p>
                    </div>
                </div>
            </div>
            <div class="row text-center mt-3">
                <div class="col-md-6 col-6">
                    <div class="stat-box p-2">
                        <h6 class="mb-1">Total Classifieds</h6>
                        <p class="mb-0"><?php echo intval($classifiedCount); ?></p>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="stat-box p-2">
                        <h6 class="mb-1">Total Favorites</h6>
                        <p class="mb-0"><?php echo intval($favoritesCount); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$html = ob_get_clean();
echo json_encode(['success' => true, 'html' => $html]);
logMessage("Successfully returned details for user ID $userId");
