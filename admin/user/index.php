<?php
require_once("../../global.php");
include_once('../header.php');

/** 
 * Optional role check to restrict who sees the user list
 */
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="user-data m-b-30">
                            <h3 class="title-3 m-b-30">
                                <i class="zmdi zmdi-account-calendar"></i>User Data
                            </h3>
                            <div class="filters m-b-45">
                                <form id="userSearchForm">
                                    <div class="form-row searchfrom">
                                        <div class="form-group col-md-3">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control"
                                                   id="name" placeholder="Enter name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control"
                                                   id="email" placeholder="Enter email">
                                        </div>
                                        <!-- Updated role filter to match new roles -->
                                        <div class="form-group col-md-3">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role">
                                                <option value="" selected>All Roles</option>
                                                <option value="0">Regular User</option>
                                                <option value="1">Super Admin</option>
                                                <option value="2">Trader</option>
                                                <option value="3">Admin</option>
                                                <option value="4">Moderator</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status">
                                                <option value="" selected>All Statuses</option>
                                                <option value="1">Activated</option>
                                                <option value="0">Blocked</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-success"
                                                id="searchUsers"
                                                style="height: 38px; margin-top: 25px;">
                                            Search
                                        </button>
                                    </div>
                                </form>
                            </div><!-- filters -->

                            <div class="table-responsive table-data">
                                <table id="userTable" class="table display">
                                    <thead>
                                        <tr>
                                            <td>
                                                <label class="au-checkbox">
                                                    <input type="checkbox">
                                                    <span class="au-checkmark"></span>
                                                </label>
                                            </td>
                                            <td>Name</td>
                                            <td>Email</td>
                                            <td>Role</td>
                                            <td>Status</td>
                                            <td>Chat</td>
                                            <td>Actions</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Filled by DataTables -->
                                    </tbody>
                                </table>
                            </div><!-- table-responsive -->
                        </div><!-- user-data -->
                    </div><!-- col-lg-12 -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<!-- Optional: Delete confirm modal if you want a popup -->
<div id="deleteConfirmModal" class="modal fade" tabindex="-1" role="dialog"
     aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        Are you sure you want to delete this user?
      </div>
      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "pageLength": 10, // Set to 10 records per page
        "lengthMenu": [10, 25, 50, 100], // Allow users to select different page lengths
        "ajax": {
            "url": "<?php echo $urlval ?>admin/ajax/user/fetchUsers.php",
            "type": "POST",
            "data": function(d) {
                d.name   = $('#name').val();
                d.email  = $('#email').val();
                d.role   = $('#role').val();
                d.status = $('#status').val();
            },
            "error": function(xhr, error, thrown) {
                var errorMessage = 'An error occurred while processing your request.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                alert('Error: ' + errorMessage);
            }
        },
        "columns": [
            { "data": "checkbox", "orderable": false }, // Checkbox column
            { "data": "name" },     // Name
            { "data": "email" },    // Email
            { "data": "role" },     // Role
            { "data": "type" },     // Status
            { "data": "chat", "orderable": false },     // Chat
            { "data": "actions", "orderable": false }   // Actions
        ],
        "order": [[1, 'asc']], // Default ordering by Name
        "drawCallback": function(settings) {
            // Optional: Initialize any plugins or add event listeners after table draw
            $('.js-select2').select2({
                minimumResultsForSearch: -1,
                width: 'resolve'
            });
        }
    });

    // Trigger a fresh search
    $('#searchUsers').on('click', function() {
        table.draw();
    });

    // Delete user logic with modal confirmation
    var deleteUserId = null;

    $('#userTable').on('click', '.btn-danger', function() {
        deleteUserId = $(this).data('id');
        $('#deleteConfirmModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        if (!deleteUserId) return;

        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/deleteUser.php',
            type: 'POST',
            data: { id: deleteUserId },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully!');
                    table.ajax.reload();
                } else {
                    alert('Error deleting user: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the user.');
            }
        });

        // Reset the userId and hide the modal
        deleteUserId = null;
        $('#deleteConfirmModal').modal('hide');
    });

    // Update user status
    $(document).on('change', '.user-status-select', function() {
        var userId = $(this).data('id');
        var status = $(this).val();

        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/update_status.php',
            type: 'POST',
            data: { id: userId, status: status },
            success: function(response) {
                if (response.success) {
                    alert('User status updated successfully!');
                } else {
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while updating status.');
            }
        });
    });

    // Create chat logic
    $('#userTable').on('click', '.create-chat-btn', function() {
        var chatId = $(this).data('chatid');
        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/create_chat.php',
            type: 'POST',
            data: { chatId: chatId },
            success: function(response) {
                if (response.success) {
                    alert('Chat created successfully!');
                    table.ajax.reload();
                } else {
                    alert('Error creating chat: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while creating the chat.');
            }
        });
    });
});
</script>
</body>
</html>
