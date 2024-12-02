<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $menuId = $_POST['id'];

    if (!$menuId) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    $deleteResult = $dbFunctions->delData('menu_items', "id='$menuId'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $deleteResult['message']]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
