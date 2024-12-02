<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {
    $conversationId = base64_decode($_POST['conversation_id']) ?? NULL;
    $message = $_POST['message'] ?? '';


    $attachments = [];
  
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
   
        $uploadDir = '../upload/messages/';
        foreach ($_FILES['attachments']['tmp_name'] as $index => $tmpName) {
            $fileName = basename($_FILES['attachments']['name'][$index]);
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($tmpName, $filePath)) {
                $attachments[] = $fileName; 
            }
        }
    }


    $attachmentsString = !empty($attachments) ? implode(',', $attachments) : null;

    if ($conversationId && !empty($message)) {
        $checkQuery = "SELECT id FROM conversations WHERE id = :conversation_id";
        $stmtCheck = $pdo->prepare($checkQuery);
        $stmtCheck->bindParam(':conversation_id', $conversationId);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            $senderId = base64_decode($_SESSION['userid']);
            $createdAt = date("Y-m-d H:i:s");

           
            $insertQuery = "INSERT INTO messages (conversation_id, sender_id, message, is_read, created_at, attachments) 
                            VALUES (:conversation_id, :sender_id, :message, :is_read, :created_at, :attachments)";
            
            $stmt = $pdo->prepare($insertQuery);
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->bindParam(':sender_id', $senderId);
            $stmt->bindParam(':message', $message);
            $stmt->bindValue(':is_read', 0); 
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':attachments', $attachmentsString); 

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid conversation ID']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid conversation ID or empty message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
}
?>
