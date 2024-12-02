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
                                        
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                    </div>
                                </div>
                                <div  class="table-responsive table-responsive-data2">
                                    <table id="userTable" class="table table-data2">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Duration</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Action</th>
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



<?php
include_once('../footer.php');
?>
<script>
$(document).ready(function () {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/packages/fetchpac.php",
            "type": "POST"
        },
        "columns": [
            {"data": "checkboost"},
            {"data": "name"},
            {"data": "heading"},
            {"data": "duration"},
            {"data": "price"},
            {"data": "status"},
            {"data": "actions"}
        ],
        "order": [[3, "desc"]] // Sort by created_at date
    });
});


</script>

</body>

</html>