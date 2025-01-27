<?php
// fetchUsers.php

// Error Handling: Log errors instead of displaying them
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/php-error.log'); // **IMPORTANT:** Update this path to a writable location

require_once('../../../global.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is authenticated and authorized
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    // Unauthorized access
    $response = [
        "draw" => intval($_POST['draw'] ?? 1),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Unauthorized access."
    ];
    echo json_encode($response);
    exit();
}

header('Content-Type: application/json');

// Get DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Gather search fields
$searchName   = $_POST['name']   ?? '';
$searchEmail  = $_POST['email']  ?? '';
$searchRole   = $_POST['role']   ?? '';
$searchStatus = $_POST['status'] ?? '';

// Initialize conditions
$conditions = [];

// WHERE conditions with proper sanitization
if (!empty($searchName)) {
    $searchNameEscaped = $dbFunctions->escapeString($searchName);
    $conditions[] = "username LIKE '%$searchNameEscaped%'";
}
if (!empty($searchEmail)) {
    $searchEmailEscaped = $dbFunctions->escapeString($searchEmail);
    $conditions[] = "email LIKE '%$searchEmailEscaped%'";
}
if ($searchRole !== '') {
    $searchRoleEscaped = $dbFunctions->escapeString($searchRole);
    $conditions[] = "role = '$searchRoleEscaped'";
}
if ($searchStatus !== '') {
    $searchStatusEscaped = $dbFunctions->escapeString($searchStatus);
    $conditions[] = "status = '$searchStatusEscaped'";
}

$where = !empty($conditions) ? implode(' AND ', $conditions) : '';

// Total records without filtering
$totalRecords = $dbFunctions->getCount('users', '*', '');

// Total records with filtering
$filteredRecords = ($where !== '') ? $dbFunctions->getCount('users', '*', $where) : $totalRecords;

// Fetch the filtered data with pagination
$filteredQuery = $dbFunctions->getDatanotenc('users', $where, '', 'id', 'DESC', $start, $length);

// Check if getDatanotenc returned valid data
if ($filteredQuery === false) {
    $response = [
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => [],
        "error" => "Failed to fetch user data."
    ];
    echo json_encode($response);
    exit();
}

$data = [];

$currentUser = base64_decode($_SESSION['userid'] ?? '');

// Determine if the current user can edit
$canEdit = in_array($role, [1,3]);

foreach ($filteredQuery as $row) {
    // Chat logic
    $chat = '';
    $conversations = $dbFunctions->getDatanotenc1(
        'conversations', 
        "user_one = '{$row['id']}' OR user_two = '{$row['id']}'"
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

    // Map user’s role to text and badge class
    switch ($row['role']) {
        case 1:  // Super Admin
            $roleClass = 'badge-danger';  // Bootstrap's red badge
            $roleText  = 'Super Admin';
            break;
        case 2:  // Trader
            $roleClass = 'badge-info';     // Bootstrap's blue badge
            $roleText  = 'Trader';
            break;
        case 3:  // Admin
            $roleClass = 'badge-success';  // Bootstrap's green badge
            $roleText  = 'Admin';
            break;
        case 4:  // Moderator
            $roleClass = 'badge-warning';  // Bootstrap's yellow badge
            $roleText  = 'Moderator';
            break;
        default:  // 0 or unknown => "User"
            $roleClass = 'badge-primary'; // Bootstrap's gray badge
            $roleText  = 'User';
            break;
    }

    // Only show checkbox if user’s role != 1 (Super Admin)
    $checkboxHtml = ($row['role'] == 1) ? '' : '<input type="checkbox">';

    // Edit and Delete buttons based on permissions
    $editButton   = '';
    $deleteButton = '';

    if ($canEdit) {
        if ($row['role'] == 1) {
            // Do not allow editing Super Admin
            $editButton   = '<span class="badge badge-danger">Super Admin</span>';
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

    // Status HTML
    if ($canEdit && $row['role'] != 1) {
        // If user is not Super Admin, allow status change
        $statusHtml = '
            <div style="position: relative; display: inline-block; width: 100%;">
                <select class="js-select2 user-status-select" data-id="'. $security->encrypt($row['id']). '"
                    style="width:100%; background-color:#f5f5f5; border:1px solid #ddd;">
                    <option value="1"'. ($row['status'] == 1 ? ' selected' : '') .'>Activate</option>
                    <option value="0"'. ($row['status'] == 0 ? ' selected' : '') .'>Block</option>
                </select>
            </div>';
    } elseif ($row['role'] == 1) {
        // Super Admin cannot change status
        $statusHtml = '<span class="badge badge-success">Active (Super Admin)</span>';
    } else {
        // Read-only for others
        $statusHtml = $row['status'] == 1
                    ? '<span class="badge badge-info">Activated</span>'
                    : '<span class="badge badge-danger">Blocked</span>';
    }

    // Final array in the same order as DataTables columns
    $data[] = [
        'checkbox' => $checkboxHtml, 
        'name'     => '
            <div class="table-data__info">
               <h6>'. htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') .'</h6>
               <span><a href="mailto:'. htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') .'">'. htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') .'</a></span>
            </div>',
        'email'    => htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'),
        'role'     => '<span class="badge '. htmlspecialchars($roleClass, ENT_QUOTES, 'UTF-8') .'">'. htmlspecialchars($roleText, ENT_QUOTES, 'UTF-8') .'</span>',
        'type'     => $statusHtml,
        'chat'     => $chat,
        'actions'  => $editButton . ' ' . $deleteButton
    ];
}

// Prepare the response
$response = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($filteredRecords),
    "data"            => $data
];

// Encode response to JSON
$json = json_encode($response);

// Check for JSON encoding errors
if ($json === false) {
    $json = json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => [],
        "error" => "JSON Encoding Error: " . json_last_error_msg()
    ]);
}

// Output JSON and terminate script
echo $json;
exit();
?>
