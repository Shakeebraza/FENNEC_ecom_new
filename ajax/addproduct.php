<?php
require_once('../global.php');

// 1) Check if direct-upload is "Enabled" in approval_parameters table
$directUploadSetting = $fun->getData('approval_parameters', 'image_option_direct_upload', 1);
// e.g. $directUploadSetting is "Enabled" or "Disabled"
$directUploadEnabled = (strtolower($directUploadSetting) === 'enabled');

// Collect errors
$errors = [];

// Validate required fields
if (empty($_POST['productName'])) {
    $errors['productName'] = 'Product name is required.';
}

// Generate slug if empty
if (empty($_POST['slug'])) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['productName']))
          . '-' 
          . substr(md5(uniqid()), 0, 6);
    $_POST['slug'] = $slug;
}

/**
 * ONLY if direct-upload is "Enabled", require images.
 * If it's "Disabled," skip the image validation check entirely.
 */
if ($directUploadEnabled) {
    // Check for at least one image
    if (
        (!isset($_FILES['gallery']) || $_FILES['gallery']['error'][0] === UPLOAD_ERR_NO_FILE)
        && (empty($_FILES['gallery']['tmp_name'][0]) || empty($_FILES['image']['tmp_name']))
    ) {
        $errors['gallery'] = 'At least one image (gallery or single) is required.';
    }
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
    echo json_encode([
        'success' => false, 
        'message' => $errors
    ]);
    exit;
}

try {
    // Map the new checkboxes into 0/1
    $bold_enabled                   = !empty($_POST['bold']) ? 1 : 0;
    $featured_enabled               = !empty($_POST['featured']) ? 1 : 0;
    $front_featured_enabled         = !empty($_POST['frontFeatured']) ? 1 : 0;
    $highlight_enabled              = !empty($_POST['highlighted']) ? 1 : 0;
    $image_gallery_featured_enabled = !empty($_POST['imageGallery']) ? 1 : 0;
    $video_gallery_featured_enabled = !empty($_POST['videoGallery']) ? 1 : 0;

    // new: read product_type from the radio
    $productType = $_POST['product_type'] ?? 'standard';

    // Start with no product image path
    $productImagePath = null;
    $uploadedGalleryPaths = [];

    // ONLY attempt to upload images if directUploadEnabled is TRUE
    if ($directUploadEnabled) {

        // 1) Process GALLERY images
        $galleryImages = $_FILES['gallery'] ?? null;
        if ($galleryImages && is_array($galleryImages['tmp_name'])) {
            foreach ($galleryImages['tmp_name'] as $key => $tmpName) {
                // Skip any file that had an upload error
                if ($galleryImages['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }
                // Generate a unique file name for each gallery image
                $originalGalleryName = basename($galleryImages['name'][$key]);
                $uniqueGalleryName = uniqid() . '_' . $originalGalleryName;
                $galleryImagePath = '../upload/productgallery/' . $uniqueGalleryName;
                $galleryImagePathSave = 'upload/productgallery/' . $uniqueGalleryName;

                if (move_uploaded_file($tmpName, $galleryImagePath)) {
                    $uploadedGalleryPaths[] = $galleryImagePathSave;
                    // Use the first gallery image as main product image if not yet set
                    if ($key === 0 && !$productImagePath) {
                        $productImagePath = $galleryImagePathSave;
                    }
                }
            }
        }

        // 2) Fallback to SINGLE "image" upload if needed
        if (empty($productImagePath) && !empty($_FILES['image']['tmp_name'])) {
            $originalImageName = basename($_FILES['image']['name']);
            $uniqueImageName = uniqid() . '_' . $originalImageName;
            $imagePath = '../upload/product/' . $uniqueImageName;
            $imagePathSave = 'upload/product/' . $uniqueImageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                throw new Exception('Failed to upload the main product image.');
            }

            $productImagePath = $imagePathSave;
        }
    }
    // else: "Disabled" => skip any mandatory image logic

    // 3) Insert product data into DB
    $productData = [
        'name'                          => $_POST['productName'],
        'slug'                          => $_POST['slug'],
        'image'                         => $productImagePath ?? '', // might remain null if "Disabled"
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
        'is_enable'                     => 1, // e.g. "pending"

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

    // ***** WALLET DEDUCTION ***** //
    // Assume the total fee to be deducted is passed via a hidden input named "totalFee"
    $totalFee = isset($_POST['totalFee']) ? floatval($_POST['totalFee']) : 0;
    // Log wallet deduction attempt
    file_put_contents("./wallet_deduction.log", date("Y-m-d H:i:s") . " Attempting wallet deduction: fee = $totalFee, userId = " . base64_decode($_SESSION['userid']) . PHP_EOL, FILE_APPEND);
    if ($totalFee > 0) {
        $stmt2 = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - :fee WHERE id = :id");
        $stmt2->execute([
            ':fee' => $totalFee,
            ':id'  => base64_decode($_SESSION['userid'])
        ]);
        $rowsAffected = $stmt2->rowCount();
        if ($rowsAffected > 0) {
            file_put_contents("./wallet_deduction.log", date("Y-m-d H:i:s") . " Wallet deduction successful. Rows affected: $rowsAffected" . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents("./wallet_deduction.log", date("Y-m-d H:i:s") . " Wallet deduction failed. No rows affected." . PHP_EOL, FILE_APPEND);
        }
    }

    // 4) Insert gallery images if directUploadEnabled
    if ($directUploadEnabled && !empty($uploadedGalleryPaths)) {
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
    // ***** EMAIL NOTIFICATION *****
    $userId = base64_decode($_SESSION['userid']);
    $userData = $dbFunctions->getData('users', "id = '$userId'");
    if (empty($userData)) {
        throw new Exception('User not found.');
    }

    $user = $userData[0];
    $username = $security->decrypt($user['username']);
    $email = $security->decrypt($user['email']);

    // Fetch the email template
    $templateData = $fun->getTemplate('product_posted_notification');
    if (!$templateData) {
        throw new Exception('Email template not found.');
    }

    // Replace placeholders in the template
    $subject = str_replace('{{username}}', $username, $templateData['subject']);
    $body = str_replace(
        ['{{username}}', '{{product_name}}', '{{product_id}}'],
        [$username, $_POST['productName'], $productId],
        $templateData['body']
    );

    // Send the email
    $mailResponse = smtp_mailer($email, $subject, $body);
    if ($mailResponse !== 'sent') {
        file_put_contents("./email_error.log", "Failed to send email to $email. Response: $mailResponse\n", FILE_APPEND);
    }

    // Return success
    // Success
    echo json_encode([
        'success' => true, 
        'message' => 'Product added successfully!'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving product: ' . $e->getMessage()
    ]);
}
