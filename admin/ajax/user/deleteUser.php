<?php
require_once('../../../global.php');

header('Content-Type: application/json'); // JSON response

$id = $_POST['id'] ?? null;
if ($id) {
    // Decrypt user ID
    $userId = $security->decrypt($id);

    // (Optional) You can check if the user is Admin or Super Admin here:
    // $role = $_SESSION['role'] ?? 0;
    // if (!in_array($role, [1,3])) {
    //   echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    //   exit;
    // }

    // Proceed with deletion
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
