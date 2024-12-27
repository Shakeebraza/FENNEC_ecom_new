<?php
require_once 'global.php';
include_once 'header.php';



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $location = $_GET['location'] ?? null;
    $city = $_GET['city'] ?? null;
    $min_price = $_GET['min_price'] ?? null;
    $max_price = $_GET['max_price'] ?? null;
    $subcategories = $_GET['subcategory'] ?? null;
    $pId = $_GET['pid'] ?? null;
    $slug = $_GET['slug'] ?? null;
    $sort = $_GET['sort'] ?? null;
    $search = $_GET['search'] ?? null;

    $filterConditions = [];

    if (!is_null($location)) {
        $filterConditions['country'] = $location;
    }
    if(isset($search)){
        $filterConditions['product_name'] = $search;
        
    }
    if (!is_null($location)) {
        $filterConditions['city'] = $city;
    }
    
    if ($min_price > 0) {
        $filterConditions['min_price'] = (float)$min_price;
    }
    
    if ($max_price < PHP_INT_MAX && $max_price > 0) {
        $filterConditions['max_price'] = (float)$max_price; 
    }
    
    if (!empty($slug)) {
        $query = "SELECT id FROM categories WHERE slug = :slug LIMIT 1";
        $stmt = $pdo->prepare($query);
        
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $filterConditions['category'] = $row_data['id'];
        }        
        
    }else{
        $row_data= $dbFunctions->getDatanotenc('cities', "id ='$location'");
        
    }
    if(!empty($subcategories)){
        $filterConditions['subcategory'] = $subcategories; 
    }
    if(!empty($pId)){
        $filterConditions['pid'] = $security->decrypt($pId); 

    }
    // $filterConditions['pid'] = $security->decrypt($pId); 
    if(!empty($sort)){
        $filterConditions['sort'] = $sort;

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
@media (max-width: 1200px) {
            .filter-container {
                display: none;
            }
            .filter-btn {
                display: block;
            }
            .open-btn{
                display: block !important;
            }
            .left-side{
                display: none;
            }


.close-modal {
    color: #aaa; 
    float: right; 
    font-size: 28px;
    font-weight: bold; 
}

.close-modal:hover,
.close-modal:focus {
    color: black; 
    text-decoration: none; 
    cursor: pointer;
}


.filter-btn {
    background-color: #00494f; 
    color: white; 
    padding: 10px 20px; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
    font-size: 16px; 
    transition: background-color 0.3s; 
}

.filter-btn:hover {
    background-color: #0056b3;
}


.btn {
    background-color: #00494f;
    color: white;
    padding: 10px 20px;
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
    font-size: 16px; 
    transition: background-color 0.3s; 
    margin-top: 10px; 
}

.btn:hover {
    background-color: #218838; 
}


.category-title {
    font-weight: bold;
    margin: 10px 0; 
}


.subcategory-dropdown {
    padding-left: 15px; 
    margin-top: 5px; 
}


.subcategory-item {
    margin: 5px 0; 
}
#openFilterModalBtn{
    margin-bottom: 10px;
}
.button-container {
    display: flex;
    justify-content: flex-end; 
    margin: 10px;
}
.styled-select {
    appearance: none; 
    -webkit-appearance: none; 
    -moz-appearance: none;
    padding: 10px 15px; 
    font-size: 16px;
    border: 2px solid #007bff; 
    border-radius: 5px; 
    background-color: #f8f9fa;
    color: #333; 
    width: 100%;
    max-width: 100%; 
    cursor: pointer; 
    transition: border-color 0.3s, background-color 0.3s; 
}


.styled-select::after {
    content: '\f0d7';
    font-family: 'Font Awesome 5 Free'; 
    font-weight: 900; 
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
}


.styled-select:hover {
    border-color: #0056b3; 
}


.styled-select:focus {
    border-color: #0056b3; 
    outline: none;
    background-color: #fff; 
}


.styled-select option {
    padding: 10px;
    background-color: #fff; 
    color: #333; 
}

/* Style for option hover */
.styled-select option:hover {
    background-color: #e9ecef; 
}
        }

            .modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0, 0, 0, 0.7); 
}

.modal-content {
    background-color: #fefefe; 
    margin: 15% auto; 
    padding: 20px;
    border: 1px solid #888; 
    width: 90%; 
    max-width: 500px; 
    border-radius: 8px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

 

    .custom-dropdown .btn-outline-primary {
        background-color: #00494f;
        color: white;
        border: 2px solid #00494f;
        font-weight: bold;
        transition: all 0.6s ease;
    }

    .custom-dropdown .btn-outline-primary:hover {
        background-color: white;
        color: #00494f;
        border-color: #00494f;
    }


    .custom-dropdown .dropdown-item {
        background-color: white;
        color: #00494f;
        font-weight: bold;
        transition: all 0.3s ease;
        padding:10px;
    }

    .custom-dropdown .dropdown-item:hover {
        background-color: #00494f;
        color: white;
    }


    .custom-dropdown .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #00494f;
        width: 200px !important;
        margin-top:1px;
        /* margin: auto; */
    }
    #sortDropdown:hover .custom-dropdown{
        display:block;
    }
 
</style>

<?php
$banner = $fun->getRandomBannerByPlacement('category_header');
if(!empty($banner)){
?>
<div style="width: 100%;  display: flex; justify-content:center; margin: 0 auto; padding:14px 16px; align-items: center;">
    <div style="width: 1000px; background-color: <?php echo $banner['bg_color']; ?>; overflow: hidden; position: relative; padding: 24px 16px;">
        <div style="display: flex; justify-content: space-between; max-width: 1200px; background-color: <?php echo $banner['bg_color']; ?>; margin: 0 auto; padding: 24px 16px; align-items: center;">

            <div style="display: flex; align-items: center; gap: 16px; width: 70%; ">
   
                <div style="border: 2px solid transparent; background: url('<?php echo $banner['image']; ?>') no-repeat; background-size: contain; width: 200px; height: 120px;"></div>

                <div style="margin-left: 16px; width: 100%;">
                
                    <h2 style="font-size: 14px; font-weight: 600; color: <?php echo $banner['text_color']; ?>;"><?php echo $banner['title']; ?></h2>
                    <p style="font-size: 20px; font-weight: 700; color: <?php echo $banner['text_color']; ?>;"><?php echo $banner['description']; ?></p>
                </div>
            </div>

            <div style="display: flex; align-items: center; width: 30%;">
              
                <?php if ($banner['btn_text'] && $banner['btn_url']) : ?>
                    <a href="<?php echo $banner['btn_url']; ?>" target="_blank" style="background-color: <?php echo $banner['btn_color']; ?>; padding: 12px 24px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background-color 0.3s ease;">
                        <span style="font-size: 14px; font-weight: 500;"><?php echo $banner['btn_text']; ?></span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12 d-flex justify-content-between mb-4">
        </div>
    </div>

    <div class="button-container">
        <button class="filter-btn open-btn" id="openFilterModalBtn" style="display:none;">
            <i class="fas fa-filter"></i> Filters
        </button>
    </div>


        <div class="modal" id="filterModal">
    <div class="modal-content">
        <span class="close-modal" id="closeModalBtn">&times;</span>
        <h5>Filter Options</h5>
        <form id="mobileFilterForm" method="GET" action="">

            <h5>Location</h5>
            <select name="location" required class="styled-select">
                <?php
                $countryCityPairs = $productFun->getCountryCityPairs();
                foreach ($countryCityPairs as $pair) {
                    echo '<option value="' . $pair['city_id'] . '" 
                                data-country-id="' . $pair['country_id'] . '" 
                                data-city-id="' . $pair['city_id'] . '">
                                ' . htmlspecialchars($pair['country_name']) . ' | ' . htmlspecialchars($pair['city_name']) . '
                          </option>';
                }
                ?>
            </select>

            <h5>Sub Category</h5>
            <div class="categories-container"> 
            <?php
                $findCate = $productFun->getAllcatandSubcat($row_data['id']);

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

                        echo '      </div>
                            </div>';
                    }
                }
            ?>

                                    <div class="mt-5">
                        <div class="card" style="max-width: 100%;">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><?= $lan['pricerange']?></h5>
                                <div class="mb-3">
                                    <label for="minPrice" class="form-label"><?= $lan['minimum_Price']?></label>
                                    <input type="number" name="min_price" class="form-control" id="minPrice" placeholder="<?= $lan['enter']?><?= $lan['minimum_Price']?>" min="0" step="500">
                                </div>
                                <div class="mb-3">
                                    <label for="maxPrice" class="form-label"><?= $lan['maximum_price']?></label>
                                    <input type="number" name="max_price" class="form-control" id="maxPrice" placeholder="<?= $lan['enter']?><?= $lan['maximum_price']?>" min="0" step="500">
                                </div>
                              
                            </div>
                        </div>
                    </div>
            </div> 
            
            <button type="submit" class="btn">Apply Filters</button>
        </form>
    </div>
        </div>



    <div class="row">
    <div class="col-md-3 left-side">
        <div class="bg-light p-4 rounded">
        <form id="filterForm" method="GET" action="">
        <div class="mb-4">
                <h5>Country</h5>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    
                    <select id="country" name="location" class="form-control">
                        <option value="">Select Country</option>
                        <?php
                        $selectedCountry = isset($_GET['location']) ? $_GET['location'] : ''; 
                        $countries = $dbFunctions->getData('countries');
                        foreach ($countries as $cont) {
                            $countryId = $security->decrypt($cont['id']);
                            $selected = ($countryId == $selectedCountry) ? 'selected' : '';
                            echo '<option value="' . $countryId . '" ' . $selected . '>' . $security->decrypt($cont['name']) . '</option>';
                        }
                        ?>
                    </select>
                    
                </div>
            </div>

                <h5>City</h5>
            <div class="mb-4">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
               
                    <select id="city" name="city" class="form-control w-20">
                        <option value="">Select City</option>
                        <?php
               
                        $selectedCity = isset($_GET['city']) ? $_GET['city'] : '';

                        if (!empty($selectedCountry)) {
                            $cities = $productFun->getCities($selectedCountry); 
                            foreach ($cities as $city) {
              
                                $selected = ($city['id'] == $selectedCity) ? 'selected' : '';
                                echo '<option value="' . $city['id'] . '" ' . $selected . '>' . $city['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-sell-car w-20">Find</button>
                </div>
            </div>

            
    <div class="mb-4">
        <h5><?= $lan['sub_category'] ?></h5>
        <div class="ms-3">
            <?php
            $selectedSubcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
            $findatacatandsubcat = $productFun->getCategoriesWithChildren();

            if ($findatacatandsubcat['status'] == 'success') {
                $categories = $findatacatandsubcat['data'];
                foreach ($categories as $index => $category) {
                    echo '
                    <div class="custom-category" onclick="toggleSubcategory(' . $index . ')">
                        <p class="category-title">' . htmlspecialchars($category['category_name']) . '</p>
                        <div id="subcategory-' . $index . '" class="subcategory-dropdown" style="display: none;">';

                    foreach ($category['children'] as $subcategory) {
                        $checked = $selectedSubcategory == $subcategory['id'] ? 'checked' : '';
                        echo '<div class="subcategory-item">
                                <label>
                                    <input type="radio" name="subcategory" value="' . htmlspecialchars($subcategory['id']) . '" ' . $checked . '>
                                    ' . htmlspecialchars($subcategory['subcategory_name']) . '
                                </label>
                            </div>';
                    }

                    echo '</div></div>';
                }
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>
        </div>
    </div>


    <div class="card mb-4" style="max-width: 300px;">
        <div class="card-body">
            <h5 class="card-title mb-3">Price Range</h5>
            <div class="mb-3">
                <label for="minPrice" class="form-label">Minimum Price:</label>
                <input type="number" name="min_price" class="form-control" id="minPrice" 
                       placeholder="Enter minimum price" min="0" step="1" 
                       value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="maxPrice" class="form-label">Maximum Price:</label>
                <input type="number" name="max_price" class="form-control" id="maxPrice" 
                       placeholder="Enter maximum price" min="0" step="1" 
                       value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
            </div>
        </div>
    </div>


    <!-- <div class="mb-4" style="max-width: 300px;">
       
        <div class="btn-group w-100">
            <button type="button" class="btn btn-outline-primary" onclick="setSort('oldest')">Oldest</button>
            <button type="button" class="btn btn-outline-primary" onclick="setSort('newest')">Newest</button>
        </div>
     
        <input type="hidden" name="sort" id="sortInput" value="<?= isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : '' ?>">
    </div> -->


    <button type="submit" class="btn btn-sell-car w-100"><?= $lan['search'] ?></button>
        </form>
        </div>

        <div class="col-md-12 mb-4 mt-4">

  <div class="sidebar-box" style="box-shadow: 4px 3px 6px #A4A4A485; padding: 20px; background-color: white; border: 2px solid #198754;">
    <h5 class="text-center" style="color: #198754;"><?= $lan['premium_products']?></h5>
    <div class="slider" style="background-color: #fef5e6; padding: 10px;">
      <?php
        $productMultipalinPre = $productFun->PoplarProductperMultipal();
        if($productMultipalinPre){
            foreach($productMultipalinPre as $row){
                $imgproductpre = $urlval . $row['image']; 
                $detailsurl=$urlval."detail.php?slug=".$row['slug'];
                $productName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); 

                echo '
                    <div>
                        <a href="'.$detailsurl.'">
                            <img src="' . $imgproductpre . '" alt="' . $productName . '" class="img-fluid">
                        </a>
                        <h6 class="text-center" style="color: #198754;">' . $productName . '</h6>
                    </div>
                ';
            }
        } else {
            echo '
                <div>
                    <h6 class="text-center" style="color: #198754;">Not a single product</h6>
                </div>
            ';
        }

      
      ?>
    </div>
  </div>

        </div>

    <?php
        $banner = $fun->getRandomBannerByPlacement('category_sidebar');
        if (!empty($banner)) {
        ?>

        <div style="background-color: <?php echo $banner['bg_color']; ?>; color: <?php echo $banner['text_color']; ?>; min-height: 50vh; position: relative;width: 15vw; display: flex; flex-direction: column; padding: 2rem;">
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $banner['title']; ?></h1>
                <h2 style="font-size: 3rem; font-weight: 300;  color: <?php echo $banner['text_color']; ?>"><?php echo $banner['description']; ?></h2>               
                <img src="<?php echo $banner['image']; ?>" alt="" style="width: 100%; height: 280px; margin-bottom:20px; border-radius:7px;">
            </div>

 
            <?php if ($banner['btn_text'] && $banner['btn_url']) : ?>
                <a href="<?php echo $banner['btn_url']; ?>" style="background-color: <?php echo $banner['btn_color']; ?>; padding: 1rem 2rem; color: black; text-decoration: none; display: flex; align-items: center; justify-content: space-between; width: fit-content; margin-top: auto; transition: background-color 0.3s ease;">
                    <?php echo $banner['btn_text']; ?>
                    <span style="margin-left: 1rem;">→</span>
                </a>
            <?php endif; ?>
        </div>

        <?php
        }
        ?>


        </div>
        <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mobileres mb-4">

            <div class="d-flex align-items-center">
                <span class="me-2"><?= $lan['view_as'] ?></span>
                <div
                    class="view-option icon-list"
                    data-cols="1"
                    title="List view"
                    onclick="setViewMode('list')">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
                <div
                    class="view-option icon-grid-2"
                    data-cols="2"
                    title="Two column grid"
                    onclick="setViewMode('grid-2')">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
                <div
                    class="view-option icon-grid-3 active"
                    data-cols="3"
                    title="Three column grid"
                    onclick="setViewMode('grid-3')">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
            </div>

        
            <div class="dropdown custom-dropdown" style="max-width: 300px;">
                    <button
                        class="btn btn-outline-primary dropdown-toggle w-100"
                        type="button"
                        id="sortDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="border-radius: 5px"
                        >
                        Sort By
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li>
                            <button class="dropdown-item" onclick="setSort('oldest')">Most recent first</button>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="setSort('pricelowtohigh')">Price: Low to High</button>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="setSort('pricehightolow')">Price: High to Low</button>
                        </li>
                        <li>
                            <button class="dropdown-item" onclick="setSort('newest')">Nearest first</button>
                        </li>
                    </ul>
                    <input
                        type="hidden"
                        name="sort"
                        id="sortInput"
                        value="<?= isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : '' ?>">
                </div>
            </div>


            <div class="container ">
                <div
                    id="product-grid"
                    class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php
                    

                        $productFind = $productFun->getProductsWithDetailsn2(1, 500000, $filterConditions);
                        $products = $productFind['products'];
                  
                        if(!empty($products)){
                        foreach ($products as $proval) {
                            $description = $proval['description'];
                            $namePro = $proval['name'];

                            $Wordsh = explode(" ", $namePro);
                            $name = count($Wordsh) > 3 ? implode(" ", array_slice($Wordsh, 0, 3)) . '...' : $namePro;

                            $words = explode(" ", $description);
                            $description = count($words) > 3 ? implode(" ", array_slice($words, 0, 3)) . '...' : $description;
                            $setSession = $fun->isSessionSet();
                            $fav = ""; 
                            
                            if ($setSession == true) {
                                $uid = base64_decode($_SESSION['userid']);
                                $pid = $proval['id'];
                                $isFav = $dbFunctions->getDatanotenc('favorites', "user_id = '$uid' AND product_id = '$pid'");
                                
                                if ($isFav) {
                                    $fav = "style='color: red'"; 
                                }
                            }

                            echo '
                                
                                    <div class="col">
                                        <div class="product-card">
                                            <img
                                                src="' . $proval['image'] . '"
                                                class="card-img-top"
                                                alt="' . $name . '"
                                            />';
                                      
                                          if($proval['product_type'] == "standard"){
                                            echo '<div class="watermark">'. $title.'</div>';
  
                                          }
                                     
                                            echo'<div class="heart-icon">';
                                    if(isset($_SESSION['userid'])){
                                        echo'
                                             <a
                                            class="heart-icon icon_heart"
                                            data-productid="'. $proval['id'] .'"
                                            id="favorite-button-'.$proval['id'] .'">
                                            <i class="fas fa-heart" '.$fav .'></i>
                                        </a>
                                        ';
                                    }else{
                                        echo'
                                        <a class="heart-icon" href="'.$urlval.'LoginRegister.php">
                                            <i class="fas fa-heart"></i>
                                        </a>
                                        
                                        ';
                                    }
                                                               
                                            
                                        echo'</div>
                                            <div class="card-body">
                                            <a href="' . $urlval . 'detail.php?slug=' . $proval['slug'] . '">
                                                <div class="p-3">
                                                    <h5 class="card-title">' . $name . '</h5>
                                                    <p class="card-text">' . $description . '</p>
                                                    <p class="text-muted">' . $proval['country'] . ' | ' . $proval['city'] . '</p>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="product-price" style="color: red;">'.$fun->getFieldData('site_currency').'' . $proval['price'] . '</span>
                                                        <span class="product-time small" style="font-size: 12px;">' . $proval['date'] . '</span>
                                                    </div>
                                                </div>
                                                </a>';
                                            if(isset($_SESSION['userid'])){
                                              
                                                // if(base64_decode($_SESSION['userid']) != $proval['prouserid']){

                                                //     echo ' 
                                                //     <button class="btn quick-add-btn" type="button" style="background: #1987546e; border: none; padding: 5px;" 
                                                //     onclick="startChat(\'' . $security->encrypt($proval['id']) . '\')">
                                                //     <i class="fas fa-comment-dots" style="font-size: 1.2em; color: #00494f;"></i> '.$lan['chat'].' 
                                                //     </button>';
                                                // }else{
                                                //     echo '
                                                //     <p style="text-align: center;color: #00494f;">'.$lan['your_product'].'<p>
                                                //     ';
                                                // }
                                                }
                                                echo'
                                            </div>
                                        </div>
                                    </div>
                                ';
                        }
                        }else{
                            echo '
                           <div style="text-align: center; margin: 50px auto;">
                            <div class="noproductfound"style="display: flex;justify-content: center;align-content: center;margin: auto;color: #00494f;gap: 31px;text-align: center;font-weight: bold;">
                                                        <p>No find a single product</p>
                                                    </div>
                                <p style="font-size: 1.2rem; color: #555; margin-top: 10px;">
                                    Check the spelling<br>
                                    Try adding or removing words<br>
                                    Try browsing the categories<br>
                                </p>

                                <p style="font-size: 1.1rem; color: #555; margin-top: 20px;">
                                    Save this search to receive email alerts and notifications when new items are available.
                                </p>

                                <a href="<?= $urlval?>index.php" style="text-decoration: none; display: inline-block; margin-top: 20px;">
                                    <button style="padding: 10px 20px; font-size: 1rem; background-color: #00494f; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                                        Back to Home
                                    </button>
                                </a>
                            </div>
                            ';
                        }
                        ?>



                </div>
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
document.getElementById('openFilterModalBtn').onclick = function() {
            document.getElementById('filterModal').style.display = 'flex';
        };

        document.getElementById('closeModalBtn').onclick = function() {
            document.getElementById('filterModal').style.display = 'none';
        };

        window.onclick = function(event) {
            var modal = document.getElementById('filterModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        function startChat(productId) {
    $.ajax({
        url: '<?= $urlval ?>ajax/start_chat.php',
        type: 'POST',
        dataType: 'json',  
        data: { product_id: productId },  
        success: function(response) {
            if (response && response.success) {
                window.location.href = '<?= $urlval ?>msg.php';
            } else {
                alert(response.message || 'Could not start chat.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Error connecting to chat. Please try again.');
        }
    });
}

document.querySelectorAll('.icon_heart').forEach(favoriteButton => {
    favoriteButton.addEventListener('click', function(event) {
        event.preventDefault(); 
        const productId = this.getAttribute('data-productid');

        fetch('<?= $urlval ?>ajax/favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: productId
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = data.isFavorited ?
                    '<i class="fas fa-heart" style="color: red;"></i>' :
                    '<i class="far fa-heart" style="color: red;"></i>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
$(document).ready(function(){
    $('.slider').slick({
      infinite: true,       
      slidesToShow: 1,     
      slidesToScroll: 1,    
      arrows: true,          
      dots: true,           
      autoplay: true,     
      autoplaySpeed: 2000,   
    });
  });
  function updateSlug(slug) {

    document.getElementById('selectedSlug').value = slug;
}


function toggleSubcategory(index) {
        const dropdown = document.getElementById('subcategory-' + index);
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }


    function setSort(order) {
        document.getElementById('sortInput').value = order;
        document.getElementById('filterForm').submit();
    }



    $('#country').on('change', function() {
    var countryId = $(this).val();
    if (countryId) {
      $.ajax({
        url: '<?php echo $urlval ?>admin/ajax/product/get_cities.php',
        type: 'POST',
        data: {
          country_id: countryId
        },
        success: function(data) {
            
          $('#city').html(data);
        },
        error: function() {
          alert('Error fetching cities. Please try again.');
        }
      });
    } else {
      $('#city').html('<option value="" disabled>Select City</option>');
    }
  });

  function toggleSubcategory(index) {
    var subcategoryDiv = document.getElementById('subcategory-' + index);
    

    if (subcategoryDiv.style.display === 'none' || subcategoryDiv.style.display === '') {
        subcategoryDiv.style.display = 'block';
    } else {
        subcategoryDiv.style.display = 'none';
    }
}


document.getElementById('sortDropdown').addEventListener('click', function (e) {
        const dropdownMenu = this.nextElementSibling; 
        if (dropdownMenu.classList.contains('show')) {
            dropdownMenu.classList.remove('show'); 
        } else {
            dropdownMenu.classList.add('show'); 
        }
    });

    
    document.addEventListener('click', function (event) {
        const sortDropdown = document.getElementById('sortDropdown');
        const dropdownMenu = sortDropdown.nextElementSibling;

 
        if (!sortDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
</script>
</body>

</html>