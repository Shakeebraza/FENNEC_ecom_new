<?php
require_once 'global.php';

$data = json_decode(file_get_contents('php://input'), true);
$boostType = $data['boostType'];
$planId = intval($data['planId']);
$price = floatval($data['price']);
$productId = intval($data['productId']);
$userId = intval(base64_decode($_SESSION['userid']));

// Fetch wallet balance
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$userBalance = $stmt->fetchColumn();

if ($userBalance === false || $userBalance < $price) {
    echo json_encode(['success' => false, 'message' => 'Insufficient wallet balance.']);
    exit();
}

try {
    // Deduct wallet balance
    $newBalance = $userBalance - $price;
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = :balance WHERE id = :id");
    $stmt->execute([':balance' => $newBalance, ':id' => $userId]);

    // Generate transaction ID
    $txnId = 'WALLET_PAYMENT_' . uniqid();

    // Update boost plan
    $updateStatus = $fun->updateBoostPlanStatus($planId, $txnId, $userId, $price, $productId);

    if ($updateStatus) {
        echo json_encode(['success' => true, 'message' => 'Boost plan activated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to activate boost plan.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
