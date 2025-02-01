<?php
require_once('../../../global.php');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_id'])) {
    $area_id = intval($security->decrypt($_POST['area_id']) ); 


    if (!$area_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid Area ID']);
        exit;
    }


    $query = "
        SELECT 
            a.id AS area_id, a.name AS area_name, a.latitude AS area_latitude, a.longitude AS area_longitude,
            c.id AS city_id, c.name AS city_name, c.latitude AS city_latitude, c.longitude AS city_longitude,
            co.id AS country_id, co.name AS country_name, co.latitude AS country_latitude, co.longitude AS country_longitude
        FROM 
            areas a
        INNER JOIN 
            cities c ON a.city_id = c.id
        INNER JOIN 
            countries co ON c.country_id = co.id
        WHERE 
            a.id = :area_id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':area_id', $area_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $location = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($location) {
            echo json_encode(['success' => true, 'data' => $location]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Location not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch location details.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
