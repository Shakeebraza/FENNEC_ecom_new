<?php
require_once("../../global.php");
include_once("../header.php");

// Only allow authorized admins (roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$csrfError = '';
$message = '';

// Process deletion if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
    } else {
        // Delete closed classifieds (assuming closed classifieds have is_enable = 0)
        $stmt = $pdo->prepare("DELETE FROM products WHERE is_enable = 0");
        if ($stmt->execute()) {
            $deletedCount = $stmt->rowCount();
            $message = "Deleted {$deletedCount} closed classified" . ($deletedCount == 1 ? "" : "s") . " successfully.";
        } else {
            $csrfError = "Failed to delete closed classifieds.";
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Retrieve count of closed classifieds
$stmt = $pdo->prepare("SELECT COUNT(*) AS closed_count FROM products WHERE is_enable = 0");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$closedCount = $row ? (int)$row['closed_count'] : 0;
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-white">
            <h3 class="mb-0">Delete Closed Classifieds</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($csrfError)): ?>
                <div class="alert alert-danger" id="alert-message"><?= htmlspecialchars($csrfError) ?></div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-success" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <p>
                There are <strong><?= $closedCount ?></strong> closed classified<?= ($closedCount == 1 ? "" : "s") ?>.
                Do you want to delete all closed classifieds? This action is non-reversible.
            </p>
            <?php if ($closedCount > 0): ?>
                <form method="post" onsubmit="return confirm('Are you sure you want to delete all closed classifieds? This action cannot be undone.');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-danger">Delete Closed Classifieds</button>
                </form>
            <?php else: ?>
                <p class="text-muted">There are no closed classifieds to delete.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once("../footer.php"); ?>
