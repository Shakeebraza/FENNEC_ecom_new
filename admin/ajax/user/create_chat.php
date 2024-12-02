<?php
require_once('../../../global.php');

$chatId = $security->decrypt($_POST['chatId']);
$currentUser = base64_decode($_SESSION['userid']);

if (!empty($chatId) && !empty($currentUser)) {
    $existingChat = $dbFunctions->getDatanotenc('conversations', "user_one = '$currentUser' AND user_two = '$chatId' OR user_one = '$chatId' AND user_two = '$currentUser'");

    if (!$existingChat) {
        $chatData = [
            'user_one' => $currentUser,
            'user_two' => $chatId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $insertChat = $dbFunctions->setData('conversations', $chatData);

        if ($insertChat) {
            echo json_encode(['success' => true, 'message' => 'Chat created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create chat.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Chat already exists.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
}
?>
