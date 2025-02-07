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

// Check for at least one image
if (
    (!isset($_FILES['gallery']) || $_FILES['gallery']['error'][0] === UPLOAD_ERR_NO_FILE) &&
    (empty($_FILES['gallery']['tmp_name'][0]) || empty($_FILES['image']['tmp_name']))
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

// If there are errors, return them
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // Map the new checkboxes into 0/1
    $bold_enabled  = !empty($_POST['bold']) ? 1 : 0;
    $featured_enabled = !empty($_POST['featured']) ? 1 : 0;
    $front_featured_enabled = !empty($_POST['frontFeatured']) ? 1 : 0;
    $highlight_enabled = !empty($_POST['highlighted']) ? 1 : 0;
    $image_gallery_featured_enabled = !empty($_POST['imageGallery']) ? 1 : 0;
    $video_gallery_featured_enabled = !empty($_POST['videoGallery']) ? 1 : 0;

    // new: read product_type from the radio
    // fallback to 'standard' if not specified
    $productType = $_POST['product_type'] ?? 'standard';

    $productImagePath = null;

    // 1. Process GALLERY images
    $galleryImages = $_FILES['gallery'] ?? null;
    $uploadedGalleryPaths = [];

    if ($galleryImages && is_array($galleryImages['tmp_name'])) {
        foreach ($galleryImages['tmp_name'] as $key => $tmpName) {
            // Skip any file that had an upload error
            if ($galleryImages['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }
            $galleryImageName = basename($galleryImages['name'][$key]);
            $galleryImagePath = '../upload/productgallery/' . $galleryImageName;
            $galleryImagePathSave = 'upload/productgallery/' . $galleryImageName;

            if (move_uploaded_file($tmpName, $galleryImagePath)) {
                $uploadedGalleryPaths[] = $galleryImagePathSave;
                // First gallery image => set main product image (if not set)
                if ($key === 0 && !$productImagePath) {
                    $productImagePath = $galleryImagePathSave;
                }
            }
        }
    }

    // 2. Fallback to SINGLE "image" upload if needed
    if (empty($productImagePath) && !empty($_FILES['image']['tmp_name'])) {
        $imagePath = '../upload/product/' . basename($_FILES['image']['name']);
        $imagePathSave = 'upload/product/' . basename($_FILES['image']['name']);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload the main product image.');
        }

        $productImagePath = $imagePathSave;
    }

    // 3. Insert product into DB
    $productData = [
        'name'                          => $_POST['productName'],
        'slug'                          => $_POST['slug'],
        'image'                         => $productImagePath,
        'description'                   => $_POST['description'],
        'brand'                         => $_POST['brand'],
        'conditions'                    => $_POST['condition'],
        'category_id'                   => $_POST['category'],
        'subcategory_id'                => $_POST['subcategory'],
        'price'                         => $_POST['price'],
        'discount_price'                => "",
        'country_id'                    => $_POST['country'],
        'city_id'                       => $_POST['city'],
        'aera_id'                       => $_POST['aera'] ?? 0,
        'user_id'                       => base64_decode($_SESSION['userid']),

        // Boost/fetures fields
        'bold_enabled'                  => $bold_enabled,
        'featured_enabled'              => $featured_enabled,
        'front_featured_enabled'        => $front_featured_enabled,
        'highlight_enabled'             => $highlight_enabled,
        'image_gallery_featured_enabled'=> $image_gallery_featured_enabled,
        'video_gallery_featured_enabled'=> $video_gallery_featured_enabled,

        // product_type (standard / premium / gold, etc.)
        'product_type'                  => $productType
    ];

    $result = $dbFunctions->setData('products', $productData);
    if (!$result['success']) {
        echo json_encode($result);
        exit;
    }

    // Last inserted product id
    $productId = $pdo->lastInsertId();

    // 4. Insert gallery images
    if (!empty($uploadedGalleryPaths)) {
        $galleryStmt = $pdo->prepare("
            INSERT INTO product_images (product_id, image_path, sort, created_at)
            VALUES (:product_id, :image_path, :sort, current_timestamp())
        ");
        $sortOrder = 0;
        foreach ($uploadedGalleryPaths as $path) {
            $galleryStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $galleryStmt->bindValue(':image_path', $path, PDO::PARAM_STR);
            $galleryStmt->bindValue(':sort', $sortOrder, PDO::PARAM_INT);
            $galleryStmt->execute();
            $sortOrder++;
        }
    }

    // Success
    echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving product: ' . $e->getMessage()
    ]);
}
