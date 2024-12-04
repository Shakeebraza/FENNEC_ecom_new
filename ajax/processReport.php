<?php
require_once("../global.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $pdo; 


    if (!isset($_SESSION['userid'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

  
    $userId = base64_decode($_SESSION['userid']);
    $productId = $_POST['productid'] ?? null;
    $reason = $_POST['reportReason'] ?? null;
    $additionalInfo = $_POST['additionalInfo'] ?? null;


    if ($productId && $reason) {
        try {
     
            $stmt = $pdo->prepare("
                INSERT INTO reports (product_id, user_id, reason, additional_info, created_at) 
                VALUES (:product_id, :user_id, :reason, :additional_info, NOW())
            ");
            $stmt->execute([
                ':product_id' => $productId,
                ':user_id' => $userId,
                ':reason' => $reason,
                ':additional_info' => $additionalInfo,
            ]);

            echo json_encode(['success' => true, 'message' => 'Report submitted successfully.']);
        } catch (PDOException $e) {
        
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    }
} else {
 
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
