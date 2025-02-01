<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lanId = $_POST['id'] ?? null;

    if (!$lanId) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }

    $idDecrypt = base64_decode($lanId);
    


    // Delete the language record from the database
    $deleteResult = $dbFunctions->delData('contacts', "id = '$idDecrypt'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true, 'message' => 'Contacts and file deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting language.']);
    }
}
