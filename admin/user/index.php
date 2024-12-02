<?php
require_once("../../global.php");
include_once('../header.php');
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
                                        <input type="text" class="form-control" id="name" placeholder="Enter name">
                                    </div>
                
                                    <div class="form-group col-md-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email">
                                    </div>
                        
                                    <div class="form-group col-md-3">
                                        <label for="role">Role</label>
                                        <select class="form-control" id="role">
                                            <option value="" selected>All Roles</option>
                                            <option value="1">Admin</option>
                                            <option value="0">User</option>
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
                                    <button type="button" class="btn btn-success" id="searchUsers">Search</button>
                                </div>

                            </form>
                            </div>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div id="deleteConfirmModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this user?
      </div>



<?php
include_once('../footer.php');
?>
<script>
$(document).ready(function() {
    
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false, 
        "ajax": {
            "url": "<?php echo $urlval ?>admin/ajax/user/fetchUsers.php",
            "type": "POST",
            "data": function(d) {
                d.name = $('#name').val();  
                d.email = $('#email').val(); 
                d.role = $('#role').val();   
                d.status = $('#status').val(); 
            }
        },
        "columns": [
            {"data": "checkbox"}, 
            {"data": "name"}, 
            {"data": "email"},
            {"data": "role"},
            {"data": "type"}, 
            {"data": "chat"}, 
            {"data": "actions"}
        ]
    });


    $('#searchUsers').on('click', function() {
        table.draw();
    });

    $('#userTable').on('click', '.btn-danger', function() {
        var userId = $(this).data('id'); 
            $.ajax({
                url: '<?php echo $urlval ?>admin/ajax/user/deleteUser.php', 
                type: 'POST',
                data: { id: userId },
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
    
    });

    $(document).on('change', '.user-status-select', function() {
        var userId = $(this).data('id');
        var status = $(this).val();

        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/update_status.php',
            type: 'POST',
            data: {
                id: userId,
                status: status
            },
            success: function(response) {
                alert('User status updated successfully!');
            },
            error: function(xhr, status, error) {
                // Handle error
                alert('An error occurred while updating status.');
            }
        });
    });
    $('#userTable').on('click', '.create-chat-btn', function() {
        var chatId = $(this).data('chatid');
        
        $.ajax({
            url: '<?php echo $urlval ?>admin/ajax/user/create_chat.php',
            type: 'POST',
            data: {
                chatId: chatId
            },
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