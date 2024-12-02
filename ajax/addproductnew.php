<?php
require_once('../global.php');

$errors = [];


if (empty($_POST['productName'])) {
    $errors['productName'] = 'Product name is required.';
}
if (empty($_POST['slug'])) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['productName'])) . '-' . substr(md5(uniqid()), 0, 6);
    $_POST['slug'] = $slug;
}
if ($_FILES['gallery']['error'] === UPLOAD_ERR_NO_FILE && empty($_FILES['gallery']['tmp_name'][0])) {
    $errors['gallery'] = 'Image or gallery image is required.';
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
if (empty($_POST['country'])) {
    $errors['country'] = 'Country is required.';
}
if (empty($_POST['price'])) {
    $errors['price'] = 'Price is required.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $productImagePath = null;

    // Process gallery and save the first image as the product image
    if (isset($_FILES['gallery'])) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                $galleryImagePath = '../upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                $galleryImagePathSave = 'upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($tmpName, $galleryImagePath)) {
                    if ($key === 0) {
                        // Save the first gallery image as the product image
                        $productImagePath = $galleryImagePathSave;
                    }
                }
            }
        }
    }

    // If no gallery image was uploaded, fallback to uploaded product image
    if (empty($productImagePath)) {
        $imagePath = '../upload/product/' . basename($_FILES['image']['name']);
        $imagePathSave = 'upload/product/' . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload image.');
        }
        $productImagePath = $imagePathSave;
    }

    // Prepare product data
    $productData = [
        'name' => $_POST['productName'],
        'slug' => $_POST['slug'],
        'image' => $productImagePath,
        'description' => $_POST['description'],
        'brand' => $_POST['brand'],
        'conditions' => $_POST['condition'],
        'category_id' => $_POST['category'],
        'subcategory_id' => $_POST['subcategory'],
        'price' => $_POST['price'],
        'discount_price' => "",
        'country_id' => $_POST['country'],
        'city_id' => $_POST['city'],
        'aera_id' => $_POST['aera'] ?? 0,
        'user_id' => base64_decode($_SESSION['userid']),
    ];

    // Insert product and fetch its ID
    $result = $dbFunctions->setData('products', $productData);

    if (!$result['success']) {
        echo json_encode($result);
        exit;
    }

    // Fetch the last inserted product ID
    $productId = $pdo->lastInsertId();

    // Save gallery images in the database
    if (isset($_FILES['gallery'])) {
        $galleryStmt = $pdo->prepare("
            INSERT INTO product_images (product_id, image_path, created_at) 
            VALUES (:product_id, :image_path, current_timestamp())");

        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                $galleryImagePath = '../upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                $galleryImagePathSave = 'upload/productgallery/' . basename($_FILES['gallery']['name'][$key]);
                    $galleryStmt->bindValue(':product_id', $productId);
                    $galleryStmt->bindValue(':image_path', $galleryImagePathSave);
                    $galleryStmt->execute();
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error saving product: ' . $e->getMessage()]);
}

?>