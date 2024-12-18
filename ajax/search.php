<?php
require_once "../global.php";

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    $results = $productFun->searchData('products', $query);

    if ($results) {
        echo '<div class="suggestions" id="suggestions">';
        foreach ($results as $result) {
            echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
            echo '<a href="category.php?search=' . urlencode($query) . '&pid=' . $security->encrypt(htmlspecialchars($result['id'])) . '&slug=' . htmlspecialchars($result['slug']) . '" style="display: flex; align-items: center; text-decoration: none; color: inherit;">';
            echo '<i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>'; 
            echo '<span>' . htmlspecialchars($result['name']) . '</span>';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
        echo '<a href="searchnotfound.php?search=' . urlencode($query) . '" style="display: flex; align-items: center; text-decoration: none; color: inherit;">';
        echo '<i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>';
        echo '<span>No results found for: <strong>' . htmlspecialchars($query) . '</strong></span>';
        echo '</a>';
        echo '</div>';
    }
}
?>
