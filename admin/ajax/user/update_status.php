<?php
require_once('../../../global.php');


$userId = $security->decrypt($_POST['id']);
$status = $_POST['status'];
$data =[
    'status'=>$status
];
$updateStatus = $dbFunctions->updateData('users', $data , $userId);
if ($updateStatus) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
