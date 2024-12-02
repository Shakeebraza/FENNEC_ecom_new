<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $bannerId = $security->decrypt($_POST['id']);


    $deleteResult = $dbFunctions->delData('banners', "id='$bannerId'");
    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $deleteResult['message']]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
