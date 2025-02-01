<?php
require_once('../../../global.php');
header('Content-Type: application/json');
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
    $cityId = isset($_POST['city_id']) ? $_POST['city_id'] : '';

    if (!empty($name) && !empty($latitude) && !empty($longitude) && !empty($cityId)) {
        // Insert into the areas table
        $result = $dbFunctions->setData('areas', [
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city_id' => $cityId
        ]);

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Area added successfully';
        } else {
            $response['message'] = 'Failed to add area';
        }
    } else {
        $response['message'] = 'All fields are required';
    }
}

echo json_encode($response);
?>