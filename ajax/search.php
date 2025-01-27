<?php
require_once "../global.php";

if (isset($_GET['q'])) {
    $query   = $_GET['q'];
    $results = $productFun->searchData('products', $query);

    // If a location param is also passed, we’ll append it:
    if (isset($_GET['location'])) {
        $location = '&location=' . urlencode($_GET['location']);
    } else {
        $location = '';
    }

    if ($results) {
        echo '<div class="suggestions" id="suggestions">';

        foreach ($results as $result) {
            // Build an absolute link:
            // e.g. "http://localhost/fennec/category.php?search=..."
            $productIdEncrypted  = $security->encrypt(htmlspecialchars($result['id']));
            $productSlug         = htmlspecialchars($result['slug']);
            $productName         = urlencode($result['name']);

            $link = $urlval 
                  . 'category.php?search=' . $productName
                  . '&pid=' . $productIdEncrypted
                  . '&slug=' . $productSlug 
                  . $location;

            echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
            echo '  <a href="' . $link . '" 
                        style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>
                        <span>' . htmlspecialchars($result['name']) . '</span>
                    </a>';
            echo '</div>';
        }

        echo '</div>';
    } else {
        // No results => Link to searchnotfound page
        $notFoundLink = $urlval 
            . 'searchnotfound.php?search=' 
            . urlencode($query);

        echo '<div class="suggestion-item" style="display: flex; align-items: center;">';
        echo '  <a href="' . $notFoundLink . '" 
                    style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                    <i class="fa fa-search" style="margin-right: 8px; color: #555;"></i>
                    <span>No results found for: <strong>' . htmlspecialchars($query) . '</strong></span>
                </a>';
        echo '</div>';
    }
}
?>
