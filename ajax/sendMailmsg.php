<?php
require_once("../global.php");

$conversationId = base64_decode($_POST['conversation_id']);  

// Updated SQL query
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
    c.user_two,
    u1.username AS user_one_name,  -- Added user_one_name from users table
    u2.username AS user_two_name   -- Added user_two_name from users table
FROM 
    products p
JOIN 
    conversations c ON p.id = c.proid
JOIN 
    messages m ON c.id = m.conversation_id
LEFT JOIN 
    users u1 ON u1.id = c.user_one  -- Join user_one
LEFT JOIN 
    users u2 ON u2.id = c.user_two  -- Join user_two
WHERE 
    c.id = :conversationId
ORDER BY 
    m.created_at ASC;
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':conversationId', $conversationId, PDO::PARAM_INT);
$stmt->execute();
$conversationMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($conversationMessages) {
    $userOneName = $conversationMessages[0]['user_one_name'];
    $userTwoName = $conversationMessages[0]['user_two_name'];
    $conversationDate = $conversationMessages[0]['product_created_at'];
    
    $subject = "Conversation Details between " . $userOneName . " and " . $userTwoName;
    
    $message = "
    <html>
    <head>
        <title>Conversation Details</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .message-container {
                margin-bottom: 20px;
                padding: 15px;
                border-radius: 8px;
                background-color: #f9f9f9;
            }
            .message-container p {
                margin: 5px 0;
            }
            .sender, .receiver {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            .sender {
                background-color: #e0f7fa;
                text-align: left;
            }
            .receiver {
                background-color: #ffecb3;
                text-align: right;
            }
            .sender .message, .receiver .message {
                font-size: 16px;
                line-height: 1.6;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                color: #888;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Conversation Details</h2>
                <p><strong>Conversation Date:</strong> " . date("F j, Y, g:i a", strtotime($conversationDate)) . "</p>
            </div>
            
            <div class='message-container'>
                <p><strong>Users Involved:</strong> " . $userOneName . " & " . $userTwoName . "</p>";

    // Loop through each message in the conversation
    foreach ($conversationMessages as $messageData) {
        $messageText = htmlspecialchars($messageData['message_text']);
        $messageTime = date("F j, Y, g:i a", strtotime($messageData['message_created_at']));
        $senderName = ($messageData['sender_id'] == $conversationMessages[0]['user_one']) ? $userOneName : $userTwoName;
        
        // Check sender and display the message
        if ($messageData['sender_id'] == $conversationMessages[0]['user_one']) {
            $message .= "<div class='sender'>
                            <strong>" . $userOneName . ":</strong>
                            " . $messageText . "<br>
                            <small><i>Sent at: " . $messageTime . "</i></small>
                          </div>";
        } else {
            $message .= "<div class='receiver'>
                            <strong>" . $userTwoName . ":</strong>
                            " . $messageText . "<br>
                            <small><i>Sent at: " . $messageTime . "</i></small>
                          </div>";
        }
    }

    $message .= "
            </div>
            
            <div class='footer'>
                <p>Thank you for using our service!</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $recipientEmail = $_SESSION['email']; 

 
    if (smtp_mailer($recipientEmail, $subject, $message)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Conversation not found.";
}
?>