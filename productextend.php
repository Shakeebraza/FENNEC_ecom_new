<?php
require_once 'global.php';
include_once 'header.php';


$productId = isset($_GET['productid']) ? base64_decode($_GET['productid']) : (isset($_POST['productid']) ? $_POST['productid'] : null);
$price = $fun->getSiteSettingValue('product_extend');
$planName = isset($_GET['plan_name']) ? $_GET['plan_name'] : (isset($_POST['plan_name']) ? $_POST['plan_name'] : null);

if ($productId && $price && $planName) {

    
    echo '<form id="paypalForm" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="' . $fun->getSiteSettingValue('paypal_email_address') . '">
            <input type="hidden" name="item_name" value="' . htmlspecialchars($planName) . '">
            <input type="hidden" name="item_number" value="' . $productId . '">
            <input type="hidden" name="amount" value="' . $price . '">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="return" value="success2.php?plan_id=' . $productId . '&amount=' . $price . '">
            <input type="hidden" name="cancel_return" value="cancel.php">
        </form>';
} else {
    echo 'Invalid product data.';
}
?>
<script>
    document.getElementById('paypalForm').submit();
</script>
