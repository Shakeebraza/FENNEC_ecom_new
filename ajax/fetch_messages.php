<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {

    // Decode the current user's ID from the session
    $UserId = base64_decode($_SESSION['userid']);
    
    // Decode the conversation ID from POST
    $conversationId = isset($_POST['conversation_id'])
        ? base64_decode($_POST['conversation_id'])
        : null;

    if ($conversationId) {
        
        // Mark all messages in this conversation as read except the user's own messages
        $updateQuery = "
            UPDATE messages 
            SET is_read = 1 
            WHERE conversation_id = :conversationId 
              AND sender_id != :userId
        ";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $UserId, PDO::PARAM_INT);
        $stmt->execute();

        // -----------------------------------------------
        // 1) FETCH PRODUCT + CONVERSATION (without messages)
        //    So we always have product info for the ad preview
        // -----------------------------------------------
        $conversationInfoQuery = "
            SELECT
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS pslug,
                p.image AS product_image,
                p.price AS product_price,
                p.created_at AS product_created_at,
                p.extension AS extension,
                c.id AS conversation_id
            FROM
                products p
            JOIN
                conversations c ON p.id = c.proid
            WHERE
                c.id = :conversationId
            LIMIT 1
        ";

        try {
            $stmt = $pdo->prepare($conversationInfoQuery);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
            $stmt->execute();
            $productRow = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Error fetching conversation info: ' . $e->getMessage();
            exit;
        }
        
        // -----------------------------------------------
        // Display the product preview IF product exists and is not expired
        // -----------------------------------------------
        if ($productRow) {
            // Check if product is expired based on extension
            $extension_days = ($productRow['extension'] == 0) ? 30 : 60;
            
            $product_creation_date = new DateTime($productRow['product_created_at']);
            $current_date = new DateTime($currentDateTime);  // Make sure $currentDateTime is set
            $diff = $current_date->diff($product_creation_date);

            // If product not expired, show the preview
            if ($diff->days <= $extension_days) {
                echo '
                <a href="'.$urlval.'detail.php?slug='.$productRow['pslug'].'" style="text-decoration: none; color: inherit;">
                    <div class="product-header sticky-header" style="background-color: #f8f8f8; padding: 10px 15px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #ddd; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
                        <div class="product-info" style="display: flex; align-items: center; gap: 10px;">
                            <img class="product-image" src="' . $urlval . $productRow['product_image'] . '" alt="Product Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" />
                            <div class="product-details" style="flex-grow: 1;">
                                <h3 class="product-name" style="font-size: 16px; font-weight: 600; margin: 0; color: #333;">' . htmlspecialchars($productRow['product_name']) . '</h3>
                                <p class="product-price" style="font-size: 14px; color: #333; margin: 2px 0;">$' . number_format($productRow['product_price'], 2) . '</p>
                            </div>
                        </div>
                    </div>
                </a>
                ';
            }
        }

        // -----------------------------------------------
        // 2) FETCH ALL MESSAGES FOR THIS CONVERSATION
        // -----------------------------------------------
        $query = "
            SELECT 
                m.id AS message_id,
                m.message AS message_text,
                m.is_read,
                m.attachments,
                m.created_at AS message_created_at,
                m.sender_id,
                c.id AS conversation_id
            FROM 
                conversations c
            JOIN 
                messages m ON c.id = m.conversation_id
            WHERE 
                c.id = :conversationId
            ORDER BY 
                m.created_at ASC
        ";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
            $stmt->execute();
            $messagesResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Error fetching messages: ' . $e->getMessage();
            exit;
        }

        // -----------------------------------------------
        // Display messages (if any)
        // -----------------------------------------------
        if ($messagesResult) {
            echo '<ul style="list-style-type: none; padding: 0;">';
            
            foreach ($messagesResult as $message) {
                $message_text = isset($message['message_text']) ? htmlspecialchars($message['message_text']) : '';
                // Turn links into clickable anchors
                $message_text = preg_replace_callback(
                    '/\b(https?:\/\/[^\s]+)/i',
                    function ($matches) {
                        $url = $matches[1];
                        return '<a href="' . $url . '" target="_blank" style="color: #007bff; text-decoration: none;">' . htmlspecialchars($url) . '</a>';
                    },
                    $message_text
                );

                $sender = ($message['sender_id'] == $UserId) ? 'sender' : 'receiver';
                $created_at = date("h:i a", strtotime($message['message_created_at']));
                
                // Attachments
                $attachments = isset($message['attachments']) ? explode(',', $message['attachments']) : [];

                // Output styling for chat bubble
                echo '<li class="'.$sender.'" style="
                    margin-bottom: 15px; 
                    word-wrap: break-word; 
                    padding: 10px; 
                    border-radius: 10px; 
                    width: 40%; 
                    background-color: '.($sender == 'sender' ? '#e1f7d5' : '#f0f0f0').'; 
                    margin-left: '.($sender == 'sender' ? '50%' : '0%').'; 
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">';

                echo '<p style="text-align: start; padding-left: 7px; margin: 0; font-size: 14px;">' 
                        . $message_text . 
                    '</p>';

                // Display image attachments if any
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                        if (in_array(strtolower($ext), ['jpg','jpeg','png','gif','bmp'])) {
                            echo '<img src="' . $urlval . 'upload/messages/' . htmlspecialchars($attachment) . '" 
                                      alt="Image attachment" 
                                      style="max-width: 100%; height: auto; margin-top: 10px; border-radius: 8px;" />';
                        }
                    }
                }

                // Time, etc.
                echo '<span class="time" style="font-size: 12px; color: #888; display: block; text-align: right;">' 
                        . $created_at . 
                    '</span>';
                
                // Example of a "send-email" button
                echo '<button class="send-email-btn" data-message-id="' . htmlspecialchars($message['message_id']) . '" 
                              style="background: none; border: none; cursor: pointer; margin-top: 10px;">
                          <i class="fa fa-envelope" aria-hidden="true"></i>
                      </button>';

                echo '</li>';
            }

            echo '</ul>';
        } else {
            // No messages yet
            echo '<ul><li><p style="padding: 10px; font-size: 14px;">No messages found for this conversation</p></li></ul>';
        }

    } else {
        // conversationId is missing
        echo '<ul><li><p style="padding: 10px; font-size: 14px;">Invalid conversation ID</p></li></ul>';
    }

} else {
    // user not logged in
    echo '<ul><li><p style="padding: 10px; font-size: 14px;">User not authenticated</p></li></ul>';
}
?>
