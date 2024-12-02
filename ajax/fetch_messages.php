<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {

    $conversationId = base64_decode($_POST['conversation_id']) ?? NULL;
    $UserId = base64_decode($_SESSION['userid']);

    if (isset($conversationId)) {

        $updateQuery = "
            UPDATE messages 
            SET is_read = 1 
            WHERE conversation_id = :conversationId 
            AND sender_id != :userId
        ";

        try {
            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $UserId, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        $getMesDataQuery = "
            SELECT * FROM messages 
            WHERE conversation_id = :conversationId
            ORDER BY created_at ASC
        ";

        try {
            $stmt = $pdo->prepare($getMesDataQuery);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_STR);
            $stmt->execute();

            $getMesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($getMesData) {
                echo '<ul style="list-style-type: none; padding: 0;">';

                foreach ($getMesData as $message) {
                    $sender_id = $message['sender_id'];
                    $message_text = htmlspecialchars($message['message']);
                    $message_text = preg_replace_callback(
                        '/\b(https?:\/\/[^\s]+)/i',
                        function ($matches) {
                            $url = $matches[1];
                            return '<a href="' . $url . '" target="_blank" style="color: #007bff; text-decoration: none;">' . htmlspecialchars($url) . '</a>';
                        },
                        $message_text
                    );
                    $created_at = date("h:i a", strtotime($message['created_at']));
                    $attachments = $message['attachments']; 

           
                    $messageClass = $sender_id == base64_decode($_SESSION['userid']) ? 'sender' : 'receiver';

                   
                    $messageStyle = $sender_id == base64_decode($_SESSION['userid']) 
                        ? 'text-align: right; background-color: #00494f75; margin-left: auto;' 
                        : 'text-align: left; background-color: #f1f1f1; margin-right: auto;';

                    echo '<li class="' . $messageClass . '" style="margin-bottom: 15px; word-wrap: break-word; padding: 10px; border-radius: 10px; width: 40%; ' . $messageStyle . '">';

                 
                    if ($attachments) {
                        $attachmentFiles = explode(',', $attachments); 
                        foreach ($attachmentFiles as $file) {
                            $filePath = $urlval . 'upload/messages/' . $file;

                            if (in_array(pathinfo($filePath, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                                echo '
                                <div style="display: flex; flex-direction: column; align-items: flex-start; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff; max-width: 250px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                    <img src="' . $filePath . '" alt="Attachment" onclick="openImagePopup(this.src)" style="max-width: 100%; max-height: 150px; border-radius: 5px; margin-bottom: 10px; cursor: pointer;">
                                    <span style="font-size: 14px; color: #333;">' . $message_text . '</span>
                                </div>';
                            } else {
                                echo '<div style="'.$messageStyle.'"><a href="' . $filePath . '" target="_blank" style="color: #007bff; text-decoration: none;">Download Attachment</a></div>';
                            }
                        }
                    }else{
                        
                        echo '<p style="text-align: start; padding-left: 7px;">' . $message_text . '</p>';
                    }

                   

               
                    echo '<span class="time" style="font-size: 12px; color: #888; display: block; text-align: right;">' . $created_at . '</span>';

                    echo '</li>';
                }

                echo '</ul>';
            } else {
                echo '<ul><li><p>No messages found</p></li></ul>';
            }
        } catch (PDOException $e) {
            echo 'Error fetching messages: ' . $e->getMessage();
        }
    } else {
        echo '<ul><li><p>Invalid conversation ID</p></li></ul>';
    }
} else {
    echo '<ul><li><p>User not authenticated</p></li></ul>';
}
?>
