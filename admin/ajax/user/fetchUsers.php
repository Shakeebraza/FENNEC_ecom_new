<?php
require_once('../../../global.php');
$totalRecords = $dbFunctions->getCount('users', '*', '');

$searchName = $_POST['name'] ?? '';
$searchEmail = $_POST['email'] ?? '';
$searchRole = $_POST['role'] ?? '';
$searchStatus = $_POST['status'] ?? '';

$conditions = [];

if (!empty($searchName)) {
    $conditions[] = "username LIKE '%" . $searchName . "%'";
}
if (!empty($searchEmail)) {
    $conditions[] = "email LIKE '%" . $searchEmail . "%'";
}
if (!empty($searchRole)) {
    $conditions[] = "role = '" . $searchRole . "'";
}
if (!empty($searchStatus)) {
    $conditions[] = "status = '" . $searchStatus. "'";
}

$where = !empty($conditions) ? implode(' AND ', $conditions) : '';
$filteredQuery = $dbFunctions->getDatanotenc('users', $where, '', 'id', 'DESC');

$data = [];

foreach ($filteredQuery as $row) {
    $conversations = $dbFunctions->getDatanotenc('conversations', "user_one = '$row[id]' OR user_two = '$row[id]'");
    $currentUser = base64_decode($_SESSION['userid']);

    if (!empty($conversations)) {
        foreach ($conversations as $conversation) {
            if (isset($conversation['user_one']) && isset($conversation['user_two'])) {
                if ($conversation['user_one'] != $currentUser || $conversation['user_two'] != $currentUser) {
                    if ($row['id'] == $currentUser) {
                        $chat = '';
                    } else {
                        $chat = '<a class="btn btn-success" href="'.$urlval.'admin/messange.php"> Chat Now </a>';
                    }
                } else {
                    $chat = '<a class="btn btn-warning create-chat-btn" data-chatid="'. $security->encrypt($row['id']). '"> Create Chat </a>';
                }
            }
        }
    } else {
        if ($row['id'] == $currentUser) {
            $chat = '';
        } else {
            $chat = '<a class="btn btn-warning create-chat-btn" data-chatid="'. $security->encrypt($row['id']). '"> Create Chat </a>';
        }
    }

    $roleClass = $row['role'] == 1 ? 'admin' : 'user';
    $Checkbox = $row['role'] == 1 ? '' : '<input type="checkbox">';
    $roleText = $row['role'] == 1 ? 'Admin' : 'User';
    $editButton = $row['role'] != 1 ? '<button class="btn btn-warning btn-sm" data-id="'. $security->encrypt($row['id']). '" >Edit</button>' : '<span class="role ' . $roleClass . '">' . $roleText . '</span>';
    $deleteButton = $row['role'] != 1 ? '<button class="btn btn-danger btn-sm" data-id="'. $security->encrypt($row['id']). '">Delete</button>' : '';

    $data[] = [
        'checkbox' => $Checkbox,
        'chat' => $chat,
        'name' => '<div class="table-data__info">
                      <h6>' . $row['username'] . '</h6>
                      <span><a href="#">' . $row['email'] . '</a></span>
                   </div>',
        'email' => $row['email'],
        'role' => '<span class="role ' . $roleClass . '">' . $roleText . '</span>',
        'type' => '<div style="position: relative; display: inline-block; width: 100%; margin: 0 auto;">
            <select class="js-select2 user-status-select" data-id="'. $security->encrypt($row['id']). '"
                style="
                  width: 100%;
                  background-color: #f5f5f5;
                  color: #333;
                  border: 1px solid #ddd;
                  border-radius: 8px;
                  padding: 10px 15px;
                  font-size: 14px;
                  appearance: none;
                  transition: all 0.3s ease;
                  cursor: pointer;
                " 
                onmouseover="this.style.backgroundColor=\'#fff\'; this.style.borderColor=\'#007bff\'; this.style.boxShadow=\'0px 4px 6px rgba(0, 0, 0, 0.1)\';" 
                onmouseout="this.style.backgroundColor=\'#f5f5f5\'; this.style.borderColor=\'#ddd\'; this.style.boxShadow=\'none\';"
                onfocus="this.style.borderColor=\'#007bff\'; this.style.boxShadow=\'0px 4px 6px rgba(0, 123, 255, 0.3)\';"
                onblur="this.style.borderColor=\'#ddd\'; this.style.boxShadow=\'none\';"
              >
                <option value="1"' . ($row['status'] == 1 ? ' selected' : '') . '>Activate</option>
                <option value="0"' . ($row['status'] == 0 ? ' selected' : '') . '>Block</option>
            </select>
            <span style="
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                pointer-events: none;
                color: #007bff;
                font-size: 12px;
            ">
                ▼
            </span>
        </div>',
        // 'actions' => $editButton . ' ' . $deleteButton
        'actions' => $editButton
    ];
}

$response = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => count($filteredQuery),
    "data" => $data
];

echo json_encode($response);
?>
