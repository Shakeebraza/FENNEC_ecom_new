<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($CsrfProtection->validateToken($_POST['token'])) {
        if ($fun->RequestSessioncheck()) {
            $country = $_POST['country'] ?? '';
            $city = $_POST['city'] ?? '';
            $contactNumber = $_POST['contactNumber'] ?? '';
            $address = $_POST['address'] ?? '';

            $userId = base64_decode($_SESSION['userid']);
            if ($userId === false) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
                exit;
            }
            $data = [
                'country' => $country,
                'city' => $city,
                'number' => $contactNumber,
                'address' => $address,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $checkUserExists = $dbFunctions->getDatanotenc('user_detail', "userid = $userId");

            if ($checkUserExists) {

                $userDetailId = $checkUserExists[0]['id'];
                $response = $dbFunctions->updateData('user_detail', $data, $userDetailId);

                if ($response['success']) {
                    echo json_encode(['status' => 'success', 'message' => 'Details updated successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update details']);
                }
            } else {
                $data['userid'] = $userId;
                $data['created_at'] = date('Y-m-d H:i:s');

                $insertResponse = $dbFunctions->setData('user_detail', $data);
                if ($insertResponse['success']) {
                    echo json_encode(['status' => 'success', 'message' => 'Details inserted successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to insert details']);
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Session validation failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
