<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? false;

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and Password are required.']);
        exit;
    }

    try {
        $where = "email = '" . $email . "'";
        $user = $dbFunctions->getDatanotenc('users', $where);

        if ($user) {
            $user = $user[0];
            
            // Only allow login for roles 0 or 2
            if (!in_array($user['role'], [0, 2])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Your account is not authorized to log in here.These are Admin level access.'
                ]);
                exit;
            }
            if ($user['admin_verified'] != 1) {
                echo json_encode(['status' => 'error', 'message' => 'Your account is pending admin approval.']);
                exit;
            }

            if (is_null($user['email_verified_at'])) {
                echo json_encode(['status' => 'error', 'message' => 'Please verify your email first.']);
                exit;
            }
            if ($user['status'] == 0) {
                echo json_encode(['status' => 'error', 'message' => 'You are blocked by Admin. Contact the admin.']);
                exit;
            }
            if (password_verify($password, $user['password'])) {
                if ($remember) {
                    $token = bin2hex(random_bytes(16));
                    $expiryTime = time() + (86400 * 30);
                    $dbFunctions->updateData('users', ['remember_token' => $token], $user['id']);
                    setcookie("remember_token", $token, $expiryTime, "/", "", true, true);
                }

                // Set session variables
                $email = $user['email'];
                $role  = $user['role'];
                $fun->sessionSet($email); // Existing session set method
                $_SESSION['role'] = $role; // Add role to session

                echo json_encode(['status' => 'success', 'role' => $role]);
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
