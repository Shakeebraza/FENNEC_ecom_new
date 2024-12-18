<?php
require_once 'global.php';
include_once 'header.php';

$planId = isset($_GET['plan_id']) ? $_GET['plan_id'] : null;
$price = isset($_GET['amount']) ? $_GET['amount'] : null;
$productId = isset($_GET['proid']) ? $_GET['proid'] : null;

if ($planId && $price && $productId) {

    $updateQuery = "UPDATE products SET extension = 1 WHERE id = :productId";

    try {

        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo '<h2>Product extension successful!</h2>';
            echo '<p>Your product has been extended. Plan: ' . htmlspecialchars($planId) . ', Price: $' . number_format($price, 2) . '</p>';
            echo '<p><a href="products.php">Go back to products</a></p>';
        } else {
            echo '<h2>Error updating product extension</h2>';
        }
    } catch (PDOException $e) {
        echo '<h2>Error: ' . $e->getMessage() . '</h2>';
    }
} else {
    echo '<h2>Invalid request data</h2>';
}
?>
