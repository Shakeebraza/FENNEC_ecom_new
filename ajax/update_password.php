<?php
require_once("../global.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($CsrfProtection->validateToken($_POST['token'])) {
        if ($fun->RequestSessioncheck()) {
            $userId = base64_decode($_SESSION['userid']);
            if ($userId === false) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
                exit;
            }
            $newPassword = $_POST['password'] ?? '';
            if (empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Password cannot be empty']);
                exit;
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $data = ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')];
            $updateResponse = $dbFunctions->updateData('users', $data, $userId);

            if ($updateResponse['success']) {
                echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Session validation failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
