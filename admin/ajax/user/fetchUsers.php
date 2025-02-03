<?php
require_once('../../../global.php');

/**
 * Handle DataTables server-side parameters for pagination
 */
$draw   = isset($_POST['draw'])   ? intval($_POST['draw'])   : 1;
$start  = isset($_POST['start'])  ? intval($_POST['start'])  : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// 1) Check the session role of the current logged-in user 
//    (assuming you store it in $_SESSION['arole'])
$sessionRole = $_SESSION['arole'] ?? 0; 
$canEdit     = in_array($sessionRole, [1, 3]); // e.g. 1=Super Admin, 3=Admin can edit

// Gather search fields from POST
$searchName   = $_POST['name']   ?? '';
$searchEmail  = $_POST['email']  ?? '';
$searchRole   = $_POST['role']   ?? '';
$searchStatus = $_POST['status'] ?? '';

//--------------------------------------
// Build WHERE conditions as a string
//--------------------------------------
$conditions = [];
if (!empty($searchName)) {
    $conditions[] = "username LIKE '%" . $searchName . "%'";
}
if (!empty($searchEmail)) {
    $conditions[] = "email LIKE '%" . $searchEmail . "%'";
}
if ($searchRole !== '') {
    $conditions[] = "role = '" . $searchRole . "'";
}
if ($searchStatus !== '') {
    $conditions[] = "status = '" . $searchStatus . "'";
}
$where = !empty($conditions) ? implode(' AND ', $conditions) : '';

//--------------------------------------
// 2) Get total counts from BOTH tables
//--------------------------------------
$totalUsers  = $dbFunctions->getCount('users', '*', '');
$totalAdmins = $dbFunctions->getCount('admins', '*', '');
$totalRecords = $totalUsers + $totalAdmins;

//--------------------------------------
// 3) Get filtered counts from BOTH tables
//--------------------------------------
$filteredCountUsers  = $dbFunctions->getCount('users', '*', $where);
$filteredCountAdmins = $dbFunctions->getCount('admins', '*', $where);
$filteredCount       = $filteredCountUsers + $filteredCountAdmins;

/**
 * 4) Fetch raw data from both tables (unlimited), then merge
 *    We'll do the sorting + pagination in PHP after merging.
 *
 *    Note: We pass a very large limit to get all matching results,
 *    or you can fetch in two passes. But simplest is to fetch all,
 *    then do array_slice().
 */
$usersRows = $dbFunctions->getDatanotenc(
    'users',
    $where,    // where
    '',        // group by
    'id',      // order by
    'DESC',    // order direction
    0,         // offset
    999999999  // very large limit
);

$adminsRows = $dbFunctions->getDatanotenc(
    'admins',
    $where,
    '',
    'id',
    'DESC',
    0,
    999999999
);

// 5) Merge them and label each row with a 'source'
$mergedRows = [];
foreach ($usersRows as $uRow) {
    // you might add a key to track which table
    $uRow['source_table'] = 'users'; 
    $mergedRows[] = $uRow;
}
foreach ($adminsRows as $aRow) {
    $aRow['source_table'] = 'admins';
    $mergedRows[] = $aRow;
}

// 6) Sort merged array by id DESC
usort($mergedRows, function($a, $b) {
    // we want descending, so compare b - a
    return ($b['id'] - $a['id']);
});

// 7) Slice out the portion for DataTables pagination
$paginatedRows = array_slice($mergedRows, $start, $length);

// Now build the $data array
$data = [];
$currentUser = base64_decode($_SESSION['userid'] ?? '');

foreach ($paginatedRows as $row) {
    // ---- Chat logic (same as before) ---
    $chat = '';
    $conversations = $dbFunctions->getDatanotenc(
        'conversations', 
        "user_one = '$row[id]' OR user_two = '$row[id]'"
    );
    if (!empty($conversations)) {
        if ($row['id'] != $currentUser) {
            $chat = '<a class="btn btn-success" href="'.$urlval.'admin/messange.php">Chat Now</a>';
        }
    } else {
        if ($row['id'] != $currentUser) {
            $chat = '<a class="btn btn-warning create-chat-btn" data-chatid="'. $security->encrypt($row['id']). '">
                        Create Chat
                     </a>';
        }
    }

    // ---- Map role to text/badge (same approach) ----
    switch ($row['role']) {
        case 1:
            $roleClass = 'badge-danger';
            $roleText  = 'Super Admin';
            break;
        case 2:
            $roleClass = 'badge-info';
            $roleText  = 'Trader';
            break;
        case 3:
            $roleClass = 'badge-success';
            $roleText  = 'Admin';
            break;
        case 4:
            $roleClass = 'badge-warning';
            $roleText  = 'Moderator';
            break;
        default:
            $roleClass = 'user';
            $roleText  = 'User';
            break;
    }

    // Only show checkbox if user’s role != 1
    $checkboxHtml = ($row['role'] == 1) ? '' : '<input type="checkbox">';

    // Decide Edit/Delete based on $canEdit
    $editButton   = '';
    $deleteButton = '';
    if ($canEdit) {
        if ($row['role'] == 1) {
            // No one can edit super admin
            $editButton   = '<span class="role superadmin">Super Admin</span>';
            $deleteButton = '';
        } else {
            // Show Edit/Delete
            $editButton = '<a class="btn btn-warning btn-sm"
                             href="'. $urlval .'admin/user/edit.php?id='. base64_encode($row['id']) .'">
                               Edit
                           </a>';
            $deleteButton = '<button class="btn btn-danger btn-sm"
                               data-id="'. $security->encrypt($row['id']). '">
                               Delete
                           </button>';
        }
    }

    // Build the status select
    if ($canEdit && $row['role'] != 1) {
        $statusHtml = '
            <div style="position: relative; display: inline-block; width: 100%;">
                <select class="js-select2 user-status-select" data-id="'. $security->encrypt($row['id']). '"
                    style="width:100%; background-color:#f5f5f5; border:1px solid #ddd;">
                    <option value="1"'. ($row['status'] == 1 ? ' selected' : '') .'>Activate</option>
                    <option value="0"'. ($row['status'] == 0 ? ' selected' : '') .'>Block</option>
                </select>
            </div>';
    } elseif ($row['role'] == 1) {
        $statusHtml = '<span class="badge badge-success">Active (Super Admin)</span>';
    } else {
        $statusHtml = $row['status'] == 1
                    ? '<span class="badge badge-info">Activated</span>'
                    : '<span class="badge badge-danger">Blocked</span>';
    }

    // Build the row in the same columns as your DataTable
    $data[] = [
        'checkbox' => $checkboxHtml, 
        'name'     => '
            <div class="table-data__info">
               <h6>'. $row['username'] .'</h6>
               <span><a href="#">'. $row['email'] .'</a></span>
            </div>',
        'email'    => $row['email'],
        'role'     => '<span class="role '. $roleClass .'">'. $roleText .'</span>',
        'type'     => $statusHtml,
        'chat'     => $chat,
        'actions'  => $editButton . ' ' . $deleteButton
    ];
}

// Finally, return JSON
$response = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($filteredCount),
    "data"            => $data
];

echo json_encode($response);
