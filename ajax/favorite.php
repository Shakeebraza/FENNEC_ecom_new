<?php
require_once "../global.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $productId = isset($data['id']) ? $data['id'] : null;

    if ($productId) {
        $isFavorited = $productFun->toggleFavorite($productId, base64_decode($_SESSION['userid']));
        echo json_encode([
            'success' => true,
            'isFavorited' => $isFavorited,
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
