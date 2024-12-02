<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_id'])) {
    $area_id = $security->decrypt($_POST['area_id']);

    if (!$area_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    $deleteResult = $dbFunctions->delData('areas', "id='$area_id'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $deleteResult['message']]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
