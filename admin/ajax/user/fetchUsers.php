<?php
require_once('../../../global.php');

/**
 * Handle DataTables server-side parameters for pagination
 */
$draw   = isset($_POST['draw'])   ? intval($_POST['draw'])   : 1;
$start  = isset($_POST['start'])  ? intval($_POST['start'])  : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Total number of records in the 'users' table (unfiltered)
$totalRecords = $dbFunctions->getCount('users', '*', '');

// 1) Check the session role of the **current logged-in user** 
$sessionRole = $_SESSION['arole'] ?? 0; 
$canEdit     = in_array($sessionRole, [1,3]); // Only 1=Super Admin, 3=Admin can edit

// Gather search fields from POST
$searchName   = $_POST['name']   ?? '';
$searchEmail  = $_POST['email']  ?? '';
$searchRole   = $_POST['role']   ?? '';
$searchStatus = $_POST['status'] ?? '';

$conditions = [];

// Build WHERE conditions
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

// Get how many rows match the filtering (without limiting)
$filteredCount = $dbFunctions->getCount('users', '*', $where);

// Now fetch the data with limit/offset for pagination
$filteredQuery = $dbFunctions->getDatanotenc(
    'users',
    $where,     // where clause
    '',         // group by
    'id',       // order by
    'DESC',     // order direction
    $start,     // offset
    $length     // limit
);

$data = [];

$currentUser = base64_decode($_SESSION['userid'] ?? '');

// Build rows
foreach ($filteredQuery as $row) {
    // **Chat logic** (unchanged)
    $chat = '';
    $conversations = $dbFunctions->getDatanotenc(
        'conversations', 
        "user_one = '$row[id]' OR user_two = '$row[id]'"
    );
    if (!empty($conversations)) {
        // If there's a conversation, show "Chat Now" unless it's the same user
        if ($row['id'] != $currentUser) {
            $chat = '<a class="btn btn-success" href="'.$urlval.'admin/messange.php">Chat Now</a>';
        }
    } else {
        // Otherwise, show "Create Chat" if it's not the same user
        if ($row['id'] != $currentUser) {
            $chat = '<a class="btn btn-warning create-chat-btn" data-chatid="'. $security->encrypt($row['id']). '">
                        Create Chat
                     </a>';
        }
    }

    // **Map user’s role** to text
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
            $roleClass = 'user'; // default for regular user
            $roleText  = 'User';
            break;
    }

    // Only show checkbox if user’s role != 1
    $checkboxHtml = ($row['role'] == 1) ? '' : '<input type="checkbox">';

    // Decide Edit/Delete based on $canEdit
    $editButton   = '';
    $deleteButton = '';

    if ($canEdit) {
        // The viewer is Admin/Super Admin
        if ($row['role'] == 1) {
            // We can decide no one can edit super admin
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
    } else {
        // If the viewer is not Admin/Super Admin, they see no edit or delete
        $editButton   = ''; 
        $deleteButton = '';
    }

    // For the **status** select, only Admin/Super Admin can modify, others read-only
    if ($canEdit && $row['role'] != 1) {
        // If row’s user is not a super admin => show dropdown
        $statusHtml = '
            <div style="position: relative; display: inline-block; width: 100%;">
                <select class="js-select2 user-status-select" data-id="'. $security->encrypt($row['id']). '"
                    style="width:100%; background-color:#f5f5f5; border:1px solid #ddd;">
                    <option value="1"'. ($row['status'] == 1 ? ' selected' : '') .'>Activate</option>
                    <option value="0"'. ($row['status'] == 0 ? ' selected' : '') .'>Block</option>
                </select>
            </div>';
    } elseif ($row['role'] == 1) {
        // This user is super admin, let’s not allow status changes
        $statusHtml = '<span class="badge badge-success">Active (Super Admin)</span>';
    } else {
        // For moderators / read-only roles, just show text
        $statusHtml = $row['status'] == 1
                    ? '<span class="badge badge-info">Activated</span>'
                    : '<span class="badge badge-danger">Blocked</span>';
    }

    // Final array in the same order as DataTables columns
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

// Return JSON
$response = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($filteredCount),
    "data"            => $data
];

echo json_encode($response);