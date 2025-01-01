<?php
require_once("../global.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = $data['userId'] ?? null;
    $lan = $data['lan'] ?? [];
    $filter = $data['filter'] ?? 'active';

    if (!$userId) {
        echo '<p>No user ID provided.</p>';
        exit;
    }

    $products = $productFun->getProductsForUserexp($userId, $lan,$filter);
    $productsren = $productFun->displayProducts($products, $lan);

} else {
    echo '<p>Invalid request method.</p>';
}
