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
            $replacement = '<span class="highlight">$1</span>';
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

    // Fetch search results using the searchData function
    $results = $productFun->searchData('products', $query);

    // Start output buffering to include CSS styles
    ob_start();
    ?>
    <style>
        /* Suggestions Container */
        .suggestions {
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

        /* Individual Suggestion Item */
        .suggestion-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        /* Suggestion Link */
        .suggestion-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        /* Search Icon */
        .icon-search {
            margin-right: 10px;
            color: #555;
            font-size: 1.2em;
        }

        /* Suggestion Text */
        .suggestion-text {
            display: flex;
            flex-direction: column;
        }

        /* Product Name */
        .product-name {
            font-weight: bold;
            font-size: 1em;
            color: #333;
        }

        /* Product Description */
        .product-description {
            font-size: 0.9em;
            color: #777;
            margin-top: 2px;
        }

        /* Highlighted Text */
        .highlight {
            background-color: #ffc107; /* Amber background */
            font-weight: bold;
            color: #212529;
            border-radius: 2px;
            padding: 0 2px;
        }

        /* No Results Message */
        .no-results {
            color: #d9534f; /* Bootstrap's danger color */
            font-weight: bold;
        }

        /* Hover Effect */
        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    <?php

    if ($results) {
        echo '<div class="suggestions" id="suggestions">';

        foreach ($results as $result) {
            // Build link like:
            // "http://example.com/category.php?search=Product+Name&pid=encryptedId&slug=product-slug&location=location-id"
            $productIdEncrypted = urlencode($security->encrypt($result['id']));
            $productSlug = urlencode($result['slug']);
            
            // Raw strings for name/description
            $productNameRaw        = $result['name'] ?? '';
            $productDescriptionRaw = $result['description'] ?? '';

            // 1) First, TRUNCATE the description (plain text)
            $truncatedDescriptionPlain = truncateText($productDescriptionRaw, MAX_DESCRIPTION_LENGTH);

            // 2) THEN highlight the truncated text
            $productDescription = highlightMatches($truncatedDescriptionPlain, $query);

            // Also highlight the product name
            $productName = highlightMatches($productNameRaw, $query);

            // Build the search link
            // Use raw product name for "search" param if you want to replicate their typed name,
            // or you could use $productNameRaw. 
            $link = "{$urlval}category.php?search=" . urlencode($productNameRaw) 
                  . "&pid={$productIdEncrypted}&slug={$productSlug}";
            if ($location !== '') {
                $link .= "&location=" . urlencode($location);
            }

            echo '<div class="suggestion-item">';
            echo "  <a href=\"{$link}\" class=\"suggestion-link\">";
            echo '      <i class="fa fa-search icon-search"></i>';
            echo '      <div class="suggestion-text">';
            echo "          <span class=\"product-name\">{$productName}</span>";
            echo "          <span class=\"product-description\">{$productDescription}</span>";
            echo '      </div>';
            echo '  </a>';
            echo '</div>';
        }

        echo '</div>';
    } else {
        // No results => Link to search not found page
        $notFoundLink = "{$urlval}searchnotfound.php?search=" . urlencode($query);
        echo '<div class="suggestion-item">';
        echo "  <a href=\"{$notFoundLink}\" class=\"suggestion-link\">";
        echo '      <i class="fa fa-search icon-search"></i>';
        echo "      <span class=\"no-results\">No results found for: <strong>" . htmlspecialchars($query) . "</strong></span>";
        echo '  </a>';
        echo '</div>';
    }

    // Flush the output buffer
    ob_end_flush();
}
?>
