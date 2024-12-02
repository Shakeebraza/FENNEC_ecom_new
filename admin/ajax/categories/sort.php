<?php
require_once('../../../global.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = $_POST['order']; 
    $parent_id = $_POST['parent_id']; 
    $data=$fun->ordercat($order,$parent_id);
    
}