<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);

// Fetch total record count
$totalRecords = $fun->boost_plansTotalCount();

// Fetch the paginated boost plans data
$boostData = $fun->getAllboost_plans($start, $length);

if (empty($boostData)) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => 0,
        'data' => []
    ]);
    exit;
}

$data = [];
foreach ($boostData as $boost) {
    $statusClass = ($boost['status'] === 'active') ? 'process' : 'denied';
    $statusText = ($boost['status'] === 'active') ? 'Active' : 'Denied';

    $data[] = [
        'checkboost' => '<label class="au-checkboost"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name' => htmlspecialchars($boost['name']),
        'heading' => '<span class="block-email">' . htmlspecialchars($boost['description']) . '</span>',
        'duration' => htmlspecialchars($boost['duration']) . ' days',
        'price' => '$' . number_format($boost['price'], 2),
        'status' => '<span class="status--' . $statusClass . '">' . $statusText . '</span>',
        'actions' => '
            <div class="table-data-feature">
                <a href="' . $urlval . 'admin/packages/edit.php?boostid=' . base64_encode($boost['id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
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
