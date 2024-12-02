<?php
require_once 'global.php';
include_once 'header.php';


?>
<style>

</style>
<div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Forgotten Password</h1>
                        <p class="text-center text-muted">Please enter your registered email address.</p>
                        <form id="forgotPasswordForm">
                            <div id="responseMessage" class="mt-3 text-center"></div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Reset Password</button>
                                <a class="btn btn-warning" href="<?= $urlval?>index.php" class="text-center text-decoration-none text-muted">Cancel</a>
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
$('#forgotPasswordForm').submit(function(event) {
    event.preventDefault(); 

    const email = $('#email').val();

    $.ajax({
        type: 'POST',
        url: '<?= $urlval?>ajax/send_reset_email.php',
        data: { email: email },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            $('#responseMessage').text(response.message);
            $('#responseMessage').css('color', response.success ? 'green' : 'red');
        },
        error: function(xhr, status, error) {
            let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? 
                               xhr.responseJSON.message : 
                               'An error occurred. Please try again.';
            $('#responseMessage').text(errorMessage);
            $('#responseMessage').css('color', 'red');
        }
    });
});

</script>

</body>
</html>