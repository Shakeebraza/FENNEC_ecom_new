<?php
require_once('../../../global.php');
header('Content-Type: application/json');
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
    $countryId = isset($_POST['country_id']) ? $_POST['country_id'] : '';

    if (!empty($name) && !empty($latitude) && !empty($longitude) && !empty($countryId)) {
        // Insert into the cities table
        $result = $dbFunctions->setData('cities', [
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'country_id' => $countryId
        ]);

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'City added successfully';
        } else {
            $response['message'] = 'Failed to add city';
        }
    } else {
        $response['message'] = 'All fields are required';
    }
}

echo json_encode($response);
?>
