<?php
require_once("../global.php");
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['image_key']) && is_numeric($data['image_key'])) {
        $success = true;
        $id = (int)$data['image_key'];
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
        $stmt->execute([$id]);
        $imagePath = $stmt->fetchColumn();

        if ($imagePath) {
            $filePath = __DIR__ . '/../' . $imagePath;
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    $success = false;
                    error_log("Failed to delete file: $filePath");
                }
            }
            $deleteStmt = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
            if (!$deleteStmt->execute([$id])) {
                $success = false;
                error_log("Failed to delete record for image ID: $id");
            }
            if ($deleteStmt->rowCount() == 0) {
                error_log("No record found to delete for image ID: $id");
                $success = false;
            }
        } else {
            error_log("Image not found for ID: $id");
            $success = false;
        }

        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid or missing image_key']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
