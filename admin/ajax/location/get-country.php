<?php
require_once('../../../global.php');

// Fetch all countries (this might be an incorrect table if your table name is actually 'countries' and not 'cities')
$countries = $dbFunctions->getDatanotenc('countries', '', '', $orderBy = 'id', $orderDirection = 'ASC', 0, 200);

$response = [];
if (!empty($countries)) {
    foreach ($countries as $country) {
        $response[] = [
            'id' => $country['id'],
            'name' => $country['name']
        ];
    }
} else {
    $response = ['message' => 'No countries available'];
}

// Return the response as JSON
echo json_encode($response);
?>
