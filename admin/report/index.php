<?php
require_once("../../global.php");
include_once('../header.php');
?>
<style>
/* Add any custom styling here if required */
</style>
<div class="page-container">

    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-5">
                            <h3 class="title-5 mb-4">Report Table</h3>
                        </div>
                        <div class="table-responsive table-responsive-data2">
                            <table id="userTable" class="table table-data2">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Product Name</th>
                                        <th>Product Image</th>
                                        <th>Reason</th>
                                        <th>Additional Info</th>
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

<?php
include_once('popup.php');
include_once('../footer.php');
?>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/report/fetchreport.php",
            "type": "POST",
        },
        "columns": [
            { "data": "name" },
            { "data": "pname" },
            { 
                "data": "img",
                "render": function(data) {
                    // Corrected image source concatenation
                    return '<img src="' + '<?php echo $urlval; ?>'  + data + '" alt="Product Image" style="width: 80px; height: 70px;border-radius: 50%;">';
                }
            },
            { "data": "reason" },
            { "data": "info" }
        ],
    });
});
</script>

</body>
</html>
