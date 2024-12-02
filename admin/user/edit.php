<?php
require_once("../../global.php");
include_once('../header.php');

// Fetch the user details based on the provided user ID
$userId = isset($_GET['id']) ? base64_decode($_GET['id']) : '';
if (!$userId) {
    die("Invalid User ID.");
}

$user = $dbFunctions->getDatanotenc('users', "id = '$userId'");
if (!$user) {
    die("User not found.");
}
?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Edit User</strong>
                            </div>
                            <div class="card-body card-block">
                                <form id="editUserForm">
                                    <input type="hidden" name="id" value="<?php echo base64_encode($user[0]['id']); ?>">

                                    <div class="form-group">
                                        <label for="username">Name</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user[0]['username']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user[0]['email']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="1" <?php echo $user[0]['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
                                            <option value="0" <?php echo $user[0]['role'] == 0 ? 'selected' : ''; ?>>User</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?php echo $user[0]['status'] == 1 ? 'selected' : ''; ?>>Activated</option>
                                            <option value="0" <?php echo $use[0]['status'] == 0 ? 'selected' : ''; ?>>Blocked</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password (leave blank to keep current)">
                                    </div>

                                    <div class="form-group">
                                        <label for="premium">Premium User</label>
                                        <select class="form-control" id="premium" name="premium">
                                            <option value="1" <?php echo $user[0]['premium'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                            <option value="0" <?php echo $user[0]['premium'] == 0 ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>

                                    <button type="button" id="updateUserBtn" class="btn btn-primary">Update User</button>
                                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    $('#updateUserBtn').click(function() {
        var formData = $('#editUserForm').serialize();

        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/updateUser.php',
            type: 'POST',
            data: formData,
            dataType: 'json', 
            success: function(response) {
                if (response.success) {
                    
                    alert('User updated successfully!');
                    window.location.href = 'index.php';
                } else {
                    
                    alert('Error updating user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                
                console.log('Error Response:', xhr.responseText);

              
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    alert('An error occurred: ' + errorResponse.message);
                } catch (e) {
                    alert('An error occurred while updating the user.');
                }
            }
        });
    });
});

</script>
</body>
</html>
