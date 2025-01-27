<?php
require_once "../global.php";

// Define the maximum length for the description in suggestions
define('MAX_DESCRIPTION_LENGTH', 100);

/**
 * Highlights all occurrences of the search query within the given text.
 *
 * @param string $text The original text.
 * @param string $query The search query.
 * @return string The text with highlighted matches.
 */
function highlightMatches($text, $query) {
    // Escape special characters to prevent XSS
    $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    
    // Escape special regex characters in the query
    $escapedQuery = preg_quote($query, '/');
    
    // Split the query into individual words for multi-word highlighting
    $queryWords = explode(' ', $escapedQuery);
    
    foreach ($queryWords as $word) {
        if (!empty($word)) {
            // Use word boundaries to highlight whole words only, case-insensitive
            $pattern = '/(' . $word . ')/i';
            $replacement = '<span class="search-suggestions-highlight">$1</span>';
            $escapedText = preg_replace($pattern, $replacement, $escapedText);
        }
    }
    
    return $escapedText;
}

/**
 * Safely truncates text (plain text) to the given max length, 
 * then appends '...' if it was truncated.
 *
 * @param string $text
 * @param int    $maxLength
 * @return string The truncated text (plain).
 */
function truncateText($text, $maxLength) {
    // Remove any HTML tags first for measuring length in plain text
    $plain = strip_tags($text);
    
    if (strlen($plain) <= $maxLength) {
        // No need to truncate
        return $plain;
    }
    
    // Substring to max length
    $truncated = substr($plain, 0, $maxLength);
    
    // Cut off at the last space to avoid mid-word cut
    $lastSpace = strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }
    
    return $truncated . '...';
}

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';

    if ($query === '') {
        // Empty query, return no suggestions
        echo '';
        exit;
    }

    // Fetch search results using your searchData function
    $results = $productFun->searchData('products', $query);

    // Start output buffering to include CSS styles
    ob_start();
    ?>
    <style>
        /* Main container for suggestions */
        .search-suggestions-list {
            position: absolute;
            background-color: #fff;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            margin-top: 5px;
        }

        /* Each suggestion item row */
        .search-suggestions-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .search-suggestions-item:last-child {
            border-bottom: none;
        }

        /* Link container for each suggestion */
        .search-suggestions-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        /* Icon next to suggestion text */
        .search-suggestions-icon {
            margin-right: 10px;
            color: #555;
            font-size: 1.2em;
        }

        /* The text container */
        .search-suggestions-text {
            display: flex;
            flex-direction: column;
        }

        /* Product Name styling */
        .search-product-name {
            font-weight: bold;
            font-size: 1em;
            color: #333;
        }

        /* Product Description styling */
        .search-product-description {
            font-size: 0.9em;
            color: #777;
            margin-top: 2px;
        }

        /* Highlighted text for search matches */
        .search-suggestions-highlight {
            background-color: #ffc107;
            font-weight: bold;
            color: #212529;
            border-radius: 2px;
            padding: 0 2px;
        }

        /* "No results" styling */
        .search-suggestions-noresults {
            color: #d9534f; /* e.g., Bootstrap's danger color */
            font-weight: bold;
        }

        /* Hover effect on each item */
        .search-suggestions-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    <?php

    if ($results) {
        // Changed the container class/id
        echo '<div class="search-suggestions-list" id="searchSuggestionsWrapper">';

        foreach ($results as $result) {
            // Build link like:
            // "http://example.com/category.php?search=Product+Name&pid=encryptedId&slug=product-slug&location=location-id"
            $productIdEncrypted = urlencode($security->encrypt($result['id']));
            $productSlug        = urlencode($result['slug']);
            
            // Raw strings for name/description
            $productNameRaw        = $result['name'] ?? '';
            $productDescriptionRaw = $result['description'] ?? '';

            // 1) Truncate the raw description
            $truncatedDescriptionPlain = truncateText($productDescriptionRaw, MAX_DESCRIPTION_LENGTH);

            // 2) Highlight the truncated text
            $productDescription = highlightMatches($truncatedDescriptionPlain, $query);

            // Also highlight the product name
            $productName = highlightMatches($productNameRaw, $query);

            // Build the search link
            // We pass the raw product name for the 'search' param so it matches user input, but feel free to adjust
            $link = "{$urlval}category.php?search=" . urlencode($productNameRaw) 
                  . "&pid={$productIdEncrypted}&slug={$productSlug}";
            if ($location !== '') {
                $link .= "&location=" . urlencode($location);
            }

            echo '<div class="search-suggestions-item">';
            echo "  <a href=\"{$link}\" class=\"search-suggestions-link\">";
            echo '      <i class="fa fa-search search-suggestions-icon"></i>';
            echo '      <div class="search-suggestions-text">';
            echo "          <span class=\"search-product-name\">{$productName}</span>";
            echo "          <span class=\"search-product-description\">{$productDescription}</span>";
            echo '      </div>';
            echo '  </a>';
            echo '</div>';
        }

        echo '</div>';
    } else {
        // No results => Link to search not found page
        $notFoundLink = "{$urlval}searchnotfound.php?search=" . urlencode($query);
        echo '<div class="search-suggestions-item">';
        echo "  <a href=\"{$notFoundLink}\" class=\"search-suggestions-link\">";
        echo '      <i class="fa fa-search search-suggestions-icon"></i>';
        echo "      <span class=\"search-suggestions-noresults\">No results found for: <strong>"
                . htmlspecialchars($query) 
                . "</strong></span>";
        echo '  </a>';
        echo '</div>';
    }

    // Flush the output buffer
    ob_end_flush();
}
?>
