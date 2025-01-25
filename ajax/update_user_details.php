<?php
require_once("../global.php");

// Define log file path
define('LOG_FILE', __DIR__ . '/debug.log');

/**
 * Logs messages to a specified log file with a timestamp.
 *
 * @param string $message The message to log.
 */
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

// Define and retrieve POST variables
$firstName    = $_POST['first-name']    ?? '';
$lastName     = $_POST['last-name']     ?? '';
$country      = $_POST['country']       ?? '';
$city         = $_POST['city']          ?? '';
$contactNumber= $_POST['contactNumber'] ?? '';
$address      = $_POST['address']       ?? '';
$language     = $_POST['language']      ?? '';
$username     = $_POST['username']      ?? '';

// For traders only
$companyName  = $_POST['companyName']   ?? '';
$urlLink       = $_POST['urlLink']        ?? '';

// Log POST data (be cautious with sensitive information)
logMessage("POST data received: " . json_encode($_POST));

if (!$fun->RequestSessioncheck()) {
    logMessage("Session validation failed for user.");
    echo json_encode(['status' => 'error', 'message' => 'Session validation failed']);
    exit();
}

// Decode user ID and log
$userId = base64_decode($_SESSION['userid']);
if ($userId === false) {
    logMessage("Invalid user ID in session.");
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}
logMessage("Decoded user ID: $userId");

// Prepare data array
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

// Only assign company_name and url_link if user is a trader
if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
    $data['company_name'] = $companyName;
    $data['url_link']      = $urlLink; // Corrected variable
    logMessage("User is a trader. Added company_name and url_link to data.");
}

// Log data to be updated or inserted
logMessage("Data to be updated/inserted: " . json_encode($data));

try {
    $checkUserExists = $dbFunctions->getDatanotenc('user_detail', "userid = $userId");
    logMessage("Checked if user exists: " . json_encode($checkUserExists));

    if ($checkUserExists) {
        $userDetailId = $checkUserExists[0]['id'];
        logMessage("User detail ID found: $userDetailId");
        $response = $dbFunctions->updateData('user_detail', $data, $userDetailId);
        logMessage("Update response: " . json_encode($response));

        if (!$response['success']) {
            throw new Exception('Failed to update details');
        }
    } else {
        $data['userid']     = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        logMessage("Inserting new user details: " . json_encode($data));
        $insertResponse = $dbFunctions->setData('user_detail', $data);
        logMessage("Insert response: " . json_encode($insertResponse));

        if (!$insertResponse['success']) {
            throw new Exception('Failed to insert details');
        }
    }

    // Check for username changes
    if ($username) {
        logMessage("Username change requested: $username");
        // Fetch current username
        $currentUsernameQuery = "SELECT username FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($currentUsernameQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $currentUsername = $stmt->fetchColumn();
        logMessage("Current username: $currentUsername");

        if ($currentUsername !== $username) {
            logMessage("Username has changed from $currentUsername to $username");
            // Check if username already exists for another user
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

            // Update username
            $updateUsernameData = ['username' => $username];
            $updateUserResponse = $dbFunctions->updateData('users', $updateUsernameData, $userId);
            logMessage("Update username response: " . json_encode($updateUserResponse));

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
