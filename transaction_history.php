<?php
require_once 'global.php';
include_once 'header.php';
?>




<style>
h2 {
        text-align: center;
        color: #00494f;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .filter-form {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .filter-form label {
        font-size: 1rem;
        color: #00494f;
    }

    .filter-form input {
        border-radius: 5px;
        padding: 8px;
        margin: 5px 0 15px 0;
        border: 1px solid #ccc;
        width: 100%;
    }

    .btn-primary {
        background-color: white;
        color: #00494f;
        border: 1px solid #00494f;
        padding: 10px 15px;
        border-radius: 5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background-color: #00494f;
        color: white;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
    }

    table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    table th {
        background-color: #00494f;
        color: white;
    }

    table tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .view-more-btn {
        display: block;
        margin-top: 20px;
        text-align: center;
    }

    .view-more-btn a {
        padding: 10px 20px;
        background-color: #00494f;
        color: #ffffff;
        border-radius: 5px;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .view-more-btn a:hover {
        background-color: white;
        color: #00494f;
        border: 1px solid #00494f;
    }
</style>

<div class="container mt-5">
    <h2>Transaction History</h2>
    <form method="POST" action="" class="filter-form">
        <div class="row">
            <div class="col-md-6">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required>
            </div>
            <div class="col-md-6">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" required>
            </div>
            <div class="col-md-6">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name" placeholder="Enter product name">
            </div>
            <div class="col-md-6">
                <label for="min_price">Price Range</label>
                <input type="number" name="priceRange" id="min_price" placeholder="Price Range" step="0.01">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div id="transactionHistory">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            $productName = isset($_POST['product_name']) ? $_POST['product_name'] : null;
            $priceRange = [
                'min_price' => isset($_POST['min_price']) ? $_POST['min_price'] : null,
                'max_price' => isset($_POST['max_price']) ? $_POST['max_price'] : null,
            ];

            $transactions = $fun->getUserTransactionsAndProducts($startDate, $endDate, $productName, $priceRange);

            if ($transactions) {
                echo '<table class="table table-bordered table-striped">';
                echo '<thead><tr><th>Transaction ID</th><th>Date</th><th>Amount</th><th>Description</th><th>Product Name</th><th>Price</th></tr></thead>';
                echo '<tbody>';
                foreach ($transactions as $transaction) {
                    echo '<tr>';
                    echo '<td>' . $transaction['payment_txn_id'] . '</td>';
                    echo '<td>' . $transaction['payment_created_at'] . '</td>';
                    echo '<td>' . $transaction['payment_amount'] . '</td>';
                    echo '<td>' . $transaction['product_description'] . '</td>';
                    echo '<td>' . $transaction['product_name'] . '</td>';
                    echo '<td>' . $transaction['product_price'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No transactions found for the selected filters.</p>';
            }
        }
        ?>
    </div>
</div>

<?php
include_once 'footer.php';
?>


</body>
</html>
