<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

if (!$fun->RequestSessioncheck()) {
    echo json_encode(['status' => 'error', 'message' => 'Session validation failed']);
    exit();
}

$firstName = $_POST['first-name'] ?? '';
$lastName = $_POST['last-name'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$contactNumber = $_POST['contactNumber'] ?? '';
$address = $_POST['address'] ?? '';
$language = $_POST['language'] ?? '';
$username = $_POST['username'] ?? '';

$userId = base64_decode($_SESSION['userid']);
if ($userId === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}

$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'country' => $country,
    'city' => $city,
    'number' => $contactNumber,
    'address' => $address,
    'language' => $language,
    'updated_at' => date('Y-m-d H:i:s')
];

try {
    $checkUserExists = $dbFunctions->getDatanotenc('user_detail', "userid = $userId");

    if ($checkUserExists) {
        $userDetailId = $checkUserExists[0]['id'];
        $response = $dbFunctions->updateData('user_detail', $data, $userDetailId);

        if (!$response['success']) {
            throw new Exception('Failed to update details');
        }
    } else {
        $data['userid'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $insertResponse = $dbFunctions->setData('user_detail', $data);

        if (!$insertResponse['success']) {
            throw new Exception('Failed to insert details');
        }
    }

    if ($username) {
        // Fetch current username
        $currentUsernameQuery = "SELECT username FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($currentUsernameQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $currentUsername = $stmt->fetchColumn();

        if ($currentUsername === $username) {
            // echo json_encode(['status' => 'error', 'message' => 'Entered username is the same as the current username.']);
            // exit();
        }else{

        // Check if username already exists for another user
        $usernameCheckQuery = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :user_id";
        $stmt = $pdo->prepare($usernameCheckQuery);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $usernameExists = $stmt->fetchColumn();

        if ($usernameExists > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose another.']);
            exit();
        }

        // Update username
        $updateUsernameData = ['username' => $username];
        $updateUserResponse = $dbFunctions->updateData('users', $updateUsernameData, $userId);

        if (!$updateUserResponse['success']) {
            throw new Exception('Failed to update username');
        }

        $_SESSION['username'] = $username;
        echo json_encode(['status' => 'success', 'message' => 'Username and details updated successfully']);
        exit();
    }
    }

    echo json_encode(['status' => 'success', 'message' => 'Details updated successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
