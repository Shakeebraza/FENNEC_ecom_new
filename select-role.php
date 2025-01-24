<?php
// select-role.php
require_once 'global.php';
include_once 'header.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if temp_user is set in the session
if (!isset($_SESSION['temp_user'])) {
    header("Location: login.php"); // Redirect to login if no temp data
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['temp_user']['email'];
    $name = $_SESSION['temp_user']['name'];
    $role = isset($_POST['role']) ? intval($_POST['role']) : 0; // Default to Private

    // Validate role
    if (!in_array($role, [0, 2])) {
        $error = "Invalid role selected.";
    } else {
        // Check if user already exists (double-check)
        $where = "email = '" . $email . "'";
        $existingUser = $dbFunctions->getDatanotenc('users', $where);

        if ($existingUser) {
            $error = "User already exists. Please <a href='login.php'>login</a>.";
        } else {
            // Create new user with selected role
            $data = [
                'email' => $email,
                'username' => $name,
                'password' => password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'role' => $role,
                'verification_token' => 0,
                'email_verified_at' => date('Y-m-d H:i:s')
            ];
            $dbFunctions->setData('users', $data);
            $fun->sessionSet($email);
            unset($_SESSION['temp_user']);
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta httpequiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Role - Fennec</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
        }
        .role-option {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .role-option:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Select Your Role</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label">Choose your role:</label>
                                <div class="d-flex justify-content-around mt-2">
                                    <div class="form-check text-center role-option">
                                        <input class="form-check-input" type="radio" name="role" id="privateRole" value="0" required>
                                        <label class="form-check-label" for="privateRole">
                                            <strong>Private</strong><br>
                                            <small>Personal use and individual transactions.</small>
                                        </label>
                                    </div>
                                    <div class="form-check text-center role-option">
                                        <input class="form-check-input" type="radio" name="role" id="traderRole" value="2" required>
                                        <label class="form-check-label" for="traderRole">
                                            <strong>Trader</strong><br>
                                            <small>Business use with advanced features.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Continue</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <a href="logout.php" class="text-decoration-none">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper (for alerts and components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Custom JS -->
</body>
</html>
