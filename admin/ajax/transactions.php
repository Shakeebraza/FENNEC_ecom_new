<?php
require_once('../../global.php');

// Retrieve DataTables parameters
$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Optional filters (if used in your DataTables initialization)
$user_filter = isset($_POST['user_filter']) ? trim($_POST['user_filter']) : '';
$time_filter = isset($_POST['time_filter']) ? trim($_POST['time_filter']) : '';

// Build WHERE clause based on filters
$where = [];
$params = [];

if (!empty($user_filter)) {
    $where[] = "u.username = :user_filter";
    $params[':user_filter'] = $user_filter;
}

if (!empty($time_filter)) {
    switch ($time_filter) {
        case 'today':
            $where[] = "DATE(t.transaction_date) = CURDATE()";
            break;
        case '3days':
            $where[] = "t.transaction_date >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
            break;
        case '1week':
            $where[] = "t.transaction_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case '1month':
            $where[] = "t.transaction_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        default:
            // No time filter if an unexpected value is passed
            break;
    }
}

$whereSQL = '';
if (count($where) > 0) {
    $whereSQL = " WHERE " . implode(" AND ", $where);
}

// Get total record count (with filters applied)
$sqlTotal = "SELECT COUNT(*) FROM transactions t LEFT JOIN users u ON t.user_id = u.id" . $whereSQL;
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($params);
$totalRecords = $stmtTotal->fetchColumn();

// Fetch paginated transaction data
$sql = "SELECT t.transaction_id, u.username, t.amount, t.description, t.transaction_date
        FROM transactions t
        LEFT JOIN users u ON t.user_id = u.id
        " . $whereSQL . "
        ORDER BY t.transaction_date DESC
        LIMIT :start, :length";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
$stmt->execute();

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach ($transactions as $transaction) {
    $data[] = [
        // Optional: you can add a DT_RowIndex here if needed (or leave it empty)
        'DT_RowIndex'      => '',
        'transaction_id'   => htmlspecialchars($transaction['transaction_id']),
        'username'         => htmlspecialchars($transaction['username']),
        'amount'           => '$' . number_format($transaction['amount'], 2),
        'description'      => htmlspecialchars($transaction['description']),
        'transaction_date' => date('Y-m-d', strtotime($transaction['transaction_date']))
    ];
}

// Prepare the JSON response in the format DataTables expects
$response = [
    'draw'            => $draw,
    'recordsTotal'    => intval($totalRecords),
    'recordsFiltered' => intval($totalRecords),
    'data'            => $data
];

// Ensure the response is JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
