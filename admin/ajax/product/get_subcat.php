<?php
require_once('../../../global.php');

if (isset($_POST['catId'])) {
    $catId = $_POST['catId'];
    $cat = $dbFunctions->getDatanotenc('subcategories',"category_id = '$catId'",'', $orderBy = 'id', $orderDirection = 'ASC',0, 100);
    if (!empty($cat)) {
        foreach ($cat as $val) {
            echo '<option value="'.$val['id'].'">'.$val['subcategory_name'].'</option>';
        }
    } else {
        echo '<option value="" disabled>No cities available</option>';
    }

}

?>