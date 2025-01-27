<?php
require_once('../../../global.php');
header('Content-Type: application/json');

$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catIdEnc = $_POST['id'] ?? null;
    if (!$catIdEnc) {
        echo json_encode(['success' => false, 'message' => 'Invalid category ID.']);
        exit;
    }

    $catId = $security->decrypt($catIdEnc);
    $deleteResult = $dbFunctions->delData('categories',"id = '$catId'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting category.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
