<?php
// session_start();
require_once("../global.php");
include_once('header.php');

// Role check (only allow 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// Initialize variables
$error = '';
$success = '';

// Database connection check
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Database connection error");
}

// Form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
    } else {
        $username = trim($_POST['username'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        
        // Validation
        if (empty($username)) {
            $error = "Username is required!";
        } elseif (!is_numeric($amount) || $amount <= 0) {
            $error = "Please enter a valid positive amount!";
        } else {
            try {
                // Check if user exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if (!$user) {
                    $error = "User not found!";
                } else {
                    // Update wallet balance
                    $sql = "UPDATE users 
                            SET wallet_balance = wallet_balance + ?
                            WHERE username = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$amount, $username]);

                    if ($stmt->rowCount() > 0) {
                        $success = "Successfully added ₹" . number_format($amount, 2) . " to $username's wallet!";
                        
                        // Optional: Add transaction record to a transactions table
                        // $stmt = $pdo->prepare("INSERT INTO transactions (...) VALUES (...)");
                        // $stmt->execute([...]);
                    } else {
                        $error = "No changes made. Please verify the username and amount.";
                    }
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = "A database error occurred. Please try again.";
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!-- Main container -->
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Add Transaction</h2>
                
                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               required
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo  $fun->getFieldData('site_currency') ?></span>
                            <input type="number" 
                                   class="form-control" 
                                   id="amount" 
                                   name="amount" 
                                   step="0.01" 
                                   min="0.01" 
                                   required
                                   value="<?= isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Description</label>
                        <input type="text" 
                               class="form-control" 
                               id="description" 
                               name="description" 
                      
                               >
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
