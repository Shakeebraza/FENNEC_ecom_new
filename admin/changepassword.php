<?php
require_once("../global.php");
include_once("header.php");

// Role check (only allow admin roles; adjust as necessary)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3, 4])) {  // Modify these values as per your admin roles
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// Define log file path.
$logFile = __DIR__ . '/changepassword.log';

// Helper function to log messages.
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

$csrfError = '';
$error = '';
$successMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logMessage("Change password form submitted by admin ID " . ($_SESSION['auserid'] ?? 'unknown'));
    
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $csrfError = "Invalid CSRF token!";
        logMessage("CSRF token validation failed.");
    } else {
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword     = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = "All fields are required.";
            logMessage("Validation error: One or more fields are empty.");
        } elseif ($newPassword !== $confirmPassword) {
            $error = "New Password and Confirm New Password do not match.";
            logMessage("Validation error: New password and confirmation do not match.");
        } else {
            // Retrieve the current admin's record from the database.
            // Decode the admin ID (stored as base64) before converting to an integer.
            $adminId = intval(base64_decode($_SESSION['auserid'] ?? '0'));
            logMessage("Attempting password update for admin ID $adminId.");
            
            $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$adminId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $dbPassword = $row['password'];
                // Verify current password
                if (password_verify($currentPassword, $dbPassword)) {
                    // Hash the new password and update the database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmtUpdate = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    if ($stmtUpdate->execute([$hashedPassword, $adminId])) {
                        $successMessage = "Password changed successfully.";
                        logMessage("Password updated successfully for admin ID $adminId.");
                    } else {
                        $error = "Failed to update password. Please try again.";
                        logMessage("Database error: Failed to update password for admin ID $adminId.");
                    }
                } else {
                    $error = "Current password is incorrect.";
                    logMessage("Authentication error: Incorrect current password for admin ID $adminId.");
                }
            } else {
                $error = "Admin record not found.";
                logMessage("Error: Admin record not found for admin ID $adminId.");
            }
        }
    }
}

// Generate a new CSRF token for the form.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mt-5">
  <div class="row justify-content-center">
     <div class="col-md-6">
       <div class="card shadow">
         <div class="card-header bg-primary text-white">
           <h4 class="mb-0">Change Password</h4>
         </div>
         <div class="card-body">
           <?php if (!empty($csrfError)): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($csrfError); ?></div>
           <?php endif; ?>
           <?php if (!empty($error)): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
           <?php endif; ?>
           <?php if (!empty($successMessage)): ?>
             <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
           <?php endif; ?>

           <form method="post" action="">
             <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
             
             <div class="form-group">
               <label for="current_password">Current Password</label>
               <input type="password" name="current_password" id="current_password" class="form-control" required>
             </div>
             
             <div class="form-group">
               <label for="new_password">New Password</label>
               <input type="password" name="new_password" id="new_password" class="form-control" required>
             </div>
             
             <div class="form-group">
               <label for="confirm_password">Confirm New Password</label>
               <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
             </div>
             
             <button type="submit" class="btn btn-primary btn-block">Change Password</button>
           </form>
         </div>
       </div>
     </div>
  </div>
</div>

<?php include_once("footer.php"); ?>
