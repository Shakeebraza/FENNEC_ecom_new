<?php
// require_once 'global.php';

// $userId = intval($_GET['user_id'] ?? 0);
// $amount = floatval($_GET['mc_gross'] ?? 0);

// if ($userId > 0 && $amount > 0) {
//     $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :id");
//     $stmt->execute([':amount' => $amount, ':id' => $userId]);

//     echo '<script>alert("Your balance has been updated!"); window.location.href = "Myaccount.php";</script>';
//     exit();
// } else {
//     echo '<script>alert("Invalid payment details."); window.location.href = "index.php";</script>';
//     exit();
// }

require_once 'global.php';

// Get PayPal response data
$userId = intval($_GET['user_id'] ?? 0);
$amountUSD = floatval($_GET['mc_gross'] ?? 0);

// Exchange rate (USD to JPY). Replace with a dynamic rate for production.
$usdToJpyRate = $fun->getFieldData('conversion_rate');// Example rate: 1 USD = 150 JPY

if ($userId > 0 && $amountUSD > 0) {
    // Calculate the amount in Yen
    $amountJPY = $amountUSD * $usdToJpyRate;

    // Update wallet balance in the database
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :id");
    $stmt->execute([':amount' => $amountJPY, ':id' => $userId]);

    echo '<script>alert("Your balance has been updated! ' . number_format($amountJPY, 2) . ' JPY has been added to your wallet."); window.location.href = "Myaccount.php";</script>';
    exit();
} else {
    echo '<script>alert("Invalid payment details."); window.location.href = "index.php";</script>';
    exit();
}
?>
