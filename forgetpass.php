<?php
require_once 'global.php';
include_once 'header.php';
?>
<style>
    /* Overall page background with a subtle gradient */
    body {
        /* background: linear-gradient(135deg, #f5f7fa, #c3cfe2); */
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }
    /* Realistic card design: clean white card with a subtle border and shadow */
    .card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }
    .card-body {
        padding: 2rem;
    }
    .card-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 600;
        font-size: 1.75rem;
        text-align: center;
        margin-bottom: 1rem;
    }
    .form-label {
        font-weight: 500;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #28a745;
    }
    /* Button styling */
    .btn-success {
        background-color: #28a745;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .btn-warning {
        background-color: #ffc107;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-warning:hover {
        background-color: #e0a800;
    }
    /* Styling for the response message */
    #responseMessage {
        font-weight: 500;
    }
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
                            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Reset Password</button>
                            <a class="btn btn-warning text-center text-decoration-none" href="<?= $urlval ?>index.php">Cancel</a>
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

    // Store the reset button element for easy access.
    const $resetButton = $(this).find('.btn-success');
    
    // Disable the button and set loading spinner.
    $resetButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

    const email = $('#email').val().trim();

    $.ajax({
        type: 'POST',
        url: '<?= $urlval ?>ajax/send_reset_email.php',
        data: { email: email },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            $('#responseMessage')
                .text(response.message)
                .css('color', response.success ? 'green' : 'red');
        },
        error: function(xhr, status, error) {
            const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? 
                                 xhr.responseJSON.message : 
                                 'An error occurred. Please try again.';
            $('#responseMessage')
                .text(errorMessage)
                .css('color', 'red');
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
