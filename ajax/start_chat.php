<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {
    $productId = $security->decrypt($_POST['product_id']) ?? null;
    $userId = base64_decode($_SESSION['userid']) ?? null;
    $productUserid = $productFun->GetUserId($productId);

    if (isset($productId) && isset($userId) && isset($productUserid)) {
        $chatData = $dbFunctions->getDatanotenc(
            'conversations',
            "proid = '$productId' AND ((user_one = $userId AND user_two = $productUserid) OR (user_one = $productUserid AND user_two = $userId))"
        );

        if ($chatData) {
            $response = [
                'success' => true,
                'message' => 'Existing chat found.',
                'conversationId' => base64_encode($chatData[0]['id'])  
            ];
        } else {
            $insertData = [
                'user_one' => $userId,
                'user_two' => $productUserid,
                'proid' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $insertResult = $dbFunctions->setData2('conversations', $insertData);

            if ($insertResult['success']) {
                $conversationId = $insertResult['last_insert_id'];
                $messageData = [
                    'conversation_id' => $conversationId,
                    'sender_id' => $userId,
                    'message' => 'Hello!',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $messageResult = $dbFunctions->setData2('messages', $messageData);

                if ($messageResult['success']) {
                    $response = [
                        'success' => true,
                        'message' => 'New chat started successfully with default message.',
                        'conversationId' => base64_encode($conversationId)
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Failed to send default message.'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Failed to start new chat.'
                ];
            }
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Required IDs are missing.'
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'User session not found.']);
}
?>
