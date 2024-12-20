<?php
require_once("../global.php");
header('Content-Type: application/json');

$errors = [];


if (empty($_POST['productName'])) {
    $errors['productName'] = 'Product name is required.';
}
if (empty($_POST['description'])) {
    $errors['description'] = 'Description is required.';
}
if (empty($_POST['category'])) {
    $errors['category'] = 'Category is required.';
}
if (empty($_POST['subcategory'])) {
    $errors['subcategory'] = 'Subcategory is required.';
}
if (empty($_POST['brand'])) {
    $errors['brand'] = 'Brand is required.';
}
if (empty($_POST['condition'])) {
    $errors['condition'] = 'Condition is required.';
}
if (empty($_POST['price'])) {
    $errors['price'] = 'Price is required.';
}


if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $productId = $_POST['productId'] ?? null;
    if (!$productId) {
        throw new Exception('Product ID is missing.');
    }


    $productData = [
        'name' => $_POST['productName'],
        'description' => $_POST['description'],
        'brand' => $_POST['brand'],
        'conditions' => $_POST['condition'],
        'category_id' => $_POST['category'],
        'subcategory_id' => $_POST['subcategory'],
        'price' => $_POST['price'],
    ];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = '../upload/product/' . basename($_FILES['image']['name']);
        $imagePathSave = 'upload/product/' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $productData['image'] = $imagePathSave;
        } else {
            throw new Exception('Failed to upload main image.');
        }
    }
    

    $updateResult = updateData('products', $productData, ['id' => $productId]);
    if (!$updateResult['success']) {
        echo json_encode($updateResult);
        exit;
    }
    
    
    if (isset($_FILES['gallery'])) {
        $galleryStmt = $pdo->prepare("
            INSERT INTO product_images (product_id, image_path, created_at) 
            VALUES (:product_id, :image_path, current_timestamp())");

        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['gallery']) {
                $galleryImagePath = '../upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                $galleryImagePathSave = 'upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($tmpName, $galleryImagePath)) {
                    $galleryStmt->bindValue(':product_id', $productId);
                    $galleryStmt->bindValue(':image_path', $galleryImagePathSave);
                    $galleryStmt->execute();
                }
            }
        }
    }



    echo json_encode(['success' => true, 'message' => 'Product updated successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()]);
}


function updateData($tableName, $data, $conditions)
{
    global $pdo;
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);

    $sanitizedData = [];
    foreach ($data as $key => $value) {
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $sanitizedData[$key] = $value;
    }

    try {

        $columns = implode(' = ?, ', array_keys($sanitizedData)) . ' = ?';
        $where = implode(' = ? AND ', array_keys($conditions)) . ' = ?';
        $stmt = $pdo->prepare("UPDATE `$tableName` SET $columns WHERE $where");


        $stmt->execute(array_merge(array_values($sanitizedData), array_values($conditions)));
        return ['success' => true, 'message' => 'Data updated successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
