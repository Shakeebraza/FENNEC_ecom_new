<?php
require_once('../../../global.php');
header('Content-Type: application/json');

// Check if user is Super Admin or Admin
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pageIdEncrypted = $_POST['id'] ?? null;
    if (!$pageIdEncrypted) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }
    // Decrypt
    $pageId = $security->decrypt($pageIdEncrypted);

    $deleteResult = $dbFunctions->delData('pages', "id = '$pageId'");
    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting page.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>