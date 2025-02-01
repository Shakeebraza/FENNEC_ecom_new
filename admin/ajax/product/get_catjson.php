<?php
require_once('../../../global.php');

// header('Content-Type: application/json');

$response = [];

if (isset($_POST['catId'])) {
    $catId = $_POST['catId'];
    $cat = $dbFunctions->getDatanotenc('subcategories', "category_id = '$catId'", '', $orderBy = 'id', $orderDirection = 'ASC', 0, 100);

    if (!empty($cat)) {
        $subcategories = [];
        foreach ($cat as $val) {
            $subcategories[] = [
                'id' => $val['id'],
                'name' => $val['subcategory_name']
            ];
        }

        $response['status'] = 'success';
        $response['data'] = $subcategories;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No subcategories available';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid category ID';
}

echo json_encode($response);
?>
