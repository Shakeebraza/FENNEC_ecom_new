<?php
ob_start(); 
require_once("global.php"); 

if (isset($_SESSION['userid'])) {
    $userId = base64_decode($_SESSION['userid']); 
    $dataRec = $dbFunctions->updateData('users', ['remember_token' => NULL], "id = '$userId'");

    session_unset();
    session_destroy();

    if (isset($_COOKIE['remember_token'])) {
        setcookie("remember_token", "", time() - 3600, "/");
    }


    echo '
    <script>
    window.location = "'.$urlval.'LoginRegister.php";
    </script>
    ';
    ob_end_flush(); 
    exit();
} else {

    echo '
    <script>
    window.location = "'.$urlval.'LoginRegister.php";
    </script>
    ';
    ob_end_flush(); 
    exit();
}
?>
