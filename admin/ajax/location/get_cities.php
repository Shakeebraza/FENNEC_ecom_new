<?php
require_once('../../../global.php');

// Fetch all cities without any country filtering
$cities = $dbFunctions->getDatanotenc('cities', '', '', $orderBy = 'id', $orderDirection = 'ASC', 0, 500);

$response = [];
if (!empty($cities)) {
    foreach ($cities as $city) {
        $response[] = [
            'id' => $city['id'],
            'name' => $city['name']
        ];
    }
} else {
    $response = ['message' => 'No cities available'];
}

// Return the response as JSON
echo json_encode($response);
?>
