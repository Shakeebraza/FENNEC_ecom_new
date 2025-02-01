<?php
require_once('../../../global.php');

if (isset($_POST['country_id'])) {
    $countryId = $_POST['country_id'];
    $cities = $dbFunctions->getDatanotenc('cities',"country_id = '$countryId'",'', $orderBy = 'id', $orderDirection = 'ASC',0, 100);
    if (!empty($cities)) {
        foreach ($cities as $city) {
            echo '<option value="'.$city['id'].'">'.$city['name'].'</option>';
        }
    } else {
        echo '<option value="" disabled>No cities available</option>';
    }

}

?>