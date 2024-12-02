<?php
require_once('../../../global.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = $_POST['id'] ?? null;

    if (!$catId) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }

    $catIdcheck = $security->decrypt($catId);

    $deleteResult = $dbFunctions->delData('categories',"id = '$catIdcheck'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting page.']);
    }
}