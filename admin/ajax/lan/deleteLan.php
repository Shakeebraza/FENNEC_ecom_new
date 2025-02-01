<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lanId = $_POST['id'] ?? null;

    if (!$lanId) {
        echo json_encode(['success' => false, 'message' => 'Invalid page ID.']);
        exit;
    }

    $idDecrypt = $security->decrypt($lanId);
    
    $findata = $dbFunctions->getDatanotenc('languages', "id = '$idDecrypt'");

    if ($findata) {
        $filePath = $findata[0]['file_path'];  
        $fullFilePath = "../../../" . $filePath; 


        if (file_exists($fullFilePath)) {
            unlink($fullFilePath);  
        } else {
            echo json_encode(['success' => false, 'message' => 'File not found.']);
            exit;
        }
    }

    // Delete the language record from the database
    $deleteResult = $dbFunctions->delData('languages', "id = '$idDecrypt'");

    if ($deleteResult['success']) {
        echo json_encode(['success' => true, 'message' => 'Language and file deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting language.']);
    }
}
