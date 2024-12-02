<?php
require_once('../../../global.php');

$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);


$totalRecords = $fun->getTotalBannerCount();

$searchName = $_POST['name'] ?? '';
$searchStatus = $_POST['status'] ?? '';
$conditions = [];

if (!empty($searchName)) {
    $conditions[] = "title LIKE '%" . $searchName . "%'";
}
if ($searchStatus == 0 || $searchStatus == 1) {
    $conditions[] = "status = '" . $searchStatus. "'";
}
$where = !empty($conditions) ? implode(' AND ', $conditions) : '';

$bannerData = $fun->getAllBanner($start, $length,$where);


if (empty($bannerData)) {
    echo json_encode(['draw' => $draw, 'recordsTotal' => $totalRecords, 'recordsFiltered' => 0, 'data' => []]);
    exit;
}

$data = [];
foreach ($bannerData as $index => $banner) {
    if($banner['status'] == 1){
        $stats='process';
        $statsTest='Active';
    }else{
        $stats='denied';
        $statsTest='Denied';

    }
    $data[] = [
        'checkbox' => '<label class="au-checkbox"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name' => htmlspecialchars($banner['title']),
        'date' => htmlspecialchars($banner['updated_at']),
        'image' => '<img style="width:44%; border-radius: 50%;border: 1px solid black;" src="' .$urlval.htmlspecialchars($banner['image_url']).'"/>',
        'status' => '<span class="status--'.$stats.'">'.$statsTest.'</span>',
        'actions' => '
    <div class="table-data-feature">
        <a href="'.$urlval.'admin/banner/edit.php?bannerid='.$security->encrypt($banner['id']).'" class="item" data-toggle="tooltip" data-placement="top" title="Edit banner">
             <i class="fa fa-pencil-square-o"></i>
        </a>
            <button class="item btn-danger delete-banner" data-id='.$security->encrypt($banner['id']).' data-toggle="tooltip" data-placement="top" title="Delete banner">
                <i class="fa fa-trash"></i> 
            </button>
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
