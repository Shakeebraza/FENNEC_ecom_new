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
.noproductfound{
    width: 100% !important;
    margin: 0px !important;
}
</style>
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12 d-flex justify-content-between mb-4">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center ">
                <div class="d-flex align-items-center mb-4">
                    <h1 class="me-2">Relative Products</h1>
                    
                   

                </div>
            </div>

            <div class="container ">
                <div
                    id="product-grid"
                    class="row row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php

                        $productFind = $productFun->getProductsWithDetails(1, 4, $filterConditions);
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
                                              
                                           
                                                ';
                                            }
                                                echo'
                                            </div>
                                        </div>
                                    </div>
                                </a>';
                        }
                        }else{
                            $productFind = $productFun->getProductsWithDetails(1, 3);
                            $products = $productFind['products'];;
                            foreach ($products as $proval) {
                                $description = $proval['description'];
                                $namePro = $proval['name'];
    
                                $Wordsh = explode(" ", $namePro);
                                $name = count($Wordsh) > 3 ? implode(" ", array_slice($Wordsh, 0, 3)) . '...' : $namePro;
    
                                $words = explode(" ", $description);
                                $description = count($words) > 3 ? implode(" ", array_slice($words, 0, 3)) . '...' : $description;
    
                                echo '
                                    <a href="' . $urlval . 'detail.php?slug=' . $proval['slug'] . '">
                                        <div class="col-12">
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
                                                   
                                                    ';
                                                }
                                                    echo'
                                                </div>
                                            </div>
                                        </div>
                                    </a>';
                            }


                        }
                        ?>



                </div>
            </div>
        </div>
       

    </div>
</div>

</body>

</html>