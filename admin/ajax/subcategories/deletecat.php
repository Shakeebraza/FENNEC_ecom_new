<?php
require_once('../../../global.php');
header('Content-Type: application/json');

// 1) Check user role
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = $_POST['id'] ?? null;

    if (!$catId) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }

    $catIdcheck = $security->decrypt($catId);

    $deleteResult = $dbFunctions->delData('subcategories', "id = '$catIdcheck'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting page.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
