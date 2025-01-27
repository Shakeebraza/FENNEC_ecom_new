<?php
require_once("../../global.php");
include_once('../header.php');

/**
 * 1) Confirm user’s role is in [1,3,4].
 *    If not, redirect or show an error.
 */
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

// 2) Check if user is Admin or Super Admin
$isAdmin = in_array($role, [1,3]);
?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Menus Table</h3>

                        <!-- If you had an "Add Menu" button, show it only if isAdmin -->
                        <!-- 
                        <?php if ($isAdmin): ?>
                            <a href="<?php echo $urlval; ?>admin/menu/add.php"
                               class="btn btn-primary mb-3">
                                Add New Menu
                            </a>
                        <?php endif; ?>
                        -->

                        <!-- Search Form -->
                        <form id="userSearchForm">
                            <div class="form-row searchfromwhite">
                                <div class="form-group col-md-3">
                                    <label for="name">Menu Name</label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status">
                                        <option value="" selected>All Statuses</option>
                                        <option value="1">Activated</option>
                                        <option value="0">Unactivated</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-success"
                                        id="searchMenu"
                                        style="height: 37px; margin-top: 30px;">
                                    Search
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive table-responsive-data2">
                            <table id="userTable" class="table table-data2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu name</th>
                                        <th>date</th>
                                        <th>status</th>
                                        <!-- Show "Action" column only if isAdmin -->
                                        <?php if ($isAdmin): ?>
                                            <th>Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div><!-- table-responsive -->
                    </div><!-- col-md-12 -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/menu/fetchmenu.php",
            "type": "POST",
            "data": function(d) {
                d.name = $('#name').val();  
                d.status = $('#status').val();  
            }
        },
        "columns": [
            { "data": "checkbox" },
            { "data": "name" },
            { "data": "date" },
            { "data": "status" },
            // Only define "actions" column if isAdmin
            <?php if ($isAdmin): ?>
            { "data": "actions" }
            <?php endif; ?>
        ]
    });

    // Trigger table reload on "Search" button click
    $('#searchMenu').on('click', function() {
        table.draw();
    });

    <?php if ($isAdmin): ?>
    // Example: If there's a "Delete" button or similar, handle it
    // (In your "fetchmenu.php" you might have an 'actions' column containing a delete button.)
    $('#userTable').on('click', '.btn-danger', function() {
        var menuId = $(this).data('id');
        if (confirm('Are you sure you want to delete this menu?')) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/menu/deletemenu.php',
                type: 'POST',
                data: { id: menuId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Menu deleted successfully!');
                        table.ajax.reload();
                    } else {
                        alert('Error deleting menu: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the menu.');
                }
            });
        }
    });
    <?php endif; ?>
});
</script>

</body>
</html>
