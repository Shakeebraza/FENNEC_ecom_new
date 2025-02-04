<?php
require_once 'global.php';

// Get PayPal response data.
$userId = intval($_GET['user_id'] ?? 0);
$amountUSD = floatval($_GET['mc_gross'] ?? 0);

// Exchange rate (USD to JPY). Replace with a dynamic rate for production.
$usdToJpyRate = $fun->getFieldData('conversion_rate'); // e.g., 1 USD = 150 JPY

if ($userId > 0 && $amountUSD > 0) {
    // Calculate the amount in Yen.
    $amountJPY = $amountUSD * $usdToJpyRate;

    // Update wallet balance in the database.
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :id");
    $stmt->execute([':amount' => $amountJPY, ':id' => $userId]);

    // Update wallet deposit field (ensure the column name is correct; here we use wallet_deposit).
    $stmt = $pdo->prepare("UPDATE users SET wallet_deposit = wallet_deposit + :amount WHERE id = :id");
    $stmt->execute([':amount' => $amountJPY, ':id' => $userId]);

    // Record the transaction in the transactions table.
    // Create a description that indicates this transaction is from this user.
    $description = "This transaction is from user ID $userId";
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, description, transaction_date) VALUES (:user_id, :amount, :description, NOW())");
    $stmt->execute([
        ':user_id'    => $userId,
        ':amount'     => $amountJPY,
        ':description'=> $description
    ]);

    echo '<script>alert("Your balance has been updated! ' . number_format($amountJPY, 2) . ' JPY has been added to your wallet."); window.location.href = "Myaccount.php";</script>';
    exit();
} else {
    echo '<script>alert("Invalid payment details."); window.location.href = "index.php";</script>';
    exit();
}
?>
