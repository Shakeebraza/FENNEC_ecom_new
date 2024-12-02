<?php
require_once('../global.php');
$setSession = $fun->isSessionSet();

$redirectUrl = $urlval . 'index.php'; 
if ($setSession == false) {
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>'; 
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: '.$redirectUrl.'');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($security->decrypt($_POST['id']));

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $productId]);

        $stmt = $pdo->prepare("DELETE FROM favorites WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $productId]);

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);

        $pdo->commit();

        // Modify response to include a message even on success
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()]);
    }
}

?>