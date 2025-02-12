<?php
require_once("../global.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('header.php');

// Check roles in [1,3,4]
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]);

// Ensure a transaction ID is provided and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-4'><p>Invalid transaction ID.</p></div>";
    include_once('footer.php');
    exit;
}

$transaction_id = (int)$_GET['id'];
$error = '';
$success = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate input values
    $amount = trim($_POST['amount'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!is_numeric($amount) || $amount <= 0) {
        $error = "Please enter a valid positive amount.";
    }

    // If no validation errors, update the transaction
    if (empty($error)) {
        try {
            $sql = "UPDATE transactions 
                    SET amount = ?, description = ?
                    WHERE transaction_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$amount, $description, $transaction_id]);

            if ($stmt->rowCount() > 0) {
                $success = "Transaction updated successfully.";
            } else {
                $error = "No changes made or transaction not found.";
            }
        } catch (PDOException $e) {
            error_log("Update transaction error: " . $e->getMessage());
            $error = "A database error occurred. Please try again.";
        }
    }
}

// Retrieve the current transaction details (join with users for username)
try {
    $sql = "SELECT t.transaction_id, t.amount, t.description, t.transaction_date, u.username
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.transaction_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        echo "<div class='container py-4'><p>Transaction not found.</p></div>";
        include_once('footer.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Fetch transaction error: " . $e->getMessage());
    echo "<div class='container py-4'><p>Database error occurred.</p></div>";
    include_once('footer.php');
    exit;
}
?>

<div class="container mt-5">
    <h2>Edit Transaction</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <!-- Display transaction ID (read-only) -->
        <div class="form-group">
            <label>Transaction ID:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($transaction['transaction_id']) ?>" readonly>
        </div>

        <!-- Display username (read-only) -->
        <div class="form-group">
            <label>Username:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($transaction['username']) ?>" readonly>
        </div>

        <!-- Editable Amount -->
        <div class="form-group">
            <label>Amount:</label>
            <div class="input-group">
                <span class="input-group-text"><?= $fun->getFieldData('site_currency'); ?></span>
                <input type="number" name="amount" step="0.01" class="form-control" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
            </div>
        </div>

        <!-- Editable Description -->
        <div class="form-group">
            <label>Description:</label>
            <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($transaction['description']) ?>">
        </div>

        <!-- (Optional) Display Transaction Date (read-only) -->
        <div class="form-group">
            <label>Transaction Date:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($transaction['transaction_date']) ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Transaction</button>
    </form>
</div>

<?php include_once('footer.php'); ?>
