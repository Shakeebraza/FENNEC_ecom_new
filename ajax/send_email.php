<?php
require_once "../global.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $messageId = $input['message_id'] ?? null;

    if ($messageId) {
        try {
            // Retrieve the message and attachment from the database
            $query = "SELECT message, sender_id, attachments FROM messages WHERE id = :messageId";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':messageId', $messageId, PDO::PARAM_INT);
            $stmt->execute();
            $messageData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($messageData) {
                $to = $_SESSION['email'] ?? null;

                if (!$to) {
                    echo json_encode(['success' => false, 'error' => 'Recipient email not found in session']);
                    exit;
                }

                $subject = 'Message from Conversation';
                $mesg = htmlspecialchars_decode($messageData['message']);
                $message=empty($mesg) ?? "Attachment";
                // Process a single attachment if it exists
                $attachment = $messageData['attachments'];
                $attachmentPath = null;

                if ($attachment) {
         
                    $attachmentPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . $attachment;
                
                }
                
                // Send the email using smtp_mailer
                $sendmail = smtp_mailer($to, $subject, $message, $attachmentPath);
              
                if ($sendmail == 'Sent') {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to send email']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Message not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid message ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}