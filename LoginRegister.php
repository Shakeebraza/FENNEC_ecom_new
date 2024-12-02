<?php
require_once 'global.php';
include_once 'header.php';
include_once 'google-login.php';

$setSession = $fun->isSessionSet();

if ($setSession == true) {
    $redirectUrl = $urlval . 'index.php'; 
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>'; 
    exit();
}

?>
<div
  class="container-fluid bg-light min-vh-100 d-flex justify-content-center align-items-center">
  <div class="row w-100 justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow border">
        <div class="card-body">
          <ul
            class="nav nav-tabs justify-content-center"
            id="loginTabs"
            role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link active"
                id="login-tab"
                data-bs-toggle="tab"
                data-bs-target="#login"
                type="button"
                role="tab"
                aria-controls="login"
                aria-selected="true">
                <?=$lan['login']?>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="register-tab"
                data-bs-toggle="tab"
                data-bs-target="#register"
                type="button"
                role="tab"
                aria-controls="register"
                aria-selected="false">
                <?=$lan['REGISTER']?>
              </button>
            </li>
          </ul>
          <div class="tab-content mt-3" id="loginTabsContent">
            <div
              class="tab-pane fade show active"
              id="login"
              role="tabpanel"
              aria-labelledby="login-tab">
              
              <form id="loginForm" data-url="<?php echo $urlval ?>ajax/login.php">
                <div id="alert-message" class="alert alert-danger alert-dismissible fade d-none" role="alert">
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  <span id="message-content"></span>
                </div>
                <div class="mb-3 text-center">
                <a class="btn btn-outline-dark w-100" href="<?php echo $client->createAuthUrl(); ?>">
                    <img src="https://www.google.com/favicon.ico" alt="Google icon" class="me-2" style="width: 20px; height: 20px" />
                    <?= $lan['sign_in_with_google']?>
                </a>
                </div>
                <div class="mb-3 text-center">
                  <!-- <button class="btn btn-primary w-100">
                    <i class="bi bi-facebook me-2"></i>
                    Sign in with Facebook
                  </button> -->
                </div>
                <div class="text-center mb-3">
                  <hr class="divider" />
                  <span class="text-muted">OR</span>
                  <hr class="divider" />
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label"><?= $lan['email']?></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required />
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label"><?= $lan['password']?></label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required />
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()"><?= $lan['show']?></button>
                  </div>
                  <div class="text-end mt-1">
                    <a href="<?= $urlval?>forgetpass.php" class="text-decoration-none"><?= $lan['Forgot_your_pass']?></a>
                  </div>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-success"><?= $lan['login']?></button>
                </div>
                <p class="text-muted text-center mt-3 small">
                  <?= $lan['login_privacy_policy']?>
                </p>
              </form>

              <div class="card mt-3 shadow">
                <div class="card-body">
                  <h5 class="card-title">Welcome to Fennec.</h5>
                  <p class="card-text">Sign in or Register to:</p>
                  <ul class="list-unstyled">
                    <li>
                      <i class="bi bi-chat-dots me-2"></i> Send and receive messages
                    </li>
                    <li>
                      <i class="bi bi-pencil-square me-2"></i> Post and manage your
                      ads
                    </li>
                    <li><i class="bi bi-star me-2"></i> Rate other users</li>
                    <li>
                      <i class="bi bi-heart me-2"></i> Favourite ads to check them
                      out later
                    </li>
                    <li>
                      <i class="bi bi-bell me-2"></i> Set alerts for your searches
                      and never miss a new ad in your area
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div
              class="tab-pane fade"
              id="register"
              role="tabpanel"
              aria-labelledby="register-tab">
              <h5 class="card-title text-center">Welcome to Fennec.</h5>
              <p class="card-text text-center">Sign in or Register to:</p>
              <ul class="list-unstyled custom-list">
                <li><i class="fas fa-envelope me-2"></i> Send and receive messages</li>
                <li><i class="fas fa-ad me-2"></i> Post and manage your ads</li>
                <li><i class="fas fa-star me-2"></i> Rate other users</li>
                <li><i class="fas fa-heart me-2"></i> Favourite ads to check them out later</li>
                <li><i class="fas fa-bell me-2"></i> Set alerts for your searches and never miss a new ad in your area</li>
              </ul>
              <div id="alert-message2" class="alert alert-danger d-none" role="alert">
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  <span id="message-content"></span>
                </div>
              <div class="mb-3 text-center">
                <a class="btn btn-outline-dark w-100" href="<?php echo $client->createAuthUrl(); ?>">
                      <img src="https://www.google.com/favicon.ico" alt="Google icon" class="me-2" style="width: 20px; height: 20px" />
                      <?= $lan['sign_in_with_google']?>
                  </a>
              </div>
              <div class="mb-3 text-center">
                <!-- <button class="btn btn-primary w-100">
                  <i class="bi bi-facebook me-2"></i>
                  Sign in with Facebook
                </button> -->
              </div>
              <div class="text-center mb-3">
                <hr class="divider" />
                <span class="text-muted">OR</span>
                <hr class="divider" />
              </div>

              <form id="registerForm" action="ajax/register.php" method="post">
                <div class="mb-3">
                  <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                </div>

                <div class="mb-3">
                  <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                  <div class="input-group">
                    <input type="password" class="form-control password" name="password" id="registerPassword" placeholder="Choose a strong password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword2()">Show</button>
                  </div>
                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="marketingEmails" name="marketingEmails">
                  <label class="form-check-label" for="marketingEmails">
                    We will send you emails regarding our services, offers, competitions, and carefully selected partners. You can unsubscribe at any time.
                  </label>
                </div>
                <button type="submit" class="btn btn-success w-100 mb-3">Register</button>
                <p class="small text-muted">
                  By selecting Register you agree you've read and accepted our Terms of Use. Please see our Privacy Notice for information regarding the processing of your data.
                </p>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<?php
include_once 'footer.php';
?>
<script>
  function togglePassword() {
    const passwordField = document.getElementById("registerPassword");
    passwordField.type = passwordField.type === "password" ? "text" : "password";
  }

  $(document).on('submit', '#registerForm', function(e) {
  e.preventDefault();

  $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        $('#alert-message2')
          .removeClass('d-none alert-danger')
          .addClass('alert-success')
          .find('#message-content').text('Registration successful!');
        
        setTimeout(function() {
          window.location.href = 'index.php';
        }, 2000);
      } else {
     
        $('#alert-message2')
          .removeClass('d-none alert-success')
          .addClass('alert-danger')
          .find('#message-content').text(response.errors || 'An error occurred. Please try again.');
      }
    },
    error: function() {
      $('#alert-message2')
        .removeClass('d-none alert-success')
        .addClass('alert-danger')
        .find('#message-content').text('An unexpected error occurred. Please try again.');
    }
  });
});

  $(document).ready(function() {
  $('#loginForm').on('submit', function(event) {
    event.preventDefault();

    let url = $(this).data('url');

    $.ajax({
      url: url,
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        $('#alert-message').removeClass('d-none fade alert-danger alert-success');
        
        if (response.status === 'success') {
          $('#alert-message').addClass('alert-success show');
          $('#message-content').text('Login successful! Redirecting...');
          setTimeout(function() {
            window.location.href =  '<?= $urlval?>index.php';
          }, 2000);
        } else {
          $('#alert-message').addClass('alert-danger show');
          $('#message-content').text(response.message);
        }
      },
      error: function() {
        $('#alert-message').removeClass('d-none fade').addClass('alert-danger show');
        $('#message-content').text('An unexpected error occurred.');
      }
    });
  });
});

function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = event.target;

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.textContent = "<?= 'hide' ?>"; 
    } else {
        passwordField.type = 'password';
        toggleButton.textContent = "<?= $lan['show'] ?>"; 
    }
}
function togglePassword2() {
    const passwordField = document.getElementById('registerPassword');
    const toggleButton = event.target;

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.textContent = "<?= 'hide' ?>"; 
    } else {
        passwordField.type = 'password';
        toggleButton.textContent = "<?= $lan['show'] ?>"; 
    }
}
</script>
</body>

</html>


