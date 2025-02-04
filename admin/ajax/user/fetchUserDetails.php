<?php
require_once("../../../global.php");

// Enable error reporting for debugging (remove or comment out in production)
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

$user = false;
$sourceTable = '';

// First, try to fetch from the users table.
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$sourceTable = 'users';

if (!$user) {
    // If not found in users, try the admins table.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $sourceTable = 'admins';
}

// Map role value to descriptive text.
$roleText = 'User'; // default value
if ($user) {
    switch ($user['role']) {
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
}

if ($user) {
    logMessage("User found in $sourceTable: ID $userId, Username: " . $user['username']);
    
    // Calculate wallet balance details.
    // Assumes your record now has two fields:
    // - wallet_deposit: the total amount deposited.
    // - wallet_balance: the current balance.
    // The spent amount is calculated as: deposit - balance.
    $walletDeposit = isset($user['wallet_deposit']) ? floatval($user['wallet_deposit']) : 0;
    $walletBalance = isset($user['wallet_balance']) ? floatval($user['wallet_balance']) : 0;
    $walletSpent   = $walletDeposit - $walletBalance;
    
    ob_start();
    ?>
    <div class="user-details">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($roleText); ?></p>
        <p><strong>Status:</strong> <?php echo ($user['status'] == 1 ? 'Activated' : 'Blocked'); ?></p>
        <hr />
        <p><strong>Wallet Balance:</strong> <?php echo number_format($walletBalance, 2); ?></p>
        <p><strong>Deposited:</strong> <?php echo number_format($walletDeposit, 2); ?></p>
        <p><strong>Spent:</strong> <?php echo number_format($walletSpent, 2); ?></p>
        <!-- Add more fields as necessary -->
    </div>
    <?php
    $html = ob_get_clean();
    echo json_encode(['success' => true, 'html' => $html]);
    logMessage("Successfully returned details for user ID $userId");
} else {
    logMessage("User not found for user ID: $userId in both tables");
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
