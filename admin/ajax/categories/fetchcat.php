<?php
require_once('../../../global.php');

// 1) Check the viewer’s role
$sessionRole = $_SESSION['role'] ?? 0;
$isAdmin     = in_array($sessionRole, [1,3]);

$draw   = intval($_POST['draw']);
$start  = intval($_POST['start']);
$length = intval($_POST['length']);

$totalRecords = $fun->getTotalCatCount();
$catsData     = $fun->getAllcat($start, $length);

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
    $finddata = $fun->findAllsubcat($cat['id']);

    // Determine category status text
    if ($cat['is_enable'] == 1) {
        $stats     = 'process';
        $statsText = 'Active';
    } else {
        $stats     = 'denied';
        $statsText = 'Denied';
    }

    // We'll build the "Delete" button only if there's no subcats AND user can edit
    $deleteButton = '';
    if ($isAdmin && empty($finddata)) {
        $deleteButton = '
            <button class="item btn-danger" data-id="' . $security->encrypt($cat['id']) . '" 
                    data-toggle="tooltip" data-placement="top" title="Delete">
                <i class="zmdi zmdi-delete"></i>
            </button>';
    }

    // Build the "actions" HTML. If $isAdmin => show everything; 
    // if not => read-only
    $actionsHtml = '';
    if ($isAdmin) {
        $actionsHtml = '
            <div class="table-data-feature">
                <a href="' . $urlval . 'admin/categories/edit.php?catid=' . $security->encrypt($cat['id']) . '" 
                   class="item" data-toggle="tooltip" data-placement="top" title="Edit">
                    <i class="zmdi zmdi-edit"></i>
                </a>
                <a href="' . $urlval . 'admin/categories/addsubcat.php?catid=' . $security->encrypt($cat['id']) . '" 
                   class="item" data-toggle="tooltip" data-placement="top" title="Add Sub Categories">
                    <i class="zmdi zmdi-layers"></i>
                </a>
                ' . $deleteButton . '
            </div>';
    } else {
        // Moderator => read-only
        $actionsHtml = '<span style="color: gray;">Read-only</span>';
    }

    // "Show Home" toggle => only if $isAdmin. 
    // If not admin => show text or a disabled checkbox
    $showHomeHtml = '';
    if ($isAdmin) {
        $showHomeHtml = '
            <label class="switch switch-3d switch-success mr-3">
                <input type="checkbox" class="switch-input show-home-toggle" 
                       data-id="' . $security->encrypt($cat['id']) . '" 
                       ' . ($cat['is_show'] == 1 ? 'checked' : '') . '>
                <span class="switch-label"></span>
                <span class="switch-handle"></span>
            </label>';
    } else {
        // read-only view
        $showHomeHtml = ($cat['is_show'] == 1)
            ? '<span style="color:green;font-weight:bold;">Yes</span>'
            : '<span style="color:red;font-weight:bold;">No</span>';
    }

    $data[] = [
        'checkbox' => '<label class="au-checkbox">
                         <input type="checkbox">
                         <span class="au-checkmark"></span>
                       </label>',
        'name'    => htmlspecialchars($cat['category_name']),
        'date'    => htmlspecialchars($cat['created_at']),
        'status'  => '<span class="status--' . $stats . '">' . $statsText . '</span>',
        'showhome'=> $showHomeHtml,
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
