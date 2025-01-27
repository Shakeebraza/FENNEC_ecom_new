<?php
require_once("../../global.php");
include_once('../header.php');

/** Check roles in [1,3,4] **/
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
                        <h3 class="title-5 m-b-35">Payment Table</h3>
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
                                <!-- You could place an "Add Payment" button here if needed, only for isAdmin -->
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th> 
                                    <th>Transaction ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Product</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <!-- If you had an actions column for deleting, you'd show it only if $isAdmin -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div><!-- table-responsive -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('../footer.php'); ?>

<script>
$(document).ready(function () {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/packages/payment.php",
            "type": "POST"
        },
        "columns": [
            { data: 'checkPayment', orderable: false },
            { data: 'txn_id' },
            { data: 'username' },
            { data: 'email' },
            { data: 'product' },
            { data: 'plan' },
            { data: 'amount' },
            { data: 'created_at' },
            { data: 'status' }
            // If you want an actions column, only define it if isAdmin
        ],
        "order": [[3, "desc"]] // Example sort
    });

    // If there's a delete button or other admin action, you'd do:
    <?php if ($isAdmin): ?>
    /*
    $('#userTable').on('click', '.deletePayment', function() {
        var paymentId = $(this).data('id');
        // ...
    });
    */
    <?php endif; ?>
});
</script>

</body>
</html>
