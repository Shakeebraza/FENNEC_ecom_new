<?php
require_once("../../global.php");
include_once('../header.php');
?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid d-flex justify-content-center" style="min-height: 100vh;">
                <div class="row" style="width:80%;">
                <div id="alert-message" class="sufee-alert alert-dismissible fade show" style="display:none; width: 250px; position: fixed; top: 113px; right: 10px; z-index: 1000; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <span class="badge"></span>
                <span id="message-content" style="margin-right: 10px;"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <!-- <span aria-hidden="true">×</span> -->
                </button>
                </div>
                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">Add User</div>
                            <div class="card-body card-block">
                                <form id="addUserForm" method="post" class="">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <input type="text" id="username" name="username" placeholder="Username" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-envelope"></i>
                                            </div>
                                            <input type="email" id="email" name="email" placeholder="Email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-cog"></i>
                                            </div>
                                            <select name="role" id="select" class="form-control" required>
                                                <option value="" disabled selected>Please select</option>
                                                <option value="0">User</option>
                                                <option value="1">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                                                        <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-asterisk"></i>
                                            </div>
                                            <input type="password" id="password" name="password" placeholder="Password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-actions form-group">
                                        <button type="submit" class="btn btn-success btn-sm">Add User</button>
                                    </div>
                                </form>
                                <div id="responseMessage" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../footer.php');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#addUserForm').on('submit', function(event) {
        event.preventDefault(); 

        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/addUsers.php',
            type: 'POST',
            data: $(this).serialize(), 
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Response:', response);
                $('#message-content').text('');
                $('#alert-message').removeClass('alert-success alert-danger').hide();

                if (response.status === 'success') {
                    $('#message-content').text(response.message);
                    $('#alert-message').addClass('alert-success').fadeIn().delay(3000).fadeOut();
                } else {
                    $('#message-content').text(response.messages.join('\n'));
                    $('#alert-message').addClass('alert-danger').fadeIn().delay(3000).fadeOut();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#message-content').text('An error occurred: ' + error);
                $('#alert-message').addClass('alert-danger').fadeIn().delay(3000).fadeOut();
            }
        });
    });
});
</script>

</body>
</html>
