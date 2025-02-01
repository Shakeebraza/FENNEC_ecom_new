<?php
require_once("../../../global.php");

$id = base64_decode($_POST['id']);
$username = $_POST['username'];
$email = $_POST['email'];
$role = $_POST['role'];
$status = $_POST['status'];
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
$premium = $_POST['premium'];

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID.']);
    exit;
}


$updateFields = [
    "username = :username",
    "email = :email",
    "role = :role",
    "status = :status",
    "premium = :premium"
];

if ($password) {
    $updateFields[] = "password = :password";
}

$updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";

try {
 
    $stmt = $pdo->prepare($updateQuery);

    // Bind values
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':role', $role, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_INT);
    $stmt->bindValue(':premium', $premium, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($password) {
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    }


    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
