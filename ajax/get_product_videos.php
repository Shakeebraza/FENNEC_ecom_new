<?php
require_once("../global.php");

$productId = $_GET['product_id']; 

$stmt = $pdo->prepare("SELECT video_paths FROM product_videos WHERE product_id = :product_id");
$stmt->bindParam(':product_id', $productId);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $videoPaths = explode(',', $row['video_paths']);
    echo json_encode(['success' => true, 'videoPaths' => $videoPaths]);
} else {
    echo json_encode(['success' => false, 'message' => 'No videos found.']);
}
?>
