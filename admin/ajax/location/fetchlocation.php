<?php
require_once('../../../global.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $searchName = $_POST['name'] ?? ''; 
    $status = $_POST['status'] ?? '';      
    

    $limit = $_POST['length'] ?? 10; 
    $offset = $_POST['start'] ?? 0;   


    $sql = "
        SELECT 
            c.name AS country, 
            ci.name AS city, 
            a.name AS area, 
            a.id AS area_id,
            ci.id AS city_id,
            c.id AS country_id
        FROM areas a
        JOIN cities ci ON a.city_id = ci.id
        JOIN countries c ON ci.country_id = c.id
        WHERE 1=1
    ";
    

    if (!empty($searchName)) {
        $sql .= " AND (c.name LIKE :searchName OR ci.name LIKE :searchName OR a.name LIKE :searchName)";
    }


    $sql .= " LIMIT :offset, :limit";


    $stmt = $pdo->prepare($sql);
    

    if (!empty($searchName)) {
        $searchTerm = '%' . $searchName . '%';
        $stmt->bindParam(':searchName', $searchTerm, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();


    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $countStmt = $pdo->query("SELECT COUNT(*) FROM areas a JOIN cities ci ON a.city_id = ci.id JOIN countries c ON ci.country_id = c.id");
    $totalRecords = $countStmt->fetchColumn();
    
    $response = [
        "draw" => $_POST['draw'] ?? 1, 
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => count($locations),
        "data" => [] 
    ];
    
    
    foreach ($locations as $location) {
        $encryptedId = $security->encrypt($location['area_id']);
        $response['data'][] = [
            "country" => $location['country'],
            "city" => $location['city'],
            "aera" => $location['area'],
            "actions" => "<button class='btn btn-info btn-sm' onclick=\"editLocation('$encryptedId')\">Edit</button>
                          <button class='btn btn-danger btn-sm' onclick=\"deleteLocation('$encryptedId')\">Delete</button>"
        ];
    }

    echo json_encode($response);
}
?>
