<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);

// Fetch total record count
$totalRecords = $fun->getTotalPayment();

// Fetch the paginated payment data
$paymentDatas = $fun->getPaymentData($start, $length);

if (empty($paymentDatas)) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => 0,
        'data' => []
    ]);
    exit;
}

$data = [];
foreach ($paymentDatas as $paymentData) {
    // Determine status display
    $statusClass = ($paymentData['status'] === 'completed') ? 'success' : (($paymentData['status'] === 'pending') ? 'warning' : 'denied');
    $statusText = ucfirst($paymentData['status']);

    $data[] = [
        'checkPayment' => '<label class="au-checkpayment"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'txn_id' => htmlspecialchars($paymentData['txn_id']),
        'username' => htmlspecialchars($paymentData['username']),
        'email' => '<span class="block-email">' . htmlspecialchars($paymentData['email']) . '</span>',
        'product' => htmlspecialchars($paymentData['product_name']),
        'plan' => htmlspecialchars($paymentData['plan_name']),
        'amount' => '$' . number_format($paymentData['amount'], 2),
        'created_at' => date('Y-m-d', strtotime($paymentData['created_at'])),
        'status' => '<span class="status--' . $statusClass . '">' . $statusText . '</span>',
        'actions' => '
            <div class="table-data-feature">
                <a href="' . $urlval . 'admin/packages/edit.php?paymentDataid=' . base64_encode($paymentData['id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                    <i class="zmdi zmdi-edit"></i>
                </a>
            </div>'
    ];
}

$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data' => $data
];

echo json_encode($response);
?>
