<?php
require_once 'global.php';
include_once 'header.php';

// Validate GET parameters and token expiry for reset password
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Validate the token using your DB function (adjust the query as needed)
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
    // This block is used if you decide not to use AJAX.
    // Your reset logic would go here if processing POST directly.
    // Otherwise, process via AJAX endpoint.
}
?>

<!-- Reset Password Page Design -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Reset Password</h1>
                    <p class="text-center text-muted">Enter your new password below.</p>
                    <!-- Response message area -->
                    <div id="responseMessage" class="mt-3 text-center"></div>
                    <form id="resetPasswordForm">
                        <!-- Hidden token passed along with the form -->
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                            <small class="form-text text-muted">Password must be at least 8 characters.</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" id="resetBtn">Reset Password</button>
                            <a class="btn btn-warning text-center text-decoration-none" href="<?= $urlval ?>LoginRegister.php">Cancel</a>
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

<script>
// When the reset password form is submitted:
$('#resetPasswordForm').submit(function(event) {
    event.preventDefault(); 

    // Retrieve the reset button element
    const $resetButton = $(this).find('.btn-success');
    
    // Get form values
    const token = $('input[name="token"]').val();
    const email = $('input[name="email"]').val();
    const password = $('#password').val().trim();
    const confirmPassword = $('#confirm_password').val().trim();

    // Client-side validations
    if (password.length < 8) {
        $('#responseMessage').text("Password must be at least 8 characters.").css('color', 'red');
        return;
    }
    if (password !== confirmPassword) {
        $('#responseMessage').text("Passwords do not match.").css('color', 'red');
        return;
    }
    
    // Disable the button and show a loading spinner
    $resetButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting...');

    // Send AJAX request to the reset password endpoint (adjust URL as needed)
    $.ajax({
        type: 'POST',
        url: '<?= $urlval ?>ajax/reset_password.php',
        data: {
            token: token,
            email: email,
            password: password,
            confirm_password: confirmPassword
        },
        dataType: 'json',
        success: function(response) {
            $('#responseMessage').text(response.message).css('color', response.success ? 'green' : 'red');
            // Optionally, redirect if successful
            if(response.success) {
                setTimeout(function() {
                    window.location.href = "<?= $urlval ?>LoginRegister.php";
                }, 2000);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? 
                                 xhr.responseJSON.message : 
                                 'An error occurred. Please try again.';
            $('#responseMessage').text(errorMessage).css('color', 'red');
        },
        complete: function() {
            // Re-enable the button and reset the text after the request completes.
            $resetButton.prop('disabled', false).text('Reset Password');
        }
    });
});
</script>

</body>
</html>
