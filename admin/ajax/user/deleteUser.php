<?php
require_once('../../../global.php');

header('Content-Type: application/json'); // Set the header for JSON response

$id = isset($_POST['id']) ? $_POST['id'] : null;
if ($id) {
    // Your deletion logic here
    $userId = $security->decrypt($id);
    // Assuming your deletion logic is correct
    $result = $dbFunctions->delData('users', "id = " . intval($userId));

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
}
?>

