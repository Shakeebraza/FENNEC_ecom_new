<?php
require_once("../global.php");
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['order']) && is_array($data['order'])) {
        $success = true;
        $updatedImages = $data['order'];

 
        $mainImageUrl = null;


        foreach ($updatedImages as $index => $image) {
            $sort = (int)$image['sort'];
            $id = (int)$image['key'];

       
            if ($sort === 0) {
                $mainImageUrl = $image['url']; 
            }

    
            $stmt = $pdo->prepare("SELECT sort FROM product_images WHERE id = ?");
            $stmt->execute([$id]);
            $currentSort = $stmt->fetchColumn();

            if ($currentSort !== false && $currentSort != $sort) {
                $stmt = $pdo->prepare("UPDATE product_images SET sort = ? WHERE id = ?");
                if (!$stmt->execute([$sort, $id])) {
                    $success = false;
                    break;
                }

          
                $affectedRows = $stmt->rowCount();
                if ($affectedRows == 0) {
                    error_log("No rows updated for image ID: $id with sort: $sort");
                }
            }
        }
        $stmt = $pdo->prepare("SELECT product_id FROM product_images WHERE id = ?");
        $stmt->execute([$id]);
        $currentproduct_id = $stmt->fetchColumn();
        if ($mainImageUrl !== null && isset($currentproduct_id)) {
            $productId = $currentproduct_id;

            $stmt = $pdo->prepare("UPDATE products SET image = ? WHERE id = ?");
            if (!$stmt->execute([$mainImageUrl, $productId])) {
                $success = false;
            }
        }

     
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
