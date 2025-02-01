<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['id'];

    if ($dbFunctions->delData('products',"id='$productId'")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Unable to delete product']);
    }
}
?>
