<?php
require_once("../global.php");

$conversationId = base64_decode($_POST['conversation_id']);  
$sql = "
    SELECT 
        c.id AS conversation_id,
        c.created_at AS conversation_date,
        u1.username AS user_one_name,
        u2.username AS user_two_name,
        m.message AS last_message,
        m.created_at AS last_message_time
    FROM conversations c
    LEFT JOIN users u1 ON u1.id = c.user_one
    LEFT JOIN users u2 ON u2.id = c.user_two
    LEFT JOIN messages m ON m.conversation_id = c.id
    WHERE c.id = :conversation_id
    ORDER BY m.created_at DESC LIMIT 1";  // Fetch the latest message

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':conversation_id', $conversationId, PDO::PARAM_INT);
$stmt->execute();
$conversation = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversation) {
    $userOneName = $conversation['user_one_name'];
    $userTwoName = $conversation['user_two_name'];
    $lastMessage = $conversation['last_message'];
    $lastMessageTime = $conversation['last_message_time'];
    $conversationDate = $conversation['conversation_date'];
    
    $subject = "New Message from " . $userOneName . " and " . $userTwoName;
    
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
                <p><strong>Users Involved:</strong> " . $userOneName . " & " . $userTwoName . "</p>
                
                <div class='sender'>
                    <div class='message'>
                        <strong>" . $userOneName . ":</strong> " . htmlspecialchars($lastMessage) . "
                    </div>
                </div>
                <div class='receiver'>
                    <div class='message'>
                        <strong>" . $userTwoName . ":</strong> " . htmlspecialchars($lastMessage) . "
                    </div>
                </div>
                
                <p><strong>Sent at:</strong> " . date("F j, Y, g:i a", strtotime($lastMessageTime)) . "</p>
            </div>
            
            <div class='footer'>
                <p>Thank you for using our service!</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $recipientEmail = $_SESSION['email']; 

    // Send the email using smtp_mailer function (defined elsewhere)
    if (smtp_mailer($recipientEmail, $subject, $message)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Conversation not found.";
}
?>