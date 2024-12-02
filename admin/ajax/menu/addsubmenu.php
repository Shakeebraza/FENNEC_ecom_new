<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $link = $_POST['link'] ?? '';
    $status = $_POST['status'] ?? '';
    $menuid = $security->decrypt($_POST['menuid']);

  
    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL provided for the link.']);
        exit;
    }


    $updateData = [
        'name' => $heading,
        'slug' => $slug,
        'menu_id' => $menuid,
        'link' => $link,
        'is_enable' => $status,
        'created_at'=> date('Y-m-d H:i:s'),
    ];


    $updateResult = $dbFunctions->setData('menu_items', $updateData);

    if ($updateResult['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating menu: ' . $updateResult['message']]);
    }
}
?>
