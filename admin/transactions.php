<?php
require_once("../global.php");
include_once('header.php');

/** Check roles in [1,3,4] **/
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]);

// (Optional) Pre-populate the user filter with distinct usernames from transactions
$userOptions = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT u.username 
                         FROM transactions t 
                         LEFT JOIN users u ON t.user_id = u.id 
                         WHERE u.username IS NOT NULL");
    $userOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("User filter error: " . $e->getMessage());
}
?>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <!-- Title & Filter Tools -->
                    <div class="col-md-12">
                        <h3 class="title-5 m-b-35">Transaction Details</h3>
                        <div class="table-data__tool">
                            <div class="table-data__tool-left">
                                <!-- User Filter -->
                                <div class="rs-select2--light rs-select2--md">
                                    <select class="js-select2" name="user_filter" id="user_filter">
                                        <option value="">All Users</option>
                                        <?php foreach ($userOptions as $user): ?>
                                            <option value="<?php echo htmlspecialchars($user['username']); ?>">
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                                <!-- Time Filter -->
                                <div class="rs-select2--light rs-select2--sm">
                                    <select class="js-select2" name="time_filter" id="time_filter">
                                        <option value="today" selected="selected">Today</option>
                                        <option value="3days">Last 3 Days</option>
                                        <option value="1week">Last 1 Week</option>
                                        <option value="1month">Last 1 Month</option>
                                        <option value="">All Time</option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                                <!-- Add more filters here as needed -->
                            </div>
                            <div class="table-data__tool-right">
                                <?php if ($isAdmin): ?>
                                    <a href="<?php echo $urlval; ?>admin/addtransaction.php" class="au-btn au-btn-icon au-btn--green au-btn--small">
                                        <i class="zmdi zmdi-plus"></i> Add Transaction
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- DataTable -->
                    <div class="col-md-12">
                        <div class="table-responsive table-responsive-data2">
                            <table id="transactionTable" class="table table-data2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Transaction ID</th>
                                        <th>Username</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate rows via AJAX -->
                                </tbody>
                            </table>
                        </div><!-- table-responsive -->
                    </div><!-- col-md-12 -->
                </div><!-- row -->
            </div><!-- container-fluid -->
        </div><!-- section__content -->
    </div><!-- main-content -->
</div><!-- page-container -->

<?php include_once('footer.php'); ?>

<!-- DataTables Script Initialization -->
<script>
$(document).ready(function() {
    var table = $('#transactionTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/transactions.php",
            "type": "POST",
            "data": function ( d ) {
                // Append filter data to the request
                d.user_filter = $('#user_filter').val();
                d.time_filter = $('#time_filter').val();
                // Add more filters here if necessary
            }
        },
        "columns": [
            { data: 'DT_RowIndex', orderable: false }, // Row index (if provided by your server script)
            { data: 'transaction_id' },
            { data: 'username' },
            { data: 'amount' },
            { data: 'description' },
            { data: 'transaction_date' }
        ],
        "order": [
            [5, "desc"]
        ] // Order by Transaction Date descending
    });

    // Redraw the table when any filter changes
    $('#user_filter, #time_filter').change(function(){
        table.draw();
    });
});
</script>
