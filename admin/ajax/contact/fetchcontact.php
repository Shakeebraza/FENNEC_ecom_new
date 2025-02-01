<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);


$totalRecords = $fun->getTotalContactCount();

$lanData = $fun->getAllContact($start, $length);


if (empty($lanData)) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => 0,
        'data' => []
    ]);
    exit;
}

$data = [];
foreach ($lanData as $index => $lan) {
   
    $path = htmlspecialchars($lan['message']);


    $deleteButton = ''; 
    if ($lan['id']) { 
        $deleteButton = '<button class="item btn-danger" data-id="' . base64_encode($lan['id']) . '" data-toggle="tooltip" data-placement="top" title="Delete">
            <i class="zmdi zmdi-delete"></i>
        </button>';
    }


    $data[] = [
        'checkbox' => '<label class="au-checkbox"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name' => htmlspecialchars($lan['name']),
        'Code' => htmlspecialchars($lan['email']),
        'Path' => $path,  
        'actions' => '
            <div class="table-data-feature">
                ' . $deleteButton . '
            </div>'
    ];
}

$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data' => $data
];

// Send the response as JSON
echo json_encode($response);
?>
