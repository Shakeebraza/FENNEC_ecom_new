<?php
require_once("../../global.php");
include_once('../header.php');

// 1) Only let [1,3,4] in => else redirect
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]); // for editing

?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
 
                        <h3 class="title-5 m-b-35">Pages Table</h3>

                        <!-- If you want to hide the form from moderators (role=4) -->
                        <?php if (!$isAdmin): ?>
                            <p>You have read-only access to pages.</p>
                        <?php else: ?>
                            <form id="userSearchForm">
                                <div class="form-row searchfromwhite">
                                    <div class="form-group col-md-3">
                                        <label for="name">Page Name</label>
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
                                            id="searchPage" style="height:37px; margin-top:30px;">
                                        Search
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="table-responsive table-responsive-data2">
                            <table id="userTable" class="table table-data2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Menu name</th>
                                        <th>date</th>
                                        <th>status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables fill -->
                                </tbody>
                            </table>
                        </div><!-- .table-responsive -->

                    </div><!-- .col-md-12 -->
                </div><!-- .row -->
            </div><!-- .container-fluid -->
        </div><!-- .section__content -->
    </div><!-- .main-content -->
</div><!-- .page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function() {
    // Figure out if user can edit
    var canEdit = <?php echo $isAdmin ? 'true' : 'false'; ?>;

    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/page/fetchpages.php",
            "type": "POST",
            "data": function(d) {
                d.name   = $('#name').val();
                d.status = $('#status').val();
            }
        },
        "columns": [
            {"data": "checkbox"},
            {"data": "name"},
            {"data": "date"},
            {"data": "status"},
            {"data": "actions"}
        ],
    });

    $('#searchPage').on('click', function() {
        table.draw();
    });

    // If user is not admin => skip delete logic
    if (canEdit) {
        $('#userTable').on('click', '.btn-danger', function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this page?')) {
                $.ajax({
                    url: '<?php echo $urlval; ?>admin/ajax/page/deletepage.php',
                    type: 'POST',
                    data: { id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Page deleted successfully!');
                            table.ajax.reload();
                        } else {
                            alert('Error deleting page: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the page.');
                    }
                });
            }
        });
    }
});
</script>
</body>
</html>
