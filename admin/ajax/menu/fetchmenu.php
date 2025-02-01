<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);


$totalRecords = $fun->getTotalMenuCount();

$searchName = $_POST['name'] ?? '';
$searchStatus = $_POST['status'] ?? '';
$conditions = [];

if (!empty($searchName)) {
    $conditions[] = "name LIKE '%" . $searchName . "%'";
}
if ($searchStatus == 0 || $searchStatus == 1) {
    $conditions[] = "is_enabled = '" . $searchStatus. "'";
}
$where = !empty($conditions) ? implode(' AND ', $conditions) : '';

$menuData = $fun->getAllMenu($start, $length,$where);


if (empty($menuData)) {
    echo json_encode(['draw' => $draw, 'recordsTotal' => $totalRecords, 'recordsFiltered' => 0, 'data' => []]);
    exit;
}


$data = [];
foreach ($menuData as $index => $menu) {
    if($menu['is_enabled'] == 1){
        $stats='process';
        $statsTest='Active';
    }else{
        $stats='denied';
        $statsTest='Denied';

    }
    $data[] = [
        'checkbox' => '<label class="au-checkbox"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name' => htmlspecialchars($menu['name']),
        'date' => htmlspecialchars($menu['updated_at']),
        'status' => '<span class="status--'.$stats.'">'.$statsTest.'</span>',
        'actions' => '
    <div class="table-data-feature">
        <a href="'.$urlval.'admin/menu/edit.php?menuid='.$security->encrypt($menu['id']).'" class="item" data-toggle="tooltip" data-placement="top" title="Edit Menu">
             <i class="fa fa-pencil-square-o"></i>
        </a>
        <a class="item btn-danger" href="'.$urlval.'admin/menu/add.php?menuid='.$security->encrypt($menu['id']).'" class="item" data-toggle="tooltip" data-placement="add" title="Add Sub Menu">
            <i class="fa fa-address-book"></i> 
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

// header('Content-Type: application/json');
echo json_encode($response);

?>
