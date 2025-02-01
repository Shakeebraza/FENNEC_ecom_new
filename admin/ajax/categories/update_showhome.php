<?php
require_once('../../../global.php');
header('Content-Type: application/json');

// Only Admin or Super Admin can update
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$id     = $security->decrypt($_POST['id'] ?? '');
$is_show= intval($_POST['is_show'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    exit;
}

$data = [ 'is_show' => $is_show ];
$updateData = $dbFunctions->updateData('categories', $data, $id);

if ($updateData['success']) {
    echo json_encode(['success' => true, 'message' => 'Data updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No changes made or error.']);
}
?>
