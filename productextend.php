<?php
require_once 'global.php';
include_once 'header.php';

// ----------------------------------
// Updated inline styling with unique class names
// ----------------------------------
echo '<style>
    .ext-container {
        max-width: 600px;
        margin: 30px auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }
    .ext-container h2 {
        margin-top: 0;
    }
    .ext-info, .ext-error {
        background: #fff;
        margin: 15px 0;
        padding: 15px;
        border-radius: 5px;
    }
    .ext-info {
        border-left: 4px solid #28a745;
    }
    .ext-error {
        border-left: 4px solid #dc3545;
    }
    .ext-btn {
        display: inline-block;
        padding: 8px 16px;
        cursor: pointer;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        background: #007bff;
        color: #fff;
        text-decoration: none;
    }
    .ext-btn:hover {
        background: #0056b3;
    }
    .ext-highlight {
        font-weight: bold;
    }
    a {
        color: #007bff;
    }
    a:hover {
        text-decoration: none;
    }
</style>';

// Ensure the user is logged in (assuming you set $_SESSION['userid'] elsewhere)
$userId = isset($_SESSION['userid']) ? intval(base64_decode($_SESSION['userid'])) : 0;
if (!$userId) {
    echo '<div class="ext-container">
            <div class="ext-error">
                <h2>Access Denied</h2>
                <p>You must be logged in to perform this action.</p>
            </div>
          </div>';
    exit;
}

// Retrieve and sanitize input parameters
$productId = isset($_GET['productid']) 
    ? base64_decode($_GET['productid']) 
    : (isset($_POST['productid']) ? base64_decode($_POST['productid']) : null);

$price = $fun->getSiteSettingValue('product_extend');
$planName = isset($_GET['plan_name']) 
    ? $_GET['plan_name'] 
    : (isset($_POST['plan_name']) ? $_POST['plan_name'] : null);

echo '<div class="ext-container">';

if ($productId && $price && $planName) {

    // Fetch the user\'s current wallet balance
    $balance = $fun->getUserBalance($userId);

    // If user just submitted the form (POST), process the payment
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($balance >= $price) {
            // Sufficient balance, proceed with deduction
            $newBalance = $balance - $price;

            try {
                // Begin transaction to ensure data integrity
                $pdo->beginTransaction();

                // Update wallet balance
                $updateBalanceQuery = "
                    UPDATE users 
                    SET wallet_balance = :newBalance 
                    WHERE id = :userId
                ";
                $stmtBalance = $pdo->prepare($updateBalanceQuery);
                $stmtBalance->bindParam(':newBalance', $newBalance, PDO::PARAM_STR);
                $stmtBalance->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmtBalance->execute();

                // Update product extension and set created_at to 1 month from current created_at
                $updateProductQuery = "
                    UPDATE products 
                    SET extension = 1, 
                        created_at = DATE_ADD(created_at, INTERVAL 1 MONTH)
                    WHERE id = :productId
                ";
                $stmtProduct = $pdo->prepare($updateProductQuery);
                $stmtProduct->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmtProduct->execute();

                // Optional: Log the transaction (uncomment if needed)
                /*
                $logTransactionQuery = "
                    INSERT INTO transactions (user_id, product_id, amount, type, created_at) 
                    VALUES (:userId, :productId, :amount, 'debit', NOW())
                ";
                $stmtTransaction = $pdo->prepare($logTransactionQuery);
                $stmtTransaction->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmtTransaction->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmtTransaction->bindParam(':amount', $price, PDO::PARAM_STR);
                $stmtTransaction->execute();
                */

                // Commit transaction
                $pdo->commit();

                // Show success message
                echo '<div class="ext-info">
                        <h2>Product Extension Successful!</h2>
                        <p>Your product has been extended by one month.</p>
                        <p><span class="ext-highlight">Plan:</span> ' . htmlspecialchars($planName) . '</p>
                        <p><span class="ext-highlight">Amount Deducted:</span> $' . number_format($price, 2) . '</p>
                        <p><span class="ext-highlight">Remaining Balance:</span> $' . number_format($newBalance, 2) . '</p>
                        <p><a class="ext-btn" href="Myaccount.php#view-products">Go Back to Products</a></p>
                      </div>';

            } catch (PDOException $e) {
                // Roll back if something fails
                $pdo->rollBack();
                echo '<div class="ext-error">
                        <h2>Error Processing Your Request</h2>
                        <p>' . htmlspecialchars($e->getMessage()) . '</p>
                      </div>';
            }
        } else {
            // Insufficient balance
            echo '<div class="ext-error">
                    <h2>Insufficient Wallet Balance</h2>
                    <p>Your current balance is $' . number_format($balance, 2) . '.</p>
                    <p>Please <a href="wallet.php">top up your wallet</a> to proceed.</p>
                  </div>';
        }

    } else {
        // Show confirmation form if not submitted yet
        if ($balance >= $price) {
            echo '<div class="ext-info">
                    <h2>Confirm Purchase</h2>
                    <p><span class="ext-highlight">Plan:</span> ' . htmlspecialchars($planName) . '</p>
                    <p><span class="ext-highlight">Price:</span> $' . number_format($price, 2) . '</p>
                    <p><span class="ext-highlight">Your Current Balance:</span> $' . number_format($balance, 2) . '</p>
                    <form method="post" action="">
                        <input type="hidden" name="productid" value="' . htmlspecialchars(base64_encode($productId)) . '">
                        <input type="hidden" name="plan_name" value="' . htmlspecialchars($planName) . '">
                        <button type="submit" class="ext-btn">Confirm and Pay</button>
                    </form>
                  </div>';
        } else {
            echo '<div class="ext-error">
                    <h2>Insufficient Wallet Balance</h2>
                    <p>Your current balance is $' . number_format($balance, 2) . '.</p>
                    <p>Please <a href="wallet.php">top up your wallet</a> to proceed.</p>
                  </div>';
        }
    }

} else {
    // Invalid or missing parameters
    echo '<div class="ext-error">
            <h2>Invalid Product Data</h2>
            <p>Please ensure you have selected a valid product.</p>
          </div>';
}

echo '</div>'; // close .ext-container
?>
