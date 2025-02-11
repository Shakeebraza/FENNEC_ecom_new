<?php
require_once 'global.php';
include_once 'header.php';

// Fetch Expiring Soon Products (Directly in the file)
try {
    $query = "SELECT * FROM products 
              WHERE expiry_date IS NOT NULL 
              AND expiry_date > NOW() 
              AND expiry_date < DATE_ADD(NOW(), INTERVAL 90 DAY) 
              ORDER BY expiry_date ASC";
    $stmt = $pdo->prepare($query); // Assuming `$pdo` is your database connection
    $stmt->execute();
    $expiringSoonProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $expiringSoonProducts = [];
    error_log('Error fetching expiring soon products: ' . $e->getMessage());
}

// Banner for the page
$banner = $fun->getRandomBannerByPlacement('expiring_soon_header');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiring Soon</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            max-height: 200px;
            object-fit: cover;
        }

        .product-card-body {
            padding: 15px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: red;
        }

        .banner {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
<div class="container mt-4">
    <?php if (!empty($banner)): ?>
        <div class="banner" style="background-color: <?= $banner['bg_color'] ?>;">
            <h1 style="color: <?= $banner['text_color'] ?>;"><?= $banner['title'] ?></h1>
            <p style="color: <?= $banner['text_color'] ?>;"><?= $banner['description'] ?></p>
            <?php if (!empty($banner['btn_text']) && !empty($banner['btn_url'])): ?>
                <a href="<?= $banner['btn_url'] ?>" class="btn btn-primary"><?= $banner['btn_text'] ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Expiring Soon Products</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (!empty($expiringSoonProducts)): ?>
            <?php foreach ($expiringSoonProducts as $product): ?>
                <div class="col">
                    <div class="product-card">
                        <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img-top">
                        <div class="product-card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="text-muted"><?= htmlspecialchars($product['country']) ?> | <?= htmlspecialchars($product['city']) ?></p>
                            <div class="d-flex justify-content-between">
                                <span class="product-price"><?= $fun->getFieldData('site_currency') . number_format($product['price'], 2) ?></span>
                                <span class="small text-muted">Expires: <?= htmlspecialchars($product['expiry_date']) ?></span>
                            </div>
                            <a href="<?= $urlval . 'p/' . urlencode($product['slug']) ?>" class="btn btn-primary mt-3">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No expiring soon products found.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
