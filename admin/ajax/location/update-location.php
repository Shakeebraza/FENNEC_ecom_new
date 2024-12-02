<?php
require_once('../../../global.php');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $country_id = $_POST['country_id'];
    $country_name = $_POST['country_name'];
    $country_longitude = $_POST['country_longitude'];
    $country_latitude = $_POST['country_latitude'];
    $city_id = $_POST['city_id'];
    $city_name = $_POST['city_name'];
    $city_longitude = $_POST['city_longitude'];
    $city_latitude = $_POST['city_latitude'];
    $area_id = $_POST['area_id'];
    $area_name = $_POST['area_name'];
    $area_longitude = $_POST['area_longitude'];
    $area_latitude = $_POST['area_latitude'];
    $query = "
        UPDATE areas
        SET 
            name = :area_name, longitude = :area_longitude, latitude = :area_latitude
        WHERE id = :area_id;
        UPDATE cities
        SET 
            name = :city_name, longitude = :city_longitude, latitude = :city_latitude
        WHERE id = :city_id;
        UPDATE countries
        SET 
            name = :country_name, longitude = :country_longitude, latitude = :country_latitude
        WHERE id = :country_id;
    ";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':area_name', $area_name);
    $stmt->bindParam(':area_longitude', $area_longitude);
    $stmt->bindParam(':area_latitude', $area_latitude);
    $stmt->bindParam(':area_id', $area_id);

    $stmt->bindParam(':city_name', $city_name);
    $stmt->bindParam(':city_longitude', $city_longitude);
    $stmt->bindParam(':city_latitude', $city_latitude);
    $stmt->bindParam(':city_id', $city_id);

    $stmt->bindParam(':country_name', $country_name);
    $stmt->bindParam(':country_longitude', $country_longitude);
    $stmt->bindParam(':country_latitude', $country_latitude);
    $stmt->bindParam(':country_id', $country_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update location details.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
