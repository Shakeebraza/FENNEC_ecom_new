<?php
require_once("../global.php");
include_once('header.php');

// Role check (only allow admins: roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

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
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        if (empty($username)) {
            $error = "Username is required!";
        } elseif (!is_numeric($amount) || $amount <= 0) {
            $error = "Please enter a valid positive amount!";
        } else {
            try {
                // Check if user exists in the users table
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $error = "User not found!";
                } else {
                    // Update wallet_balance and wallet_deposited fields
                    $sql = "UPDATE users 
                            SET wallet_balance = wallet_balance + ?, 
                                wallet_deposited = wallet_deposited + ?
                            WHERE username = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$amount, $amount, $username]);

                    if ($stmt->rowCount() > 0) {
                        // Record the transaction in the transactions table
                        $stmtTrans = $pdo->prepare("INSERT INTO transactions (user_id, amount, description, transaction_date) VALUES (?, ?, ?, NOW())");
                        $stmtTrans->execute([$user['id'], $amount, $description]);

                        $success = "Successfully added " . $fun->getFieldData('site_currency') . number_format($amount, 2) . " to $username's wallet!";
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

<div class="container mt-5">
    <div class="row justify-content-center">
         <div class="col-md-6">
             <div class="card shadow">
                 <div class="card-header bg-primary text-white">
                     <h4 class="mb-0">Add Transaction</h4>
                 </div>
                 <div class="card-body">
                     <?php if (!empty($error)): ?>
                         <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                     <?php elseif (!empty($success)): ?>
                         <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                     <?php endif; ?>

                     <form method="post" action="">
                         <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                         
                         <div class="form-group">
                             <label for="username">Username</label>
                             <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                         </div>
                         
                         <div class="form-group">
                             <label for="amount">Amount</label>
                             <div class="input-group">
                                 <span class="input-group-text"><?php echo $fun->getFieldData('site_currency'); ?></span>
                                 <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" step="0.01" min="0.01" required value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
                             </div>
                         </div>
                         
                         <div class="form-group">
                             <label for="description">Description</label>
                             <input type="text" name="description" id="description" class="form-control" placeholder="Enter description" value="<?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?>">
                         </div>
                         
                         <button type="submit" class="btn btn-primary btn-block">Submit</button>
                     </form>
                 </div>
             </div>
         </div>
    </div>
</div>

<?php include 'footer.php'; ?>
