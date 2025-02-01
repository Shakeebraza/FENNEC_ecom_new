<?php
require_once('../../../global.php');

// 1) Check viewer role from session
$sessionRole = $_SESSION['role'] ?? 0;
$isAdmin     = in_array($sessionRole, [1,3]);

$draw   = intval($_POST['draw']);
$start  = intval($_POST['start']);
$length = intval($_POST['length']);

$totalRecords = $fun->getTotalSubCatCount();
$catsData = $fun->getAllSubcat($start, $length);

if (empty($catsData)) {
    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $totalRecords,
        'recordsFiltered' => 0,
        'data'            => []
    ]);
    exit;
}

$data = [];
foreach ($catsData as $cat) {
    // fetch parent cat name
    $finddata = $fun->findAllPerentcat($cat['category_id']);
    $categoryName = isset($finddata[0]['category_name']) ? $finddata[0]['category_name'] : 'Unknown';

    // subcat is_enable
    $stats     = ($cat['is_enable'] == 1) ? 'process' : 'denied';
    $statsText = ($cat['is_enable'] == 1) ? 'Active' : 'Denied';

    // If user is admin => show delete/edit. If not => read-only
    $actionsHtml = '';
    if ($isAdmin) {
        $deleteButton = '
            <button class="item btn-danger" data-id="' . $security->encrypt($cat['id']) . '" 
                    data-toggle="tooltip" data-placement="top" title="Delete">
                <i class="zmdi zmdi-delete"></i>
            </button>';

        $actionsHtml = '
            <div class="table-data-feature">
                <a href="' . $urlval . 'admin/subcategories/edit.php?catid=' . $security->encrypt($cat['id']) . '" 
                   class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                    <i class="zmdi zmdi-edit"></i>
                </a>
                ' . $deleteButton . '
            </div>';
    } else {
        // role=4 => moderator => read-only
        $actionsHtml = '<span style="color:gray;">Read-only</span>';
    }

    $data[] = [
        'checkbox' => '<label class="au-checkbox">
                         <input type="checkbox">
                         <span class="au-checkmark"></span>
                       </label>',
        'name'    => htmlspecialchars($cat['subcategory_name']),
        'catname' => htmlspecialchars($categoryName),
        'date'    => htmlspecialchars($cat['created_at']),
        'status'  => '<span class="status--' . $stats . '">' . $statsText . '</span>',
        'actions' => $actionsHtml
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
