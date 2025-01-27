<?php
require_once("../../global.php");
include_once('../header.php');

// Restrict page to roles [1,3,4]
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]); // only role=1 or 3 can delete

?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Contact Table</h3>
                        <div class="table-data__tool">
                            <div class="table-data__tool-right">
                                <!-- If you wanted an "Add" or "Delete multiple" button, hide them if !isAdmin -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <!-- Show Action column only if Admin or Super Admin -->
                                    <?php if ($isAdmin): ?>
                                        <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will fill this via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
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
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/contact/fetchcontact.php",
            "type": "POST"
        },
        "columns": [
            { "data": "checkbox" },
            { "data": "name" },
            { "data": "Code" },
            { "data": "Path" },
            // Only define the actions column if $isAdmin
            <?php if ($isAdmin): ?>
            {
                "data": "actions"
            }
            <?php endif; ?>
        ],
    });

    <?php if ($isAdmin): ?>
    // Delete contact functionality (only if isAdmin)
    $('#userTable').on('click', '.btn-danger', function() {
        var contactId = $(this).data('id');
        if (confirm('Are you sure you want to delete this contact?')) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/contact/deletecon.php',
                type: 'POST',
                data: { id: contactId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Contact deleted successfully!');
                        table.ajax.reload();
                    } else {
                        alert('Error deleting contact: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the contact.');
                }
            });
        }
    });
    <?php endif; ?>
});
</script>
</body>
</html>
