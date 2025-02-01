<?php
require_once("../../global.php");
include_once('../header.php');

/** Check roles in [1,3,4] **/
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]);

?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Box Table</h3>
                        <div class="table-data__tool">
                            <div class="table-data__tool-left">
                                <div class="rs-select2--light rs-select2--md">
                                    <select class="js-select2" name="property">
                                        <option selected="selected">All Properties</option>
                                        <option value="">Option 1</option>
                                        <option value="">Option 2</option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                                <div class="rs-select2--light rs-select2--sm">
                                    <select class="js-select2" name="time">
                                        <option selected="selected">Today</option>
                                        <option value="">3 Days</option>
                                        <option value="">1 Week</option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                            <div class="table-data__tool-right">
                                <!-- If there's an "Add Box" button, show only if isAdmin -->
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Heading</th>
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
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/box/fetchbox.php",
            "type": "POST"
        },
        "columns": [{
                "data": "checkbox"
            },
            {
                "data": "name"
            },
            {
                "data": "heading"
            },
            {
                "data": "date"
            },
            {
                "data": "status"
            },

            // Only define "actions" if isAdmin
            <?php if ($isAdmin): ?> {
                "data": "actions"
            }
            <?php endif; ?>
        ]
    });

    <?php if ($isAdmin): ?>
    // Delete button logic
    $('#userTable').on('click', '.btn-danger', function() {
        var boxId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user (box entry)?')) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/box/deletebox.php',
                type: 'POST',
                data: {
                    id: boxId
                },
                dataType: 'json',
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
        }
    });
    <?php endif; ?>
});
</script>

</body>

</html>