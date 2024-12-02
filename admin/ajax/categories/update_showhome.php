<?php
require_once('../../../global.php');

$id = $security->decrypt($_POST['id']);
$is_show = intval($_POST['is_show']);
$data = [
    'is_show' => $is_show
];

$updateQuery = "UPDATE categories SET is_show = :is_show WHERE id = :id";
$updateData = $dbFunctions->updateData('categories', $data, $id);

// header('Content-Type: application/json'); 

if ($updateData['success']) {
    echo json_encode(['success' => true, 'message' => 'Data updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No changes made; the data was the same.']);
}
?>

