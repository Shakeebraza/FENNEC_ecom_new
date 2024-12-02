<?php
require_once "../global.php";

if (isset($_SESSION['userid'])) {
    $productId = $security->decrypt($_POST['product_id']) ?? null;
    $userId = base64_decode($_SESSION['userid']) ?? null;
    $productUserid = $productFun->GetUserId($productId);

    if (isset($productId) && isset($userId) && isset($productUserid)) {
        $chatData = $dbFunctions->getDatanotenc(
            'conversations',
            "proid = '$productId' AND ((user_one = $userId AND user_two = $productUserid) OR (user_one = $productUserid AND user_two = $userId))"
        );

        if ($chatData) {
            $response = ['success' => true, 'message' => 'Existing chat found.'];
        } else {
            $insertData = [
                'user_one' => $userId,
                'user_two' => $productUserid,
                'proid' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $insertResult = $dbFunctions->setData('conversations', $insertData);

            if ($insertResult['success']) {
                $response = ['success' => true, 'message' => 'New chat started successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to start new chat.'];
            }
        }
    } else {
        $response = ['success' => false, 'message' => 'Required IDs are missing.'];
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'User session not found.']);
}

?>
