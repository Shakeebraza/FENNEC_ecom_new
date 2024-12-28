<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {
    $user_id = base64_decode($_SESSION['userid']);

    $query = "
    SELECT c.*, 
           p.image AS product_image,
           u1.username AS sender_name,
           u2.username AS receiver_name,
           (SELECT m.message 
            FROM messages m 
            WHERE m.conversation_id = c.id 
            ORDER BY m.id DESC LIMIT 1) AS last_message,
           (SELECT m.is_read
            FROM messages m 
            WHERE m.conversation_id = c.id 
            ORDER BY m.id DESC LIMIT 1) AS last_message_read,
           (SELECT m.sender_id
            FROM messages m 
            WHERE m.conversation_id = c.id 
            ORDER BY m.id DESC LIMIT 1) AS last_sender_id,
           (SELECT m.created_at
            FROM messages m 
            WHERE m.conversation_id = c.id 
            ORDER BY m.id DESC LIMIT 1) AS last_message_time,
           -- Check if the product is expired (created_at > 30 days)
           CASE 
               WHEN DATEDIFF(CURRENT_DATE, p.created_at) > 30 THEN 'expired'
               WHEN p.extension BETWEEN 0 AND 60 THEN 'available'
               ELSE 'not_available'
           END AS product_status
    FROM conversations c
    LEFT JOIN products p ON p.id = c.proid
    LEFT JOIN users u1 ON u1.id = c.user_one
    LEFT JOIN users u2 ON u2.id = c.user_two
    WHERE c.user_one = :user_id OR c.user_two = :user_id
    ORDER BY last_message_time DESC, c.id DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($conversations) {
        foreach ($conversations as $conversation) {
            $product_image = $conversation['product_image'] ? $conversation['product_image'] : 'images/admin.jpg';  

            $conversation_id = base64_encode($conversation['id']);
            $last_message = $conversation['last_message'] ? $conversation['last_message'] : 'Send a message to start the conversation';
            $last_message_read = $conversation['last_message_read']; 
            $last_sender_id = $conversation['last_sender_id']; 

            $sender_name = $conversation['sender_name'];
            $receiver_name = $conversation['receiver_name'];

            // If user_two is null, show only the sender name
            if ($conversation['user_two'] == null) {
                $display_name = $sender_name;
            } else {
                $display_name = ($conversation['user_one'] == $user_id) ? $receiver_name : $sender_name;
            }

            if ($last_sender_id != $user_id) {
                $message_style = ($last_message_read == 0) ? 'font-weight: bold;' : '';
            } else {
                $message_style = '';
            }

            $message_words = explode(' ', $last_message);
            $truncated_message = implode(' ', array_slice($message_words, 0, 2)) . (count($message_words) > 2 ? '...' : '');

            $product_status = $conversation['product_status'];
            if ($product_status == 'expired') {
                $status_message = 'Expired';
                $status_color = '#d9534f'; 
            } elseif ($product_status == 'available') {
                $status_message = 'Available';
                $status_color = '#28a745'; 
            } else {
                $status_message = 'Admin chat';
                $status_color = '#6c757d'; 
            }

            echo '
            <div class="d-flex align-items-center message-container">
                <a href="#messages543" class="d-flex align-items-center message-item" onclick="loadMessages(
            \'' . $conversation_id . '\', 
            \'' . addslashes($display_name ?? '') . '\', 
            \'' . $urlval . addslashes($product_image ?? '') . '\',
            \'' . addslashes($status_message ?? '') . '\'
        ); updateUrlWithChatId(\'' . $conversation_id . '\')">
                    <div class="flex-shrink-0">
                        <img class="img-fluid" src="' . $urlval . $product_image . '" alt="user img" style="width: 40px; height: 40px; border-radius: 50%;">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 style="font-size: 16px; margin: 0; color: #157347; font-weight:700">' . $display_name . '</h3>
                        <p style="font-size: 12px; color: #000; ' . $message_style . '">' . $truncated_message . '</p> 
                        <p style="font-size: 12px; color: ' . $status_color . ';">' . $status_message . '</p>
                    </div>
                </a>
                <div class="delete-icon">
                    <i class="fas fa-trash-alt" title="Delete" onclick="deleteConversation(\'' . $conversation_id . '\')"></i>
                </div>
            </div>
            <hr style="color: #157347 !important; width:100%; height:2px;">
        ';
        }
    } else {
        echo '<p>No conversations found</p>';
    }
} else {
    echo '<p>User not logged in</p>';
}
?>
