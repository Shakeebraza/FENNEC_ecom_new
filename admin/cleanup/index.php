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

// Process cleanup actions if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'cleanup_files') {
        // Include or require your file cleanup script
        include 'cleanup_files.php';
        $message = "Unused files cleanup completed.";
    } elseif ($action === 'cleanup_classifieds') {
        include 'cleanup_classifieds.php';
        $message = "Expired classifieds cleanup completed.";
    }
}
?>

<div class="container mt-5">
    <h3>Cleanup Actions (Non-Reversible)</h3>
    <p>
        <strong>Warning:</strong> These actions are non-reversible. Once data is removed, it cannot be retrieved.
    </p>
    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post" onsubmit="return confirm('Are you sure you want to proceed with the cleanup? This action is non-reversible.');">
        <div class="mb-3">
            <button type="submit" name="action" value="cleanup_files" class="btn btn-danger">Cleanup Unused Files</button>
        </div>
        <div class="mb-3">
            <button type="submit" name="action" value="cleanup_classifieds" class="btn btn-danger">Cleanup Expired Classifieds</button>
        </div>
    </form>
</div>

<?php include_once("../footer.php"); ?>
