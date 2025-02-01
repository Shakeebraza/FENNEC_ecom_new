<?php
require_once('../../../global.php');

// Optional: restrict to in_array($_SESSION['role'], [1,3,4])
// $role = $_SESSION['role'] ?? 0;
// if (!in_array($role, [1,3,4])) {
//     echo json_encode(['status'=>'error','message'=>'Unauthorized']);
//     exit;
// }

$bannerId = isset($_POST['id']) ? base64_decode($_POST['id']) : null;
if (!$bannerId) {
    echo json_encode(['status' => 'error', 'message' => 'Banner ID is missing']);
    exit;
}

$query = "SELECT * FROM banners WHERE id = :bannerId LIMIT 1";
$stmt  = $pdo->prepare($query);
$stmt->bindParam(':bannerId', $bannerId, PDO::PARAM_INT);
$stmt->execute();
$banner = $stmt->fetch(PDO::FETCH_ASSOC);

if ($banner) {
    echo json_encode([
        'status' => 'success',
        'data' => [
            'title'       => $banner['title'],
            'description' => $banner['description'],
            'image'       => $urlval . $banner['image'],
            'btn_text'    => $banner['btn_text'],
            'btn_url'     => $banner['btn_url'],
            'text_color'  => $banner['text_color'],
            'btn_color'   => $banner['btn_color'],
            'bg_color'    => $banner['bg_color'],
            'placement'   => $banner['placement'],
            'is_active'   => $banner['is_active'],
            'created_at'  => $banner['created_at'],
            'updated_at'  => $banner['updated_at'],
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Banner not found']);
}
?>
