<?php
require_once('../../../global.php');


$userId = $_POST['id'];
$status = $_POST['status'];

$data =[
    'is_enable'=>$status
];
$updateStatus = $dbFunctions->updateData('products', $data , $userId);
if ($updateStatus) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
