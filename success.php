<?php
require_once 'global.php';

$userId = $_SESSION['user_id'];
$amount = $_GET['amount'] ?? 0;
$txnId = $_GET['tx'] ?? '';
$planId = $_GET['plan_id'] ?? null;
$productId = $_GET['proid'] ?? null;


if (!empty($txnId) && !empty($productId)) {
    $payment_status = $_GET['st'] ?? '';
    
    if ($payment_status === 'Completed') {
        $updateStatus = $fun->updateBoostPlanStatus($planId, $txnId, $userId, $amount, $productId);
        if ($updateStatus) {
            echo "<h2>Payment Successful!</h2>";
            echo "<p>Your boost plan has been activated successfully.</p>";
        } else {
            echo "<h2>Payment Failed!</h2>";
            echo "<p>There was an issue updating your plan. Please contact support.</p>";
        }
    } else {
        echo "<h2>Payment Error</h2>";
        echo "<p>Payment was not completed or is invalid.</p>";
    }
} else {
    echo "<h2>Invalid Request</h2>";
    echo "<p>Missing transaction or product information.</p>";
}
?>
