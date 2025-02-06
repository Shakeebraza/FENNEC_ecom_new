<?php
require_once('../global.php');

$errors = [];

// Validate required fields
if (empty($_POST['productName'])) {
    $errors['productName'] = 'Product name is required.';
}

// Generate slug if empty
if (empty($_POST['slug'])) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['productName'])) . '-' . substr(md5(uniqid()), 0, 6);
    $_POST['slug'] = $slug;
}

// If no gallery AND no single image, throw an error
if (
    (!isset($_FILES['gallery']) || $_FILES['gallery']['error'][0] === UPLOAD_ERR_NO_FILE) 
    && (empty($_FILES['gallery']['tmp_name'][0]) || empty($_FILES['image']['tmp_name']))
) {
    $errors['gallery'] = 'At least one image (gallery or single) is required.';
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

// Default price to 0 if not provided
if (empty($_POST['price'])) {
    $_POST['price'] = 0;
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $productImagePath = null;

    // ===========================
    // 1. Process GALLERY images
    // ===========================
    // We will also set the product's main image from the first valid gallery image if it exists.
    $galleryImages = $_FILES['gallery'] ?? null;
    $uploadedGalleryPaths = []; // We'll keep track of all successfully moved gallery files

    if ($galleryImages && is_array($galleryImages['tmp_name'])) {
        foreach ($galleryImages['tmp_name'] as $key => $tmpName) {
            // Skip any file that had an upload error
            if ($galleryImages['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }

            $galleryImageName = basename($galleryImages['name'][$key]);
            $galleryImagePath = '../upload/productgallery/' . $galleryImageName; 
            $galleryImagePathSave = 'upload/productgallery/' . $galleryImageName;

            // Attempt to move uploaded file
            if (move_uploaded_file($tmpName, $galleryImagePath)) {
                // Keep track of successfully saved image path
                $uploadedGalleryPaths[] = $galleryImagePathSave;

                // If this is the first gallery image, set it as main product image
                if ($key === 0 && !$productImagePath) {
                    $productImagePath = $galleryImagePathSave;
                }
            }
        }
    }

    // =====================================
    // 2. Fallback to SINGLE "image" upload
    // =====================================
    // If no main product image was determined from the gallery, use the primary image field
    if (empty($productImagePath) && !empty($_FILES['image']['tmp_name'])) {
        $imagePath = '../upload/product/' . basename($_FILES['image']['name']);
        $imagePathSave = 'upload/product/' . basename($_FILES['image']['name']);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload the main product image.');
        }

        $productImagePath = $imagePathSave;
    }

    // ===============================
    // 3. Insert product into DB
    // ===============================
    $productData = [
        'name'          => $_POST['productName'],
        'slug'          => $_POST['slug'],
        'image'         => $productImagePath,
        'description'   => $_POST['description'],
        'brand'         => $_POST['brand'],
        'conditions'    => $_POST['condition'],
        'category_id'   => $_POST['category'],
        'subcategory_id'=> $_POST['subcategory'],
        'price'         => $_POST['price'],
        'discount_price'=> "", // or whatever your logic is
        'country_id'    => $_POST['country'],
        'city_id'       => $_POST['city'],
        // 'is_enable'    => 2,
        'aera_id'       => $_POST['aera'] ?? 0,
        'user_id'       => base64_decode($_SESSION['userid']),
    ];

    // Insert the product; $dbFunctions->setData() presumably returns ['success' => bool, ...]
    $result = $dbFunctions->setData('products', $productData);
    if (!$result['success']) {
        echo json_encode($result);
        exit;
    }

    // Retrieve last inserted product id
    $productId = $pdo->lastInsertId();

    // ======================================
    // 4. Insert gallery images into DB
    // ======================================
    // We'll do a simple loop with a numeric sort that increments for each image.
    if (!empty($uploadedGalleryPaths)) {
        // Prepare the statement in advance
        $galleryStmt = $pdo->prepare("
            INSERT INTO product_images (product_id, image_path, sort, created_at) 
            VALUES (:product_id, :image_path, :sort, current_timestamp())
        ");

        // Initialize sort to 0 (first image => sort=0, second => sort=1, etc.)
        $sortOrder = 0;

        foreach ($uploadedGalleryPaths as $path) {
            $galleryStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $galleryStmt->bindValue(':image_path', $path, PDO::PARAM_STR);
            $galleryStmt->bindValue(':sort', $sortOrder, PDO::PARAM_INT);
            $galleryStmt->execute();

            $sortOrder++;
        }
    }

    // =====================================
    // 5. Return success response
    // =====================================
    echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error saving product: ' . $e->getMessage()
    ]);
}
?>
