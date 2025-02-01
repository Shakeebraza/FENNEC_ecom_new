<?php
require_once('../../../global.php');

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch the main product details
    $stmt = $pdo->prepare("
        SELECT 
            p.id AS product_id,
            p.name AS product_name,
            p.slug AS product_slug,
            p.description AS product_description,
            p.image AS product_image,
            p.price AS product_price,
            p.product_type AS product_type,
            p.discount_price AS product_discount_price,
            c.category_name AS category_name,
            s.subcategory_name AS subcategory_name,
            ci.name AS city_name,
            co.name AS country_name
        FROM 
            products p
        LEFT JOIN 
            categories c ON p.category_id = c.id
        LEFT JOIN 
            subcategories s ON p.subcategory_id = s.id
        LEFT JOIN 
            cities ci ON p.city_id = ci.id
        LEFT JOIN 
            countries co ON p.country_id = co.id
        WHERE 
            p.is_enable = 1 AND p.id = :product_id");

    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

     
        $imagesStmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = :product_id");
        $imagesStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $imagesStmt->execute();
        $gallery_images = $imagesStmt->fetchAll(PDO::FETCH_COLUMN);

       
        $product['gallery_images'] = implode(',', $gallery_images);
        
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
