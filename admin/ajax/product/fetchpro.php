<?php
require_once('../../../global.php');




// header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;

$filters = [];
if (isset($_GET['product_name'])) {
    $filters['product_name'] = $_GET['product_name'];
}
if (isset($_GET['min_price'])) {
    $filters['min_price'] = (float)$_GET['min_price'];
}
if (isset($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
}
if (isset($_GET['category'])) {
    $filters['category'] = $_GET['category'];
}
if (isset($_GET['subcategory'])) {
    $filters['subcategory'] = $_GET['subcategory'];
}
if (isset($_GET['product_type'])) {
    $filters['product_type'] = $_GET['product_type'];
}
if (isset($_GET['country'])) {
    $filters['country'] = $_GET['country'];
}
if (isset($_GET['city'])) {
    $filters['city'] = $_GET['city'];
}


$response = $productFun->getProductsWithDetailsAdmin($page, $limit, $filters);

echo json_encode($response);
