<?php
require_once("../../global.php");
include_once('../header.php');

// 1) Only let [1,3,4] in => else redirect
$role = $_SESSION['role'] ?? 0;
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

                        <h3 class="title-5 m-b-35">Categories Table</h3>
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
                                <!-- Only show "add categories" if user can edit -->
                                <?php if ($isAdmin): ?>
                                    <a href="<?= $urlval ?>admin/categories/addcat.php" 
                                       class="au-btn au-btn-icon au-btn--green au-btn--small">
                                       <i class="zmdi zmdi-plus"></i>add Categories
                                    </a>
                                    <a href="<?= $urlval ?>admin/categories/sort.php" 
                                       class="au-btn au-btn-icon btn-dark au-btn--small" style="color:white;">
                                       <i class="zmdi zmdi-sort"></i>Sort
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category name</th>
                                    <th>date</th>
                                    <th>status</th>
                                    <th>Show Home</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables fill -->
                            </tbody>
                        </table>
                    </div><!-- table-responsive -->

                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    // Determine if user can edit
    var canEdit = <?php echo $isAdmin ? 'true' : 'false'; ?>;

    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/categories/fetchcat.php",
            "type": "POST"
        },
        "columns": [
            { "data": "checkbox" },
            { "data": "name" },
            { "data": "date" },
            { "data": "status" },
            { "data": "showhome" },
            { "data": "actions" }
        ]
    });

    // If canEdit is false => skip delete & "show-home" logic
    if (canEdit) {
        // DELETE
        $('#userTable').on('click', '.btn-danger', function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: '<?php echo $urlval; ?>admin/ajax/categories/deletecat.php',
                    type: 'POST',
                    data: { id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Category deleted successfully!');
                            table.ajax.reload();
                        } else {
                            alert('Error deleting category: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the category.');
                    }
                });
            }
        });

        // Show-home toggle
        $('#userTable').on('change', '.show-home-toggle', function() {
            var catId   = $(this).data('id');
            var isChecked = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/categories/update_showhome.php',
                type: 'POST',
                data: { id: catId, is_show: isChecked },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Show home status updated successfully!');
                    } else {
                        alert('Error updating status: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the show home status.');
                }
            });
        });
    }
});
</script>
</body>
</html>
