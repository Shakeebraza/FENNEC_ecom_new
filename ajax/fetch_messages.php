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

            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $UserId, PDO::PARAM_STR);
            $stmt->execute();

        $query = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS pslug,
                p.image AS product_image,
                p.price AS product_price,
                p.created_at AS product_created_at,
                p.extension AS extension,
                m.id AS message_id,
                m.message AS message_text,
                m.is_read,
                m.attachments,
                m.created_at AS message_created_at,
                m.sender_id,  -- Add sender_id to the query
                c.id AS conversation_id,
                c.user_one,
                c.user_two
            FROM 
                products p
            JOIN 
                conversations c ON p.id = c.proid
            JOIN 
                messages m ON c.id = m.conversation_id
            WHERE 
                c.id = :conversationId
            ORDER BY 
                m.created_at ASC;
        ";

        try {

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
         
                $product = $result[0];
                $extension_days = $product['extension'] == 0 ? 30 : 60;  
                $product_creation_date = new DateTime($product['product_created_at']);  
                $current_date = new DateTime($currentDateTime); 
                
               
                $diff = $current_date->diff($product_creation_date);
             
                if ($diff->days > $extension_days) {
                } else {
             
                    echo '
                <a href="'.$urlval.'detail.php?slug='.$product['pslug'].'" style="text-decoration: none; color: inherit;">
                    <div class="product-header sticky-header" style="background-color: #f8f8f8; padding: 10px 15px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #ddd; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
                        <div class="product-info" style="display: flex; align-items: center; gap: 10px;">
                            <img class="product-image" src="' . $urlval . $product['product_image'] . '" alt="Product Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" />
                            <div class="product-details" style="flex-grow: 1;">
                                <h3 class="product-name" style="font-size: 16px; font-weight: 600; margin: 0; color: #333;">' . htmlspecialchars($product['product_name']) . '</h3>
                                <p class="product-price" style="font-size: 14px; color: #333; margin: 2px 0;">$' . number_format($product['product_price'], 2) . '</p>
                            </div>
                        </div>
                    </div>
                </a>';
                }
                

                echo '<ul style="list-style-type: none; padding: 0;">';

                foreach ($result as $message) {
                    $message_text = isset($message['message_text']) ? htmlspecialchars($message['message_text']) : '';
                    $message_text = preg_replace_callback(
                        '/\b(https?:\/\/[^\s]+)/i',
                        function ($matches) {
                            $url = $matches[1];
                            return '<a href="' . $url . '" target="_blank" style="color: #007bff; text-decoration: none;">' . htmlspecialchars($url) . '</a>';
                        },
                        $message_text
                    );
                
                    $sender = (isset($message['sender_id']) && $message['sender_id'] == $UserId) ? 'sender' : 'receiver';
                    $created_at = date("h:i a", strtotime($message['message_created_at']));
                
                    // Check if there are attachments (images)
                    $attachments = isset($message['attachments']) ? explode(',', $message['attachments']) : [];
                
                    echo '<li class="' . $sender . '" style="margin-bottom: 15px; word-wrap: break-word; padding: 10px; border-radius: 10px; width: 40%; background-color: ' . ($sender == 'sender' ? '#e1f7d5' : '#f0f0f0') . ';margin-left: ' . ($sender == 'sender' ? '50%' : '0%') . '; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                    echo '<p style="text-align: start; padding-left: 7px; margin: 0; font-size: 14px;">' . $message_text . '</p>';
                
                    // Display attachments (images)
                    if (!empty($attachments)) {
                        foreach ($attachments as $attachment) {
                            // Check if the attachment is an image
                            if (in_array(pathinfo($attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                echo '<img src="' . $urlval . 'upload/messages/' . htmlspecialchars($attachment) . '" alt="Image attachment" style="max-width: 100%; height: auto; margin-top: 10px; border-radius: 8px;" />';
                            }
                        }
                    }
                
                    echo '<span class="time" style="font-size: 12px; color: #888; display: block; text-align: right;">' . $created_at . '</span>';
                    echo '</li>';
                }
                

                echo '</ul>';
            } else {
             
                $getMesDataQuery = "
                    SELECT * FROM messages 
                    WHERE conversation_id = :conversationId
                    ORDER BY created_at ASC
                ";

        
                $stmt = $pdo->prepare($getMesDataQuery);
                $stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
                $stmt->execute();
                $messageResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($messageResult) {
                    echo '<ul style="list-style-type: none; padding: 0;">';

                    foreach ($messageResult as $message) {
                        $message_text = isset($message['message']) ? htmlspecialchars($message['message']) : '';
                        $message_text = preg_replace_callback(
                            '/\b(https?:\/\/[^\s]+)/i',
                            function ($matches) {
                                $url = $matches[1];
                                return '<a href="' . $url . '" target="_blank" style="color: #007bff; text-decoration: none;">' . htmlspecialchars($url) . '</a>';
                            },
                            $message_text
                        );

                        $sender = (isset($message['sender_id']) && $message['sender_id'] == $UserId) ? 'sender' : 'receiver';

                        $created_at = date("h:i a", strtotime($message['created_at']));

                        echo '<li class="' . $sender . '" style="margin-bottom: 15px; word-wrap: break-word; padding: 10px; border-radius: 10px; width: 40%; background-color: ' . ($sender == 'sender' ? '#e1f7d5' : '#f0f0f0') . ';margin-left: ' . ($sender == 'sender' ? '50%' : '0%') . '; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                        echo '<p style="text-align: start; padding-left: 7px; margin: 0; font-size: 14px;">' . $message_text . '</p>';
                        echo '<span class="time" style="font-size: 12px; color: #888; display: block; text-align: right;">' . $created_at . '</span>';
                        echo '</li>';
                    }

                    echo '</ul>';
                } else {
                    echo '<ul><li><p style="padding: 10px; font-size: 14px;">No messages found for this conversation</p></li></ul>';
                }
            }
        } catch (PDOException $e) {
            echo 'Error fetching data: ' . $e->getMessage();
        }
    } else {
        echo '<ul><li><p style="padding: 10px; font-size: 14px;">Invalid conversation ID</p></li></ul>';
    }
} else {
    echo '<ul><li><p style="padding: 10px; font-size: 14px;">User not authenticated</p></li></ul>';
}
?>