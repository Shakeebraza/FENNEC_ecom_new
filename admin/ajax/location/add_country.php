<?php
require_once('../../../global.php');
header('Content-Type: application/json');
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

    if (!empty($name) && !empty($latitude) && !empty($longitude)) {
        $result = $dbFunctions->setdata('countries', [
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Country added successfully';
        } else {
            $response['message'] = 'Failed to add country';
        }
    } else {
        $response['message'] = 'All fields are required';
    }
}

echo json_encode($response);
?>
