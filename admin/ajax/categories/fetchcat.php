<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);


$totalRecords = $fun->getTotalCatCount();


$catsData = $fun->getAllcat($start, $length);



if (empty($catsData)) {
    echo json_encode(['draw' => $draw, 'recordsTotal' => $totalRecords, 'recordsFiltered' => 0, 'data' => []]);
    exit;
}


$data = [];
foreach ($catsData as $index => $cat) {
    $finddata = $fun->findAllsubcat($cat['id']);
    if ($cat['is_enable'] == 1) {
        $stats = 'process';
        $statsTest = 'Active';
    } else {
        $stats = 'denied';
        $statsTest = 'Denied';
    }


    $deleteButton = '';
    if (empty($finddata)) {

        $deleteButton = '<button class="item btn-danger" data-id="' . $security->encrypt($cat['id']) . '" data-toggle="tooltip" data-placement="top" title="Delete">
            <i class="zmdi zmdi-delete"></i>
        </button>';
    }

    $data[] = [
        'checkbox' => '<label class="au-checkbox"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name' => htmlspecialchars($cat['category_name']),
        'date' => htmlspecialchars($cat['created_at']),
        'showhome' => '
            <label class="switch switch-3d switch-success mr-3">
                <input type="checkbox" class="switch-input show-home-toggle" data-id="' . $security->encrypt($cat['id']) . '" ' . ($cat['is_show'] == 1 ? 'checked' : '') . '>
                <span class="switch-label"></span>
                <span class="switch-handle"></span>
            </label>
        ',
        'status' => '<span class="status--' . $stats . '">' . $statsTest . '</span>',
        'actions' => '
            <div class="table-data-feature">
                <a href="' . $urlval . 'admin/categories/edit.php?catid=' . $security->encrypt($cat['id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                    <i class="zmdi zmdi-edit"></i>
                </a>

                <a href="' . $urlval . 'admin/categories/addsubcat.php?catid=' . $security->encrypt($cat['id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Add Sub Categories">
                    <i class="zmdi zmdi-layers"></i>
                </a>
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

// header('Content-Type: application/json');
echo json_encode($response);

?>
