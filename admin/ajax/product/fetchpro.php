<?php
require_once('../../../global.php');


$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;

$filters = [];
if (isset($_POST['name'])) {
    $filters['name'] = $_POST['name'];
}
if (isset($_POST['min_price'])) {
    $filters['min_price'] = (float)$_POST['min_price'];
}
if (isset($_POST['max_price'])) {
    $filters['max_price'] = (float)$_POST['max_price'];
}
if (isset($_POST['category'])) {
    $filters['category'] = $_POST['category'];
}
if (isset($_POST['subcategory'])) {
    $filters['subcategory'] = $_POST['subcategory'];
}
if (isset($_POST['product_type'])) {
    $filters['product_type'] = $_POST['product_type'];
}
if (isset($_POST['country'])) {
    $filters['country'] = $_POST['country'];
}
if (isset($_POST['city'])) {
    $filters['city'] = $_POST['city'];
}


$response = $productFun->getProductsWithDetailsAdminFixed2($page, $limit, $filters);

echo json_encode($response);
