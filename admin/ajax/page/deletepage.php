<?php
require_once('../../../global.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pageId = $_POST['id'] ?? null;

    if (!$pageId) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }

    // Decrypt if needed
    $pageId = $security->decrypt($pageId);

    $deleteResult = $dbFunctions->delData('pages',"id = '$pageId'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting page.']);
    }
}