<?php
require_once("../../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? false;

    // Basic input validation
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and Password are required.']);
        exit;
    }

    try {
        $where = "email = '" . $email . "'";
        $user  = $dbFunctions->getDatanotenc('users', $where);

        if ($user) {
            $user = $user[0];

            // Check if email has been verified
            if (is_null($user['email_verified_at'])) {
                echo json_encode(['status' => 'error', 'message' => 'Please verify your email first.']);
                exit;
            }

            // Ensure the user has a valid role
            // If you only allow roles 1,2,3 to log in:
            if (!in_array($user['role'], [1,3,4])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid role or insufficient privileges.']);
                exit;
            }

            // Check password
            if (password_verify($password, $user['password'])) {
                // "Remember me" logic
                if ($remember) {
                    $token       = bin2hex(random_bytes(16));
                    $expiryTime  = time() + (86400 * 30); // 30 days
                    $dbFunctions->updateData('users', ['remember_token' => $token], $user['id']);
                    // Set a secure, HTTP-only cookie
                    setcookie("remember_token", $token, $expiryTime, "/", "", true, true);
                }

                // Set user session
                $email      = $user['email'];
                $sessionSet = $fun->sessionSet($email);

                // Return success with user’s role
                echo json_encode(['status' => 'success', 'role' => $user['role']]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No user found with this email.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
