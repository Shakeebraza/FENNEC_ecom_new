<?php
require_once("../global.php");

if (isset($_POST['conversation_id'])) {
    $conversation_id = base64_decode($_POST['conversation_id']);
    $user_id = base64_decode($_SESSION['userid']) ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID not found in session.']);
        exit;
    }


    $sql = "SELECT * FROM conversations WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$conversation_id]);

    if ($stmt->rowCount() > 0) {
        $conversation = $stmt->fetch();

   
        if ($conversation['user_one'] == 0 || $conversation['user_two'] == 0) {

       
            $deleteMessagesSql = "DELETE FROM messages WHERE conversation_id = ?";
            $deleteMessagesStmt = $pdo->prepare($deleteMessagesSql);
            $deleteMessagesStmt->execute([$conversation_id]);

      
            if ($deleteMessagesStmt->rowCount() > 0) {
                $deleteConversationSql = "DELETE FROM conversations WHERE id = ?";
                $deleteConversationStmt = $pdo->prepare($deleteConversationSql);
                $deleteConversationStmt->execute([$conversation_id]);

                echo json_encode(['success' => true, 'message' => 'Messages and conversation deleted successfully.']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete messages.']);
                exit;
            }
        } else {
       
            if ($conversation['user_one'] == $user_id) {
                $updateSql = "UPDATE conversations SET user_one = NULL WHERE id = ?";
            } elseif ($conversation['user_two'] == $user_id) {
                $updateSql = "UPDATE conversations SET user_two = NULL WHERE id = ?";
            } else {
                echo json_encode(['success' => false, 'message' => 'You are not part of this conversation.']);
                exit;
            }

   
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$conversation_id]);

       
            $messageSql = "UPDATE messages SET is_read = 1 WHERE conversation_id = ?";
            $messageStmt = $pdo->prepare($messageSql);
            $messageStmt->execute([$conversation_id]);

            echo json_encode(['success' => true, 'message' => 'Conversation updated successfully.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Conversation not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Conversation ID not provided.']);
}
?>
