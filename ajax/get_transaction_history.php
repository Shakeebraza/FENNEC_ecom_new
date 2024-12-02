<?php
require_once("../global.php");

$userid = base64_decode($_SESSION['userid']);
$transactions = $dbFunctions->getDatanotenc('payments', "user_id='$userid'");


if (count($transactions) > 0) {

    echo json_encode($transactions);
} else {

    echo json_encode(['message' => 'No transactions found']);
}
?>
