<?php
// select-role.php
require_once 'global.php';
include_once 'header.php';

if (!isset($_SESSION['temp_user'])) {
    header("Location: login.php"); // Redirect to login if no temp data
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['temp_user']['email'];
    $name = $_SESSION['temp_user']['name'];
    $role = isset($_POST['role']) ? intval($_POST['role']) : 0; // Default to Private

    // Validate role
    if (!in_array($role, [0, 2])) {
        $error = "Invalid role selected.";
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
?>

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
<?php include_once 'footer.php'; ?>
