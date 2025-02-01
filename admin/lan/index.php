<?php
require_once("../../global.php");
include_once('../header.php');

/**
 * Role check: only allow roles in [1,3,4].
 * If not in those roles, redirect to logout.
 */
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
// $isAdmin => Super Admin(1) or Admin(3)
$isAdmin = in_array($role, [1,3]);

?>
<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Languages Table</h3>
                        <div class="table-data__tool">
                            <div class="table-data__tool-right">
                                <!-- Show "Add Languages" button only if $isAdmin -->
                                <?php if ($isAdmin): ?>
                                <a href="<?= $urlval ?>admin/lan/add.php" class="au-btn au-btn-icon au-btn--small"
                                    style="background-color: #333; color: white;">
                                    <i class="zmdi zmdi-plus"></i> Add Languages
                                </a>
                                <?php endif; ?>

                                <!-- Download template is harmless, so we can show to everyone -->
                                <a href="<?= $urlval ?>languages/en.php" class="au-btn au-btn-icon au-btn--small"
                                    style="background-color: #28a745; color: white;" download>
                                    <i class="zmdi zmdi-download"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Language name</th>
                                    <th>Code</th>
                                    <th>Path</th>
                                    <!-- Show Action column only if $isAdmin -->
                                    <?php if ($isAdmin): ?>
                                    <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table rows populated by DataTables -->
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
            "url": "<?php echo $urlval; ?>admin/ajax/lan/fetchlan.php",
            "type": "POST"
        },
        "columns": [{
                "data": "checkbox"
            },
            {
                "data": "name"
            },
            {
                "data": "Code"
            },
            {
                "data": "Path"
            },

            // Only define "actions" column if isAdmin
            <?php if ($isAdmin): ?> {
                "data": "actions"
            }
            <?php endif; ?>
        ],
    });

    <?php if ($isAdmin): ?>
    // Delete language functionality (only if Admin or Super Admin)
    $('#userTable').on('click', '.btn-danger', function() {
        var lanId = $(this).data('id');
        if (confirm('Are you sure you want to delete this language?')) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/lan/deleteLan.php',
                type: 'POST',
                data: {
                    id: lanId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Language deleted successfully!');
                        table.ajax.reload();
                    } else {
                        alert('Error deleting language: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the language.');
                }
            });
        }
    });
    <?php endif; ?>
});
</script>

</body>

</html>