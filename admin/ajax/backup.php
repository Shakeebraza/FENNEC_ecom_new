<?php
require_once("../../global.php");


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    die('Unauthorized access. Only admins can perform this action.');
}

$backupFile = tempnam(sys_get_temp_dir(), 'backup_') . '.sql';


if ($db->backup($backupFile)) {

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="database_backup.sql"');
    readfile($backupFile);
    unlink($backupFile); 
} else {
    echo "Error: Could not create the backup.";
}
?>
