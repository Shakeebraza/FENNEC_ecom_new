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

            $deletedConversation = $dbFunctions->getDatanotenc(
                'conversations',
                "proid = '$productId' AND (user_one = 0 OR user_two = 0)"
            );

            if ($deletedConversation) {
                // Prepare and execute the update query
                $updateQuery = "
                    UPDATE conversations 
                    SET user_one = CASE WHEN user_one = 0 THEN :userId ELSE user_one END,
                        user_two = CASE WHEN user_two = 0 THEN :userId ELSE user_two END
                    WHERE id = :conversationId";
                
                $stmt = $pdo->prepare($updateQuery);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':conversationId', $deletedConversation[0]['id']);
                
                $result = $stmt->execute();

                if ($result) {
                    // Prepare and execute the update query for messages
                    $updateMessagesQuery = "
                        UPDATE messages 
                        SET sender_id = :userId 
                        WHERE sender_id = 0 AND conversation_id = :conversationId";
                    
                    $messageStmt = $pdo->prepare($updateMessagesQuery);
                    $messageStmt->bindParam(':userId', $userId);
                    $messageStmt->bindParam(':conversationId', $deletedConversation[0]['id']);
                    
                    $messageResult = $messageStmt->execute();

                    if ($messageResult) {
                        $response = [
                            'success' => true,
                            'message' => 'Reopened deleted chat with new user ID.',
                            'conversationId' => base64_encode($deletedConversation[0]['id'])
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Failed to update messages in the conversation.'
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Failed to update the deleted conversation.'
                    ];
                }
            } else {
                // Start a new chat if no deleted conversation exists
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
