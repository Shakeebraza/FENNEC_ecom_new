<?php
require_once("../../global.php");
include_once('../header.php');

// 1) Only let roles in [1,3,4] see this page
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

                        <h3 class="title-5 m-b-35">SubCategories Table</h3>
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
                                <!-- Show "add categories" only if isAdmin -->
                                <?php if ($isAdmin): ?>
                                <a href="<?= $urlval ?>admin/subcategories/add.php"
                                    class="au-btn au-btn-icon au-btn--green au-btn--small">
                                    <i class="zmdi zmdi-plus"></i>Add SubCategory
                                </a>
                                <a href="<?= $urlval ?>admin/categories/sort.php"
                                    class="au-btn au-btn-icon btn-dark au-btn--small" style="color:white;">
                                    <i class="zmdi zmdi-sort"></i>Sort
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div><!-- col-md-12 -->

                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sub Category name</th>
                                    <th>Category</th>
                                    <th>date</th>
                                    <th>status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will fill -->
                            </tbody>
                        </table>
                    </div><!-- .table-responsive -->

                </div><!-- .row -->
            </div><!-- .container-fluid -->
        </div><!-- .section__content -->
    </div><!-- .main-content -->
</div><!-- .page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    // figure out if user is admin or super admin
    var canEdit = <?php echo $isAdmin ? 'true' : 'false'; ?>;

    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/subcategories/fetchsubcat.php",
            "type": "POST"
        },
        "columns": [{
                "data": "checkbox"
            },
            {
                "data": "name"
            },
            {
                "data": "catname"
            },
            {
                "data": "date"
            },
            {
                "data": "status"
            },
            {
                "data": "actions"
            }
        ]
    });

    // If user is not admin => skip delete logic
    if (canEdit) {
        $('#userTable').on('click', '.btn-danger', function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this subcategory?')) {
                $.ajax({
                    url: '<?php echo $urlval; ?>admin/ajax/subcategories/deletecat.php',
                    type: 'POST',
                    data: {
                        id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Subcategory deleted successfully!');
                            table.ajax.reload();
                        } else {
                            alert('Error deleting subcategory: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the subcategory.');
                    }
                });
            }
        });
    }
});
</script>
</body>

</html>