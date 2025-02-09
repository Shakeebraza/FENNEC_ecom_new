<?php
require_once("./global.php");

// Check that the required query parameters exist
if (!isset($_GET['token']) || !isset($_GET['user_id'])) {
    echo "Invalid approval link.";
    exit();
}

$approvalToken = $_GET['token'];
$userId        = $_GET['user_id'];

// Retrieve the user record with the matching id and token
$user = $dbFunctions->getData('users', "id = '$userId' AND approval_token = '$approvalToken'");
if (!$user || empty($user)) {
    
    echo "Invalid approval link or user has already been approved.";
    exit();
}

// Prepare data to mark the user as approved
$updateData = [
    'admin_verified' => 1,
    'approval_token' => 0  // Clear the token after approval
];

// Update the user record
$response = $dbFunctions->updateData('users', $updateData, $userId);
if (!$response['success']) {

    echo "Error approving user.";
    exit();
}


echo "User approved successfully.";
?>
