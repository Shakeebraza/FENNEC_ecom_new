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

<div class="container">
    <h2>Select Your Role</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="role" class="form-label">Choose your role:</label>
            <select class="form-select" id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="0">Private</option>
                <option value="2">Trader</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Continue</button>
    </form>
</div>

<?php include_once 'footer.php'; ?>
