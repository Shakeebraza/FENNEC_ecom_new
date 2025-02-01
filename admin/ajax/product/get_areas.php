<?php
require_once('../../../global.php');

if (isset($_POST['city_id'])) {
    $city_id = $_POST['city_id'];
    $aeras = $dbFunctions->getDatanotenc('areas',"city_id = '$city_id'",'', $orderBy = 'id', $orderDirection = 'ASC',0, 500);
    if (!empty($aeras)) {
        foreach ($aeras as $aera) {
            echo '<option value="'.$aera['id'].'">'.$aera['name'].'</option>';
        }
    } else {
        echo '<option value="" disabled>No cities available</option>';
    }

}

?>