<?php
require_once 'global.php';
include_once 'header.php';

// Fetch Latest Products (Directly in the file)
try {
    $query = "SELECT * FROM products 
              ORDER BY created_at DESC 
              LIMIT 20"; // Limit to 20 latest products
    $stmt = $pdo->prepare($query); // Assuming `$pdo` is your database connection
    $stmt->execute();
    $latestProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestProducts = [];
    error_log('Error fetching latest products: ' . $e->getMessage());
}

// Banner for the page
$banner = $fun->getRandomBannerByPlacement('latest_products_header');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-card-body {
            padding: 15px;
        }

        .product-card-body h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .product-card .btn-primary {
            padding: 8px 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .banner {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .banner h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .banner p {
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .banner .btn {
            padding: 10px 20px;
            font-size: 1rem;
        }

        .no-products {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 500;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<div class="container mt-4">
    <?php if (!empty($banner)): ?>
        <div class="banner" style="background-color: <?= htmlspecialchars($banner['bg_color']) ?>;">
            <h1 style="color: <?= htmlspecialchars($banner['text_color']) ?>;"><?= htmlspecialchars($banner['title']) ?></h1>
            <p style="color: <?= htmlspecialchars($banner['text_color']) ?>;"><?= htmlspecialchars($banner['description']) ?></p>
            <?php if (!empty($banner['btn_text']) && !empty($banner['btn_url'])): ?>
                <a href="<?= htmlspecialchars($banner['btn_url']) ?>" class="btn btn-primary"><?= htmlspecialchars($banner['btn_text']) ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Latest Products</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (!empty($latestProducts)): ?>
    <?php foreach ($latestProducts as $product): ?>
        <div class="col">
            <div class="product-card">
                <img src="<?= htmlspecialchars($product['image'] ?? 'default_image.jpg') ?>" 
                     alt="<?= htmlspecialchars($product['name'] ?? 'Product', ENT_QUOTES, 'UTF-8') ?>" 
                     class="card-img-top">
                <div class="product-card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name'] ?? 'Unnamed', ENT_QUOTES, 'UTF-8') ?></h5>
                
                    <div class="d-flex justify-content-between">
                        <span class="product-price"><?= $fun->getFieldData('site_currency') . number_format($product['price'] ?? 0, 2) ?></span>
                      
                        <a href="<?= $urlval . 'p/' . urlencode($product['slug'] ?? '') . '/' . urlencode($product['name'] ?? '') . '/' . urlencode($product['description'] ?? '') ?>" 
   class="btn btn-primary mt-3">View Details</a>

    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No latest products found.</p>
<?php endif; ?>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
