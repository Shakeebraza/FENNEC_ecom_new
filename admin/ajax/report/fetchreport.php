<?php
require_once('../../../global.php');



$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);


$totalRecordsQuery = "SELECT COUNT(*) AS total FROM reports";
$totalRecordsStmt = $pdo->prepare($totalRecordsQuery);
$totalRecordsStmt->execute();
$totalRecords = $totalRecordsStmt->fetch(PDO::FETCH_ASSOC)['total'];


$query = "
    SELECT 
        r.id,
        u.username AS name,
        p.name AS pname,
        p.image AS img,
        r.reason,
        r.additional_info AS info
    FROM reports r
    INNER JOIN users u ON r.user_id = u.id
    INNER JOIN products p ON r.product_id = p.id
    LIMIT :start, :length
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':length', $length, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords, 
    "data" => $data
];

echo json_encode($response);
