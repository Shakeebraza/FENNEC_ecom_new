<?php
require_once('../../../global.php');

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role']==''?'0' : $_POST['role'];
    $password = $_POST['password'];

    $errors = [];

    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } else {
        $usernameCount = $dbFunctions->getCount('users', 'username', "username = '$username'");
        if ($usernameCount > 0) {
            $errors[] = 'Username is already taken.';
        }
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } else {
        $emailCount = $dbFunctions->getCount('users', 'email', "email = '$email'");
        if ($emailCount > 0) {
            $errors[] = 'Email is already registered.';
        }
    }

    // if (empty($role) || !in_array((string) $role, ['0', '1'], true)) {
    //     $errors[] = "Please select a valid role.";
    // }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // If there are validation errors
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data
    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'role' => $role
    ];

    $response = $dbFunctions->setData('users', $data);

    // Success or error response
    if ($response['success']) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $response['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

?>