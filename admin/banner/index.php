<?php
require_once("../../global.php");
include_once('../header.php');
?>

<div class="page-container">

<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="title-5 m-b-35">Banners Table</h3>
                    <form id="userSearchForm">
                        <div class="form-row searchfromwhite">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
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
                            <button type="button" class="btn btn-success" id="searchMenu" style="height: 37px;margin-top: 30px;">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>date</th>
                                    <th>Image</th>
                                    <th>status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
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
            "url": "<?php echo $urlval; ?>admin/ajax/banner/fetchbanner.php",
            "type": "POST",
            "data": function(d) {
                d.name = $('#name').val();  
                d.status = $('#status').val();  
            }
        },
        "columns": [
            {"data": "checkbox"},
            {"data": "name"},
            {"data": "date"},
            {"data": "image"},
            {"data": "status"},
            {"data": "actions"}
        ],
    });

    $('#searchMenu').on('click', function() {
        table.draw();
    });

    $(document).on('click', '.delete-banner', function() {
        var bannerId = $(this).data('id');
        var confirmDelete = confirm('Are you sure you want to delete this banner?');

        if (confirmDelete) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/banner/deletebanner.php',
                type: 'POST',
                data: { id: bannerId },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('Banner deleted successfully');
                        location.reload();
                    } else {
                        alert('Failed to delete banner');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Error occurred. Please try again.');
                }
            });
        }
    });
});



</script>

</body>

</html>