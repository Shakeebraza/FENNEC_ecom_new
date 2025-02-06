<?php
// cleanup/index.php
require_once("../../global.php");
include_once("../header.php");

// Make sure only authorized admins can access
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// Initialize message variable
$message = '';

// Process cleanup actions if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Invalid CSRF token!";
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'cleanup_files') {
            // Include your file cleanup script (make sure cleanup_files.php exists and works)
            include 'cleanup_files.php';
            $message = "Unused files cleanup completed.";
        } elseif ($action === 'cleanup_classifieds') {
            // Include your classifieds cleanup script (make sure cleanup_classifieds.php exists and works)
            include 'cleanup_classifieds.php';
            $message = "Expired classifieds cleanup completed.";
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mt-5">
    <div class="card shadow border-danger">
        <div class="card-header bg-danger text-white">
            <h3 class="mb-0">Cleanup Actions (Non-Reversible)</h3>
        </div>
        <div class="card-body">
            <p class="mb-4">
                <strong>Warning:</strong> These actions are non-reversible. Once data is removed, it cannot be retrieved.
            </p>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('Are you sure you want to proceed with the cleanup? This action is non-reversible.');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="submit" name="action" value="cleanup_files" class="btn btn-danger btn-block">
                            Cleanup Unused Files
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="action" value="cleanup_classifieds" class="btn btn-danger btn-block">
                            Cleanup Expired Classifieds
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once("../footer.php"); ?>
