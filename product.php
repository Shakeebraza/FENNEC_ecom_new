<?php
require_once 'global.php';
include_once 'header.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $location = $_GET['location'] ?? null;
    $min_price = $_GET['min_price'] ?? null;
    $max_price = $_GET['max_price'] ?? null;
    $subcategories = $_GET['subcategory'] ?? null;

    $filterConditions = [];

    if (!is_null($location)) {
        $filterConditions['city'] = $location;
    }

    if ($min_price > 0) {
        $filterConditions['min_price'] = (float)$min_price;
    }

    if ($max_price < PHP_INT_MAX) {
        $filterConditions['max_price'] = (float)$max_price; 
    }

    if (!empty($subcategories)) {
        $filterConditions['subcategory'] = $subcategories; 
    }
}

?>
<style>
    .btn-sell-car:hover {
        background-color: white;
        color: #00494F;
        border: 1px solid #00494F;
    }
    .custom-category {
    cursor: pointer;
    padding: 10px;
}

.subcategory-dropdown {
    margin-top: 5px;
    padding-left: 15px;
    background-color: #f5f5f5;
    border-left: 2px solid #ccc;
}

.subcategory-item {
    padding: 5px;
}
.custom-category {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    margin: 5px 0;
    transition: background-color 0.3s;
    cursor: pointer;
}

.custom-category:hover {
    background-color: #f0f0f0;
}

.category-title {
    font-weight: 603;
  font-size: 16px;
    margin: 0;
}

.subcategory-dropdown {
    margin-top: 5px;
    padding-left: 20px; 
}

.subcategory-item {
    margin: 5px 0;
    font-size: 1em;
}

.subcategory-item input {
    margin-right: 5px;
}
</style>
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12 d-flex justify-content-between mb-4">
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center ">
                <div class="d-flex align-items-center mb-4">
                    <span class="me-2">VIEW AS</span>
                    <div
                        class="view-option icon-list"
                        data-cols="1"
                        title="List view">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </div>
                    <div
                        class="view-option icon-grid-2"
                        data-cols="2"
                        title="Two column grid">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </div>
                    <div
                        class="view-option icon-grid-3 active"
                        data-cols="3"
                        title="Three column grid">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </div>
                </div>
            </div>

            <div class="container ">
                <div
                    id="product-grid"
                    class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php

                        $productFind = $productFun->getProductsWithDetails(1, 16, $filterConditions);
                        $products = $productFind['products'];
                        if(!empty($products)){
                        foreach ($products as $proval) {
                            $description = $proval['description'];
                            $namePro = $proval['name'];

                            $Wordsh = explode(" ", $namePro);
                            $name = count($Wordsh) > 3 ? implode(" ", array_slice($Wordsh, 0, 3)) . '...' : $namePro;

                            $words = explode(" ", $description);
                            $description = count($words) > 3 ? implode(" ", array_slice($words, 0, 3)) . '...' : $description;

                            echo '
                                <a href="' . $urlval . 'detail.php?slug=' . $proval['slug'] . '">
                                    <div class="col">
                                        <div class="product-card">
                                            <img
                                                src="' . $proval['image'] . '"
                                                class="card-img-top"
                                                alt="' . $name . '"
                                            />
                                            <div class="heart-icon">
                                                <i class="fas fa-heart"></i>
                                            </div>
                                            <div class="card-body">
                                                <div class="p-3">
                                                    <h5 class="card-title">' . $name . '</h5>
                                                    <p class="card-text">' . $description . '</p>
                                                    <p class="text-muted">' . $proval['country'] . ' | ' . $proval['city'] . '</p>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="product-price" style="color: red;">$' . $proval['price'] . '</span>
                                                        <span class="product-time small" style="font-size: 12px;">' . $proval['date'] . '</span>
                                                    </div>
                                                </div>';
                                            if(isset($_SESSION['userid'])){
                                           echo' 
                                                <button class="_91e21052 e07f63ca af478541 btn quick-add-btn" type="submit" style="padding: 5px 10px; font-size: 0.8em;">
                                                    <svg viewBox="0 0 24 24" class="b4840212 _545e587d" style="width: 14px; height: 14px; vertical-align: middle;">
                                                        <path fill-rule="evenodd" d="M7 18h6a7 7 0 0 0 0-14h-2a7 7 0 0 0-7 7v8.5l2.6-1.3.4-.1zm4-16h2a9 9 0 0 1 0 18H7.2l-3.8 2L2 21V11c0-5 4-9 9-9zm-4 9a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm5-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm3 1a1 1 0 1 1 2 0 1 1 0 0 1-2 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="_30de236c af478541 b7af14b4" style="font-size: 0.8em;">Chat</span>
                                                </button>
                                                ';
                                            }
                                                echo'
                                            </div>
                                        </div>
                                    </div>
                                </a>';
                        }
                        }else{
                            echo '
                            <div class="noproductfound"style="display: flex;justify-content: center;align-content: center;margin: auto;color: #00494f;gap: 31px;text-align: center;font-weight: bold;">
                            <p>No find a single product</p>
                        </div>
                            ';
                        }
                        ?>



                </div>
            </div>
        </div>
        <div class="col-md-3 left-side">
        <div class="bg-light p-4 rounded">
            <form id="filterForm" method="GET" action="">
                <div class="mb-4">
                    <h5>Location</h5>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <select id="country-city-select" name="location" class="form-control">
                            <option value="">Select Country | City</option>
                            <?php
                            $countryCityPairs = $productFun->getCountryCityPairs();
                            foreach ($countryCityPairs as $pair) {
                                echo '<option value="' . $pair['city_id'] . '" 
                                            data-country-id="' . $pair['country_id'] . '" 
                                            data-city-id="' . $pair['city_id'] . '">
                                            ' . $pair['country_name'] . ' | ' . $pair['city_name'] . '
                                    </option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-sell-car ms-3 w-50 custom-button">Search</button>
                </div>

                <div class="">
                    <h5>Category</h5>
                    <div class="ms-3">
                       
                    <?php
                    $findCate = $productFun->getAllcatandSubcat();

                    if ($findCate['status'] == 'success') {
                        foreach ($findCate['data'] as $index => $category) {
                            echo '
                                <div class="custom-category" onclick="toggleSubcategory(' . $index . ')">
                                    <p class="category-title">' . htmlspecialchars($category['category_name']) . '</p>
                                    <div id="subcategory-' . $index . '" class="subcategory-dropdown" style="display: none;">
                            ';

                            if (!empty($category['subcategories'])) {
                                foreach ($category['subcategories'] as $subcategory) {
                                    echo '<div class="subcategory-item">
                                            <label>
                                                <input type="radio" name="subcategory" value="' . htmlspecialchars($subcategory['id']) . '">
                                                ' . htmlspecialchars($subcategory['subcategory_name']) . '
                                            </label>
                                        </div>';
                                }
                            } else {
                                echo '<p class="subcategory-item">No subcategories</p>';
                            }

                            echo '  </div>
                                </div>';
                        }
                    }
                    ?>

                    </div>
                    
                    <hr>
                    <div class="mt-5">
                        <div class="card" style="max-width: 300px;">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Price Range</h5>
                                <div class="mb-3">
                                    <label for="minPrice" class="form-label">Minimum Price:</label>
                                    <input type="number" name="min_price" class="form-control" id="minPrice" placeholder="Enter minimum price" min="0" step="1000">
                                </div>
                                <div class="mb-3">
                                    <label for="maxPrice" class="form-label">Maximum Price:</label>
                                    <input type="number" name="max_price" class="form-control" id="maxPrice" placeholder="Enter maximum price" min="0" step="1000">
                                </div>
                                <button type="submit" class="btn btn-sell-car w-100">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</div>

    </div>
</div>
<?php
include_once 'footer.php';
?>
<script>
    function toggleSubcategory(index) {
    const subcategoryDiv = document.getElementById('subcategory-' + index);
    if (subcategoryDiv.style.display === 'none') {
        subcategoryDiv.style.display = 'block';
    } else {
        subcategoryDiv.style.display = 'none';
    }
}
</script>
</body>

</html>