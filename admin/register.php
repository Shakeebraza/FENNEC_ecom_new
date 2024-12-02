<?php
require_once("../global.php");
if (isset($_COOKIE['remember_token'])) {
    $rememberTokenCookie = $fun->rememberTokenCheckByCookie($_COOKIE['remember_token']);
    
    if ($rememberTokenCookie === true) {
        header('Location: index.php');
        exit();
    }
}
$setSession = $fun->isSessionSet();

if ($setSession == true) {
    $redirectUrl = $urlval . 'admin/index.php'; 
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <title>Register</title>

    <link href="<?php echo $urlval?>admin/asset/css/font-face.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <link href="<?php echo $urlval?>admin/asset/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <link href="<?php echo $urlval?>admin/asset/css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                            <img src="<?php echo $urlval?>admin/asset/images/icon/logo2.png" alt="CoolAdmin">
                            <p>Fennec</p>
                            </a>
                        </div>
                        <div class="login-form">
                        <div id="alert-message" class="sufee-alert alert-dismissible fade show" style="display:none; position: fixed; top: 10px; right: 10px; z-index: 1000;">
                            <span class="badge"></span>
                            <span id="message-content"></span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <form id="registerForm" action="register.php" method="post">
                            <div class="form-group">
                                <label>Username</label>
                                <input class="au-input au-input--full" type="text" name="username" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="au-input au-input--full" type="email" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                            </div>
                            <div class="login-checkbox">
                                <label>
                                    <input type="checkbox" name="aggree">Agree the terms and policy
                                </label>
                            </div>
                            <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">register</button>
                        </form>
                            <div class="register-link">
                                <p>
                                    Already have account?
                                    <a href="<?php echo $urlval?>admin/login.php">Sign In</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="<?php echo $urlval?>admin/asset/vendor/jquery-3.2.1.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/slick/slick.min.js">
    </script>
    <script src="<?php echo $urlval?>admin/asset/vendor/wow/wow.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/animsition/animsition.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="<?php echo $urlval?>admin/asset/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="<?php echo $urlval?>admin/asset/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="<?php echo $urlval?>admin/asset/vendor/select2/select2.min.js">
    </script>

    <script src="<?php echo $urlval?>admin/asset/js/main.js"></script>
    <script>
$(document).ready(function() {
    $('#registerForm').on('submit', function(event) {
        event.preventDefault();
        
        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/register.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                var alertBox = $('#alert-message');
                var messageContent = $('#message-content');
                var badge = alertBox.find('.badge');

                if (response.status === 'success') {
                    badge.removeClass('badge-danger').addClass('badge-primary').text('Success');
                    messageContent.text(response.message);
                    alertBox.removeClass('alert-danger').addClass('alert-primary').fadeIn();
                } else if (response.status === 'error') {
                    badge.removeClass('badge-primary').addClass('badge-danger').text('Error');
                    if (Array.isArray(response.errors)) {
                        messageContent.html(response.errors.join('<br>'));
                    } else {
                        messageContent.text(response.message);
                    }
                    alertBox.removeClass('alert-primary').addClass('alert-danger').fadeIn();
                }

                setTimeout(function() {
                    alertBox.fadeOut(); 
                }, 3000);
            },
            error: function(jqXHR) {
                var alertBox = $('#alert-message');
                var messageContent = $('#message-content');
                var badge = alertBox.find('.badge');
                
                badge.removeClass('badge-primary').addClass('badge-danger').text('Error');
                
                if (jqXHR.status === 400) {
                    messageContent.text('Validation failed. Please check your input.');
                } else if (jqXHR.status === 500) {
                    messageContent.text('Internal Server Error. Please try again later.');
                } else {
                    messageContent.text('An unexpected error occurred.');
                }

                alertBox.removeClass('alert-primary').addClass('alert-danger').fadeIn();

                setTimeout(function() {
                    alertBox.fadeOut();
                }, 3000);
            }
        });
    });
});

</script>
</body>

</html>