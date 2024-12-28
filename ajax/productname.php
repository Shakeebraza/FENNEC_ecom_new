<?php
require_once('../global.php');

if (isset($_GET['chatid'])) {
    $chatId = base64_decode($_GET['chatid']);
    $sessionUserId = base64_decode($_SESSION['userid']);
    $sql = "SELECT c.user_one, c.user_two, p.name AS productName 
            FROM conversations c
            JOIN products p ON c.proid = p.id
            WHERE c.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$chatId]);

    if ($stmt->rowCount() > 0) {
        $conversation = $stmt->fetch();
        
    
        $matchedUserId = null;
        if ($conversation['user_one'] == $sessionUserId) {
            $matchedUserId = $conversation['user_two'];
        } elseif ($conversation['user_two'] == $sessionUserId) {
            $matchedUserId = $conversation['user_one'];
        }

        if ($matchedUserId) {
  
            $userSql = "SELECT username FROM users WHERE id = ?";
            $userStmt = $pdo->prepare($userSql);
            $userStmt->execute([$matchedUserId]);
            
            if ($userStmt->rowCount() > 0) {
                $user = $userStmt->fetch();
                $userName = $user['username'];
                $firstLetter = strtoupper($userName[0]); 

                $response = [
                    'userName' => $conversation['productName'],
                    'productName' => $userName,
                    'firstLetter' => $firstLetter
                ];
                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'User not found']);
            }
        } else {
            echo json_encode(['error' => 'No matching user found']);
        }
    } else {
        echo json_encode(['error' => 'Conversation not found']);
    }
} else {
    echo json_encode(['error' => 'chatId is required']);
}
?>
