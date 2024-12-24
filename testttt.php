<?php
// Include database connection file
include('dbcon/Database.php');

// Create Database instance
$db = new Database();
$conn = $db->getConnection();

// Initialize messages
$success_message = '';
$error_message = '';

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = htmlspecialchars($_POST['name']);
        $review = htmlspecialchars($_POST['review']);
        $rating = (int)$_POST['rating']; // Star rating input

        $query = "INSERT INTO reviews (name, review, rating) VALUES (:name, :review, :rating)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':review', $review);
        $stmt->bindParam(':rating', $rating);

        if ($stmt->execute()) {
            $success_message = "Thank you for your review!";
        } else {
            $error_message = "Failed to submit your review. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}

// Updated query to fetch star ratings
$query = "SELECT * FROM reviews ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch reviews from the database
try {
    $query = "SELECT * FROM reviews ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Failed to fetch reviews: " . $e->getMessage();
    $reviews = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
</head>
<body>

<div class="container">
    <div class="review-container mx-auto">
        <h2>Client Reviews</h2>

        <!-- Success or Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Review Submission Form -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="review" class="form-label">Your Review</label>
                <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Your Rating</label>
                <select class="form-select" id="rating" name="rating" required>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>

            <button type="submit" class="btn btn-custom">Submit Review</button>
        </form>

        <!-- List of Reviews -->
        <?php foreach ($reviews as $review): ?>
    <div class="review-item">
        <h5 style="color:#000;">
            <?php echo htmlspecialchars($review['name']); ?>
        </h5>
        <p style="color:#000;">
            <?php echo htmlspecialchars($review['review']); ?>
        </p>
        <p>
            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                <span style="color: #157347;">&#9733;</span> <!-- Filled star -->
            <?php endfor; ?>
            <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                <span style="color: #ccc;">&#9733;</span> <!-- Empty star -->
            <?php endfor; ?>
        </p>
        <small class="text-muted">
            Reviewed on: <?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?>
        </small>
    </div>
<?php endforeach; ?>

    </div>

   
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
