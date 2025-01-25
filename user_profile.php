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
            
            // For traders (role=2), these fields may exist:
            $companyName = isset($user_detail['company_name']) ? htmlspecialchars($user_detail['company_name']) : '';
            $urlLink    = isset($user_detail['url_link']) ? htmlspecialchars($user_detail['url_link']) : '';
        } else {
            $fullName = htmlspecialchars($user['username']);
            $location = 'N/A';
            $companyName = '';
            $urlLink    = '';
        }

        $profileLink     = "profile.php?username=" . urlencode($user['username']);
        $firstLetter     = strtoupper(substr($user['username'], 0, 1));
        $emailVerified   = !empty($user['email_verified_at']);
        $postingDuration = number_format($fun->calculatePostingDuration($user['created_at']), 1);
        $totalItems      = $fun->getTotalItems($user['id']);
        $categories      = $fun->getCategories($user['id']);
        $listings        = $fun->getListings($user['id']);

        // Fetch reviews
        $query       = "SELECT * FROM reviews WHERE userid = :id ORDER BY created_at DESC";
        $stmt        = $pdo->prepare($query);
        $stmt->execute(['id' => $user['id']]);
        $reviews     = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalStars  = 0;
        $totalReviews= count($reviews);
        foreach ($reviews as $review) {
            $totalStars += $review['rating'];
        }
        $averageRating = $totalReviews > 0 ? $totalStars / $totalReviews : 0;
        $averageRating = round($averageRating, 1);

    } else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}

$loggedInUserId = base64_decode($_SESSION['userid']) ?? null;
$favoritedProductIds = [];

if ($loggedInUserId) {
    $stmt_fav = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = :user_id");
    $stmt_fav->execute(['user_id' => $loggedInUserId]);
    $favoritedProductIds = $stmt_fav->fetchAll(PDO::FETCH_COLUMN, 0);
}

$success_message = '';
$error_message   = '';

// Handle Reviews submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name   = htmlspecialchars($_POST['name']);
        $review = htmlspecialchars($_POST['review']);
        $rating = (int)$_POST['rating'];
        $userid = (int)$_POST['useridreview']; 
        
        $query  = "INSERT INTO reviews (userid, name, review, rating) VALUES (:userid, :name, :review, :rating)";
        $stmt   = $pdo->prepare($query);
        
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':review', $review, PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $success_message = "Thank you for your review!";
        } else {
            $error_message = "Failed to submit your review. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>
<style>
.star-rating {
    display: flex;
    cursor: pointer;
}
.star {
    font-size: 30px;
    color: #d3d3d3;
    transition: color 0.2s ease !important;
}
.star.selected {
    color: gold;
}
.star:hover,
.star:hover~.star {
    color: gold;
}
</style>

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
                    <?php 
                    for ($i = 1; $i <= 5; $i++) {
                        $starColor = ($i <= $averageRating) ? 'gold' : '#64748b'; 
                        echo '<svg class="star" style="width: 10px; height: 10px; color: ' . $starColor . ';" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                              </svg>';
                    }
                    ?>
                    <span class="rating-count">(<?= $totalReviews ?>)</span>
                </div>
                <small class="text-muted" style="font-size: 0.9rem;">Posting for <?= $postingDuration ?> years</small>
            </div>
        </div>

        <div style="text-align: right;">
            <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem; color: #555;">
                <i class="fas fa-location-dot text-danger"></i>
                <span><?= $location ?></span>
            </div>
            <div class="d-flex align-items-center gap-2"
                 style="font-size: 0.9rem; <?= $emailVerified ? 'color: #28a745;' : 'color: #dc3545;' ?>">
                <i class="fas fa-check-circle"></i>
                <small><?= $emailVerified ? 'Email address verified' : 'Email not verified' ?></small>
            </div>
            
            <!-- If user is Trader (role=2), show company info -->
            <?php if ($user['role'] == 2) : ?>
                <?php if (!empty($companyName)) : ?>
                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem; color: #555;">
                        <i class="fas fa-building"></i>
                        <span><?= $companyName ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($urlLink)) : ?>
                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem; color: #555;">
                        <i class="fas fa-link"></i>
                        <span><?= $urlLink ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

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
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="listings-tab" data-bs-toggle="tab" href="#listings" role="tab"
                   aria-controls="listings" aria-selected="true">Listings</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab"
                   aria-controls="reviews" aria-selected="false">Reviews</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="myTabContent">
            <div class="tab-pane fade show active" id="listings" role="tabpanel" aria-labelledby="listings-tab">
                <h6 class="mb-4" style="color: #333; font-weight: bold;"><?= count($listings) ?> Items for Sale</h6>
                <div class="d-flex flex-column gap-3">
                    <?php if (count($listings) > 0): ?>
                        <?php foreach ($listings as $listing) {
                            $isFavorited = in_array($listing['id'], $favoritedProductIds);
                        ?>
                            <a href="<?= $urlval ?>p/<?= urlencode($listing['slug']) ?>/<?= urlencode($listing['title']) ?>/<?= urlencode($listing['title']) ?>"
                               class="text-decoration-none text-dark"
                               style="border-radius: 8px; overflow: hidden; box-shadow: 0 0 12px 0 rgb(0 0 0 / 20%);">
                                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                    <div class="row g-0">
                                        <div class="col-4" style="background: #FFFFFF;">
                                            <div class="position-relative">
                                                <img src="<?= $listing['image_url'] ?>" class="img-fluid rounded-start"
                                                     alt="<?= $listing['title'] ?>"
                                                     style="height: 250px !important; width: 345px !important; object-fit: cover;">
                                                <span class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 rounded-1"
                                                      style="font-size: 0.8rem;">
                                                    <i class="fas fa-camera"></i> <?= $listing['photo_count'] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body d-flex justify-content-between align-items-start"
                                                 style="padding: 45px 40px 0px 0px;">
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
                                                        style="font-size: 1.2rem;" data-productid="<?= $listing['id'] ?>"
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
                        <?php } ?>
                    <?php else: ?>
                        <p class="text-center text-muted" style="font-size: 1.1rem;">No listings available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab"
                 style="background-color: white; padding: 40px;">
                <div class="review-container mx-auto"
                     style="background-color: white; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 30px; max-width: 800px;">
                    <h2 style="font-size: 2rem; font-weight: bold; color: #00494f; text-align: center;">Client Reviews</h2>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert"
                             style="margin-bottom: 20px;">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"
                             style="margin-bottom: 20px;">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label" style="font-weight: 600; color: #333;">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   style="border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        </div>
                        <input type="hidden" name="useridreview" value="<?= $user['id']?>">
                        <div class="mb-3">
                            <label for="review" class="form-label" style="font-weight: 600; color: #333;">Your Review</label>
                            <textarea class="form-control" id="review" name="review" rows="4" required
                                      style="border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label" style="font-weight: 600; color: #333;">Your Rating</label>
                            <div class="star-rating" id="rating" style="display: flex; gap: 5px;">
                                <span class="star" data-value="5">&#9733;</span>
                                <span class="star" data-value="4">&#9733;</span>
                                <span class="star" data-value="3">&#9733;</span>
                                <span class="star" data-value="2">&#9733;</span>
                                <span class="star" data-value="1">&#9733;</span>
                            </div>
                            <input type="hidden" id="rating-value" name="rating" required>
                        </div>

                        <button type="submit" class="btn" style="background-color: #00494f; color: white; border-radius: 5px; padding: 10px 20px; font-size: 1rem; transition: background-color 0.3s ease;">
                            Submit Review
                        </button>
                    </form>

                    <?php if (empty($reviews)): ?>
                        <div class="no-reviews" style="text-align: center; margin-top: 30px;">
                            <p style="font-size: 1.2rem; color: #555;">No reviews available for this product yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item" style="margin-top: 30px; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                <h5 style="font-size: 1.2rem; font-weight: bold; color: #00494f;">
                                    <?php echo htmlspecialchars($review['name']); ?>
                                </h5>
                                <p style="color: #555; font-size: 1rem;">
                                    <?php echo htmlspecialchars($review['review']); ?>
                                </p>
                                <p>
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                        <span style="color: #157347;">&#9733;</span>
                                    <?php endfor; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                        <span style="color: #ccc;">&#9733;</span>
                                    <?php endfor; ?>
                                </p>
                                <small class="text-muted" style="font-size: 0.9rem;">
                                    Reviewed on: <?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>

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
            body: JSON.stringify({ id: productId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = data.isFavorited
                    ? '<i class="fas fa-heart" style="color: red;"></i>'
                    : '<i class="far fa-heart" style="color: red;"></i>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating-value');

stars.forEach(star => {
    star.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        ratingInput.value = value;
        updateStars(value);
    });
});

function updateStars(value) {
    stars.forEach(star => {
        if (star.getAttribute('data-value') <= value) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
}
</script>
</body>
</html>
