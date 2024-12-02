<?php
require_once 'global.php'; 
include_once 'header.php';

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    $page = $dbFunctions->getDatanotenc('pages',"slug = '$slug'");
    if ($page) {
        ?>
        <div class="container my-5">
         
            <?php echo html_entity_decode($page[0]['subcontent']); ?> 
        </div>
        <?php
    } else {
      
        echo "<h1 class='text-danger'>Page Not Found</h1>";
    }
} else {

    echo "<h1 class='text-warning'>No Page Selected</h1>";
}


include_once 'footer.php';
?>
