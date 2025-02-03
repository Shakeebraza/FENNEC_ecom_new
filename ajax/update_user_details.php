<?php
require_once("../global.php");

// Define log file path
define('LOG_FILE', __DIR__ . '/debug.log');

function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $formattedMessage, FILE_APPEND);
}

logMessage("Script accessed via {$_SERVER['REQUEST_METHOD']} request.");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logMessage("Invalid request method: {$_SERVER['REQUEST_METHOD']}");
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Retrieve POST variables
$firstName     = $_POST['first-name']     ?? '';
$lastName      = $_POST['last-name']      ?? '';
$country       = $_POST['country']        ?? '';
$city          = $_POST['city']           ?? '';
$contactNumber = $_POST['contactNumber']  ?? '';
$address       = $_POST['address']        ?? '';
$language      = $_POST['language']       ?? '';
$username      = $_POST['username']       ?? '';

// For traders only
$companyName   = $_POST['companyName']    ?? '';
$urlLink       = $_POST['urlLink']        ?? '';

logMessage("POST data received: " . json_encode($_POST));

if (!$fun->RequestSessioncheck()) {
    logMessage("Session validation failed for user.");
    echo json_encode(['status' => 'error', 'message' => 'Session validation failed']);
    exit();
}

$userId = base64_decode($_SESSION['userid']);
if ($userId === false) {
    logMessage("Invalid user ID in session.");
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}
logMessage("Decoded user ID: $userId");

// Prepare data for updating/inserting user details (stored in the user_detail table)
$data = [
    'first_name' => $firstName,
    'last_name'  => $lastName,
    'country'    => $country,
    'city'       => $city,
    'number'     => $contactNumber,
    'address'    => $address,
    'language'   => $language,
    'updated_at' => date('Y-m-d H:i:s')
];

// *** Handle Profile Image Upload ***
// Since the profile image belongs in the users table, we handle it separately.
$profileImagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath   = $_FILES['profile_image']['tmp_name'];
    $fileName      = $_FILES['profile_image']['name'];
    $fileNameCmps  = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Create a unique file name
        $newFileName   = md5(time() . $fileName) . '.' . $fileExtension;
        // Define the target directory (adjust relative path as needed)
        $uploadFileDir = '../upload/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        $dest_path = $uploadFileDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $profileImagePath = 'upload/' . $newFileName;
            logMessage("Profile image uploaded successfully: " . $dest_path);
        } else {
            logMessage("Error moving the uploaded file to: " . $dest_path);
        }
    } else {
        logMessage("File extension not allowed: " . $fileExtension);
    }
}

// Only assign trader-specific details for the user_detail table
if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
    $data['company_name'] = $companyName;
    $data['url_link']     = $urlLink;
    logMessage("User is a trader. Added company_name and url_link to data.");
}

logMessage("Data to be updated/inserted into user_detail: " . json_encode($data));

try {
    // Update or insert into the user_detail table
    $checkUserExists = $dbFunctions->getDatanotenc('user_detail', "userid = $userId");
    logMessage("Checked if user exists in user_detail: " . json_encode($checkUserExists));

    if ($checkUserExists) {
        $userDetailId = $checkUserExists[0]['id'];
        logMessage("User detail ID found: $userDetailId");
        $response = $dbFunctions->updateData('user_detail', $data, $userDetailId);
        logMessage("Update response for user_detail: " . json_encode($response));

        if (!$response['success']) {
            throw new Exception('Failed to update details in user_detail');
        }
    } else {
        $data['userid']     = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        logMessage("Inserting new user details into user_detail: " . json_encode($data));
        $insertResponse = $dbFunctions->setData('user_detail', $data);
        logMessage("Insert response for user_detail: " . json_encode($insertResponse));

        if (!$insertResponse['success']) {
            throw new Exception('Failed to insert details into user_detail');
        }
    }

    // If a new profile image was uploaded, update the 'profile' column in the users table
    if ($profileImagePath !== null) {
        $profileData = ['profile' => $profileImagePath];
        $profileUpdateResponse = $dbFunctions->updateData('users', $profileData, $userId);
        logMessage("Profile image update response in users table: " . json_encode($profileUpdateResponse));
        $_SESSION['profile'] = $urlval.$profileImagePath;
        if (!$profileUpdateResponse['success']) {
            throw new Exception('Failed to update profile image');
        }
    }

    // Check for username changes in the users table
    if ($username) {
        logMessage("Username change requested: $username");
        $currentUsernameQuery = "SELECT username FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($currentUsernameQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $currentUsername = $stmt->fetchColumn();
        logMessage("Current username: $currentUsername");

        if ($currentUsername !== $username) {
            logMessage("Username has changed from $currentUsername to $username");
            $usernameCheckQuery = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :user_id";
            $stmt = $pdo->prepare($usernameCheckQuery);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $usernameExists = $stmt->fetchColumn();
            logMessage("Username exists count: $usernameExists");

            if ($usernameExists > 0) {
                logMessage("Username already exists: $username");
                echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose another.']);
                exit();
            }

            $updateUsernameData = ['username' => $username];
            $updateUserResponse = $dbFunctions->updateData('users', $updateUsernameData, $userId);
            logMessage("Update username response in users table: " . json_encode($updateUserResponse));

            if (!$updateUserResponse['success']) {
                throw new Exception('Failed to update username');
            }

            $_SESSION['username'] = $username;
            logMessage("Username updated successfully to $username");
            echo json_encode(['status' => 'success', 'message' => 'Username and details updated successfully']);
            exit();
        } else {
            logMessage("Username remains unchanged.");
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Details updated successfully']);
    logMessage("Details updated successfully for user ID: $userId");
} catch (Exception $e) {
    logMessage("Exception caught: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
