<?php
require_once('../../../global.php');

// 1) Check the viewer's role
$sessionRole = $_SESSION['arole'] ?? 0;
$isAdmin     = in_array($sessionRole, [1,3]);

$draw   = intval($_POST['draw']);
$start  = intval($_POST['start']);
$length = intval($_POST['length']);

$totalRecords = $fun->getTotalBannerCount();

$searchName   = $_POST['name']   ?? '';
$searchStatus = $_POST['status'] ?? '';
$conditions   = [];

if (!empty($searchName)) {
    $conditions[] = "title LIKE '%" . $searchName . "%'";
}
if ($searchStatus === '0' || $searchStatus === '1') {
    $conditions[] = "status = '" . $searchStatus . "'";
}

$where       = !empty($conditions) ? implode(' AND ', $conditions) : '';
$bannerData  = $fun->getAllBanner($start, $length, $where);

if (empty($bannerData)) {
    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $totalRecords,
        'recordsFiltered' => 0,
        'data'            => []
    ]);
    exit;
}

$data = [];
foreach ($bannerData as $banner) {
    // Map status for display
    if ($banner['is_active'] == 1) {
        $stats     = 'process';
        $statsText = 'Active';
    } else {
        $stats     = 'denied';
        $statsText = 'Denied';
    }

    // If user is Admin/Super Admin, show delete/edit. Otherwise, omit.
    $actionsHtml = '';
    if ($isAdmin) {
        $actionsHtml = '
          <div class="table-data-feature">
              <a href="'.$urlval.'admin/banner/edit.php?bannerid='.$security->encrypt($banner['id']).'" 
                 class="item" data-toggle="tooltip" data-placement="top" title="Edit banner">
                  <i class="fa fa-pencil-square-o"></i>
              </a>
              <button class="item btn-danger delete-banner" data-id='.$security->encrypt($banner['id']).'
                      data-toggle="tooltip" data-placement="top" title="Delete banner">
                  <i class="fa fa-trash"></i>
              </button>
          </div>';
    } else {
        // Moderator => read-only => maybe just an icon or blank
        $actionsHtml = '<span style="color:gray;">Read-only</span>';
    }

    $data[] = [
        'checkbox' => '<label class="au-checkbox"><input type="checkbox"><span class="au-checkmark"></span></label>',
        'name'     => htmlspecialchars($banner['title']),
        'date'     => htmlspecialchars($banner['updated_at']),
        'image'    => '<img style="width:44%; border-radius:50%; border:1px solid black;"
                          src="' .$urlval.htmlspecialchars($banner['image']). '"/>',
        'status'   => '<span class="status--'.$stats.'">'.$statsText.'</span>',
        'show'     => htmlspecialchars($banner['placement']),
        'get_code' => '<button class="btn btn-primary get-code" data-id="' . base64_encode($banner['id']) . '"
                          data-toggle="tooltip" data-placement="top" title="Get banner code">
                          Get code
                       </button>',
        'actions'  => $actionsHtml
    ];
}

$response = [
    'draw'            => $draw,
    'recordsTotal'    => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data'            => $data
];

echo json_encode($response);
?>