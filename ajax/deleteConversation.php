<?php
require_once("../global.php");

if (isset($_POST['conversation_id'])) {
    $conversation_id = base64_decode($_POST['conversation_id']);
    $user_id = base64_decode($_SESSION['userid']) ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID not found in session.']);
        exit;
    }

    // Fetch the conversation details
    $sql = "SELECT * FROM conversations WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$conversation_id]);

    if ($stmt->rowCount() > 0) {
        $conversation = $stmt->fetch();

        // Update the user_one or user_two fields to NULL for the current user
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

        // Check if both users have deleted the chat
        $checkSql = "SELECT user_one, user_two FROM conversations WHERE id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$conversation_id]);
        $updatedConversation = $checkStmt->fetch();

        if (is_null($updatedConversation['user_one']) && is_null($updatedConversation['user_two'])) {
            // Both users have deleted the chat, so update fields instead of deleting
            $clearChatSql = "
                UPDATE conversations 
                SET user_one = 0, user_two = 0 
                WHERE id = ?";
            $clearChatStmt = $pdo->prepare($clearChatSql);
            $clearChatStmt->execute([$conversation_id]);

            // Optionally mark messages as archived or update their status
            $archiveMessagesSql = "
                UPDATE messages 
                SET is_read = 1 
                WHERE conversation_id = ?";
            $archiveMessagesStmt = $pdo->prepare($archiveMessagesSql);
            $archiveMessagesStmt->execute([$conversation_id]);

            echo json_encode(['success' => true, 'message' => 'Conversation archived as both users have left.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'User removed from the conversation successfully.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Conversation not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Conversation ID not provided.']);
}
?>