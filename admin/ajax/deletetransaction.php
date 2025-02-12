<?php
require_once("../../global.php");
// Optional: role check here
$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction ID']);
    exit;
}
try {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE transaction_id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Transaction not found or already deleted']);
    }
} catch (PDOException $e) {
    error_log("Delete transaction error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
exit;
?>
