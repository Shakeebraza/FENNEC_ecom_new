    <?php
    require_once 'global.php';
    include_once 'header.php';

    if (isset($_GET['username'])) {
        $username = trim($_GET['username']);
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            echo "<div class='container py-4'><p>Invalid username format.</p></div>";
            include_once 'footer.php';
            exit();
        }

        $username = htmlspecialchars($username);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stmt_detail = $pdo->prepare("SELECT * FROM user_detail WHERE userid = :userid");
            $stmt_detail->execute(['userid' => $user['id']]);
            $user_detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);

            if ($user_detail) {
                $fullName = htmlspecialchars($user_detail['first_name'] . ' ' . $user_detail['last_name']);
                $location = htmlspecialchars($user_detail['city'] . ', ' . $user_detail['country']);
            } else {
                $fullName = htmlspecialchars($user['username']);
                $location = 'N/A';
            }

            $profileLink = "profile.php?username=" . urlencode($user['username']);
            $firstLetter = strtoupper(substr($user['username'], 0, 1));
            $emailVerified = !empty($user['email_verified_at']);
            $postingDuration = number_format($fun->calculatePostingDuration($user['created_at']), 1); 
            $totalItems = $fun->getTotalItems($user['id']);
            $categories = $fun->getCategories($user['id']);
            $listings = $fun->getListings($user['id']);
        } else {
            echo "<div class='container py-4'><p>User not found.</p></div>";
            include_once 'footer.php';
            exit();
        }
    } else {
        echo "<div class='container py-4'><p>No username specified.</p></div>";
        include_once 'footer.php';
        exit();
    }
    ?>

<div class="container py-4" style="max-width: 80%; margin: auto; font-family: Arial, sans-serif;">

    <!-- User Header -->
    <div class="d-flex align-items-center justify-content-between mb-4" style="border-bottom: 1px solid #ccc; padding-bottom: 15px;">
        <div class="d-flex align-items-center gap-3">
            <!-- User Initials -->
            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm" 
                style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                <?= $firstLetter ?>
            </div>
            <!-- User Details -->
            <div>
                <h5 class="mb-1" style="font-weight: 600; color: #333;"><?= $fullName ?></h5>
                <small class="text-muted" style="font-size: 0.9rem;">Posting for <?= $postingDuration ?> years</small>
            </div>
        </div>
        <!-- Location and Email Verification -->
        <div style="text-align: right;">
            <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem; color: #555;">
                <i class="fas fa-location-dot text-danger"></i>
                <span><?= $location ?></span>
            </div>
            <div class="d-flex align-items-center gap-2" style="font-size: 0.9rem; <?= $emailVerified ? 'color: #28a745;' : 'color: #dc3545;' ?>">
                <i class="fas fa-check-circle"></i>
                <small><?= $emailVerified ? 'Email address verified' : 'Email not verified' ?></small>
            </div>
        </div>
    </div>

    <!-- Selling History -->
    <div class="mb-4" style="padding: 10px; background-color: #f8f9fa; border-radius: 8px;">
        <h6 class="mb-3" style="color: #444; font-weight: bold;">Selling History</h6>
        <div class="mb-2" style="color: #555; font-size: 0.95rem;">
            <i class="far fa-circle me-2" style="color: #007bff;"></i>
            Total items: <strong><?= htmlspecialchars($totalItems) ?></strong>
        </div>
        <div style="color: #555; font-size: 0.95rem;">
            <i class="fas fa-tags me-2" style="color: #007bff;"></i>
            Categories: <strong><?= htmlspecialchars($categories) ?></strong>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="mb-4" style="border-bottom: 2px solid #ddd;">
        <ul class="nav nav-tabs" style="border-bottom: none;">
            <li class="nav-item">
                <a class="nav-link active" href="#" style="border-bottom: 2px solid #198754; color: #198754; font-weight: bold;">Listings</a>
            </li>
        </ul>
    </div>

    <!-- Listings Section -->
    <h6 class="mb-4" style="color: #333; font-weight: bold;"><?= count($listings) ?> Items for Sale</h6>

    <div class="d-flex flex-column gap-3">
        <?php if (count($listings) > 0): ?>
            <?php foreach ($listings as $listing): ?>
                <!-- Individual Listing Card -->
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="row g-0">
                        <!-- Image Section -->
                        <div class="col-4" style="background: #f8f8f8;">
                            <div class="position-relative">
                                <img src="<?= $listing['image_url'] ?>" 
                                    class="img-fluid rounded-start" 
                                    alt="<?= $listing['title'] ?>" 
                                    style="height: 100%; object-fit: cover;">
                                <span class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 rounded-1" 
                                    style="font-size: 0.8rem;">
                                    <i class="fas fa-camera"></i> <?= $listing['photo_count'] ?>
                                </span>
                            </div>
                        </div>
                        <!-- Details Section -->
                        <div class="col-8">
                            <div class="card-body d-flex justify-content-between align-items-start" style="padding: 15px;">
                                <div>
                                    <h6 class="card-title mb-1" style="color: #333; font-weight: bold;">
                                        <?= $listing['title'] ?>
                                    </h6>
                                    <p class="card-text mb-1" style="color: #888; font-size: 0.9rem;">
                                        <i class="fas fa-map-marker-alt me-1"></i><?= $location ?>
                                    </p>
                                    <p class="card-text text-success" style="font-size: 1rem; font-weight: bold;">
                                        £<?= $listing['price'] ?>
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <button class="btn btn-link text-danger p-0" style="font-size: 1.2rem;">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <?= $listing['posted_days_ago'] ?> days ago
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted" style="font-size: 1.1rem;">No listings available.</p>
        <?php endif; ?>
    </div>
</div>


    <?php
    include_once 'footer.php';
    ?>
