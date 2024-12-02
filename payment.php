<?php
require_once 'global.php';
include_once 'header.php';

$boostType = $_POST['boost_type'];
$planId = $_POST['plan_id'];
$price = $_POST['price'];
$planName = $_POST['plan_name'];

$paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
$businessEmail = $fun->getSiteSettingValue('paypal_email_address');

?>

<form id="paypalForm" action="<?php echo $paypalUrl; ?>" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="<?php echo $businessEmail; ?>">
    <input type="hidden" name="item_name" value="<?php echo $planName; ?>">
    <input type="hidden" name="item_number" value="<?php echo $planId; ?>">
    <input type="hidden" name="amount" value="<?php echo $price; ?>">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="proid" value="<?php echo $_POST['proid']; ?>">
    <input type="hidden" name="return" value="success.php?plan_id=<?php echo $planId; ?>&amount=<?php echo $price; ?>&proid=<?php echo $_POST['proid']; ?>">
    <input type="hidden" name="cancel_return" value="cancel.php">
</form>
<script>

document.getElementById('paypalForm').submit();
</script>
