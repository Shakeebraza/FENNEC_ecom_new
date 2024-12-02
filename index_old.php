<?php
require_once 'global.php';
$user = new User($db);
$userData = $user->getUser(1);
if ($userData) {
    echo "User ID: " . $userData['id'] . "<br>";
    echo "Username: " . $userData['username'] . "<br>";
} else {
    echo "User not found.";
}
$csrfToken = CsrfProtection::generateToken();
echo $csrfToken;
$dataToEncrypt = 12;
$datanew ="shakeebraza";
echo "<br>";
echo "data en".$security->encrypt($datanew);
echo "<br>";
$encryptedData = $security->encrypt($dataToEncrypt);
echo "Encrypted Data: " . $encryptedData . "<br>";

$decryptedData = $security->decrypt($encryptedData);
echo "Decrypted Data: " . $decryptedData . "<br>";

$tableName = 'users';
$users = $dbFunctions->getData($tableName);

foreach ($users as $user) {
    echo "User Data:<br>";
    foreach ($user as $key => $value) {
        $decryptedValue = $security->decrypt($value);
        echo htmlspecialchars($key) . ": " . htmlspecialchars($decryptedValue) . "<br>"; 
    }
    echo "<br>";
}
?>
