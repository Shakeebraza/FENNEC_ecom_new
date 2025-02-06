<?php
require_once('../../../global.php');

if (isset($_POST['city_id'])) {
    $city_id = $_POST['city_id'];
    // Fetch areas where city_id matches
    $areas = $dbFunctions->getDatanotenc('areas', "city_id = '$city_id'", '', $orderBy = 'id', $orderDirection = 'ASC', 0, 500);
    if (!empty($areas)) {
        foreach ($areas as $area) {
            echo '<option value="' . $area['id'] . '">' . $area['name'] . '</option>';
        }
    } else {
        echo '<option value="" disabled>No areas available</option>';
    }
}
?>
