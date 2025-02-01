<?php
require_once('../../../global.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boxId = $_POST['id'] ?? null;

    if (!$boxId) {
        echo json_encode(['success' => false, 'message' => 'Invalid box ID.']);
        exit;
    }

    // Decrypt if needed
    $boxId = $security->decrypt($boxId);

    $deleteResult = $fun->deleteData($boxId);

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting box.']);
    }
}