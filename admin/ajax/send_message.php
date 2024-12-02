<?php
require_once("../../global.php");
if (isset($_POST['message']) && isset($_POST['conversation_id'])) {
    $message = $_POST['message'];
    $conversationId = $_POST['conversation_id'];
    $userId = base64_decode($_SESSION['userid']);

    $sqlInsert = "INSERT INTO messages (conversation_id, sender_id, message, created_at) VALUES (:conversation_id, :user_id, :message, NOW())";
    $stmtInsert = $pdo->prepare($sqlInsert);
    
    try {
        $stmtInsert->execute([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'message' => $message
        ]);
    
        echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
    } catch (Exception $e) {
    
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
