<?php
require_once 'global.php';
include_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $user = $dbFunctions->getDatanotenc('users', "email = '$email' AND reset_token = '$token' AND reset_token_expiry >= NOW()");

    if (!$user) {
        echo '
        <script>
            alert("Invalid or expired token.");
            window.location.href = "' . $urlval . 'LoginRegister.php";
        </script>
    ';
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'];
    $email = $_POST['email'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < 8) {
        echo '
        <script>
            alert("Password must be at least 8 characters.");
        </script>
    ';
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo '
        <script>
            alert("Passwords do not match. Please try again.");
        </script>
    ';
        exit;
    }

    $user = $dbFunctions->getDatanotenc('users', "email = '$email' AND reset_token = '$token' AND reset_token_expiry >= NOW()");
    if (!$user) {
        echo '
        <script>
            alert("Invalid or expired token.");
            window.location.href = "' . $urlval . 'LoginRegister.php";
        </script>
    ';
        exit;
    }

    $userId = $user[0]['id'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $data = $dbFunctions->updateData('users', [
        'password' => $hashedPassword,
        'reset_token' => NULL,
        'reset_token_expiry' => NULL,
    ], $userId);

    echo '
        <script>
            alert("Your password has been successfully reset.");
            window.location.href = "' . $urlval . 'LoginRegister.php";
        </script>
    ';
    exit;
} else {
    echo '
        <script>
            alert("Invalid request.");
            window.location.href = "' . $urlval . 'LoginRegister.php";
        </script>
    ';
    exit;
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Reset Password</h1>
                    <form action="resetpassword.php" method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>
</body>
</html>
