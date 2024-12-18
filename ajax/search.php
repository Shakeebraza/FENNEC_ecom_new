<?php
require_once "../global.php";

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    $results = $productFun->searchData('products', $query);

    if ($results) {
        echo ' <div class="suggestions" id="suggestions">';
        foreach ($results as $result) {
            echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
            echo '<i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>'; // Search icon
            echo '<a href="category.php?pid=' . $security->encrypt(htmlspecialchars($result['id'])) . '&slug=' . htmlspecialchars($result['slug']) . '">' . htmlspecialchars($result['name']) . '</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
        echo '<i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>'; // Search icon
        echo '<a href="searchnotfound.php?search=' . urlencode($query) . '">No results found for: <strong>' . htmlspecialchars($query) . '</strong></a>';
        echo '</div>';
    }
}
?>
