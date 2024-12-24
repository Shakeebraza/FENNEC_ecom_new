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

    $loggedInUserId = base64_decode($_SESSION['userid']) ?? null;

        $favoritedProductIds = [];

        if ($loggedInUserId) {

            $stmt_fav = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = :user_id");
            $stmt_fav->execute(['user_id' => $loggedInUserId]);
            $favoritedProductIds = $stmt_fav->fetchAll(PDO::FETCH_COLUMN, 0);
        }
     
    ?>

<div class="container py-4" style="max-width: 80%; margin: auto; font-family: Arial, sans-serif;">


    <div class="d-flex align-items-left justify-content-between mb-4" style="border-bottom: 1px solid #ccc; padding-bottom: 15px;">
        <div class="d-flex align-items-center gap-3">
   
            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm" 
                style="width: 75px; height: 75px; font-size: 1.5rem; font-weight: bold;">
                <?= $firstLetter ?>
            </div>
  
            <div>
                <h5 class="mb-1" style="font-weight: 600; color: #333;"><?= $fullName ?></h5>
                <div class="stars" style="display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                    <svg class="star" style="width: 10px; height: 10px; color: #64748b;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg class="star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span class="rating-count">(0)</span>
                </div>
                <small class="text-muted" style="font-size: 0.9rem;">Posting for <?= $postingDuration ?> years</small>
            </div>
        </div>

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

    <div class="mb-4" style="border-bottom: 2px solid #ddd;">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .review-container {
            background: linear-gradient(45deg, #00494F, #198754);
            color: white;
            border-radius: 10px;
            padding: 40px;
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #198754;
            color: white;
            border-radius: 5px;
        }
        .btn-custom:hover {
            background-color: #00494F;
            color: white;
        }
        .review-item {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }
    </style>

        <!-- Bootstrap Tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="listings-tab" data-bs-toggle="tab" href="#listings" role="tab" aria-controls="listings" aria-selected="true">Listings</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews</a>
        </li>
    </ul>
</div>

<div class="tab-content mt-4" id="myTabContent">

        <!-- Listings Tab Content -->
        <div class="tab-pane fade show active" id="listings" role="tabpanel" aria-labelledby="listings-tab">
        <h6 class="mb-4" style="color: #333; font-weight: bold;"><?= count($listings) ?> Items for Sale</h6>
        <div class="d-flex flex-column gap-3">
            <?php if (count($listings) > 0): ?>
                <?php foreach ($listings as $listing) {
        $isFavorited = in_array($listing['id'], $favoritedProductIds);
        ?>
        <a href="<?= $urlval?>detail.php?slug=<?= urlencode($listing['slug']) ?>" 
        class="text-decoration-none text-dark" style="border-radius: 8px; overflow: hidden; box-shadow: 0 0 12px 0 rgb(0 0 0 / 20%);">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="row g-0">
                    <div class="col-4" style="background: #FFFFFF;">
                        <div class="position-relative">
                            <img src="<?= $listing['image_url'] ?>" 
                                class="img-fluid rounded-start" 
                                alt="<?= $listing['title'] ?>" 
                                style="height: 250px !important; width: 345px !important; object-fit: cover;">
                            <span class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 rounded-1" 
                                style="font-size: 0.8rem;">
                                <i class="fas fa-camera"></i> <?= $listing['photo_count'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-8">
                        <style>
                            .icon_heart .fa-heart {
                                    color: #198754;
                            }
                        </style>
                        <div class="card-body d-flex justify-content-between align-items-start" style="padding: 45px 40px 0px 0px;">
                            <div>
                                <h6 class="card-title mb-1" style="color: #333; font-weight: bold;">
                                    <?= $listing['title'] ?>
                                </h6>
                                <p class="card-text mb-1" style="color: #888; font-size: 0.9rem;">
                                    <i class="fas fa-map-marker-alt me-1"></i> Location
                                </p>
                                <p class="card-text text-success" style="font-size: 1rem; font-weight: bold;">
                                    £<?= $listing['price'] ?>
                                </p>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <button 
                                    class="btn btn-link <?= $isFavorited ? 'text-danger' : 'text-secondary' ?> p-0 icon_heart" 
                                    style="font-size: 1.2rem;" 
                                    data-productid="<?= $listing['id'] ?>"
                                    onclick="event.stopPropagation();">
                                    <i class="<?= $isFavorited ? 'fas' : 'far' ?> fa-heart"></i>
                                </button>
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    <?= $listing['posted_days_ago'] ?> days ago
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <?php
    }
    ?>

            <?php else: ?>
                <p class="text-center text-muted" style="font-size: 1.1rem;">No listings available.</p>
            <?php endif; ?>
        </div>
        </div>

       <!-- Reviews Tab Content -->
       <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">

        </div>


</div>    
</div>


    <?php
    include_once 'footer.php';
    ?>
    <script>
        document.querySelectorAll('.icon_heart').forEach(favoriteButton => {
    favoriteButton.addEventListener('click', function(event) {
        event.preventDefault(); 
        const productId = this.getAttribute('data-productid');

        fetch('<?= $urlval ?>ajax/favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: productId
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = data.isFavorited ?
                    '<i class="fas fa-heart" style="color: red;"></i>' :
                    '<i class="far fa-heart" style="color: red;"></i>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    </body>

</html>
