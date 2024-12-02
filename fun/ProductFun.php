<?php
Class Productfun{
   
    private $pdo;
    private $security;
    private $dbfun;
    private $urlval;

    public function __construct($database, $security, $dbfun, $urlval){
        $this->pdo = $database->getConnection();  
        $this->security = $security;             
        $this->dbfun = $dbfun;                  
        $this->urlval = $urlval;
    }
    function getProductsWithDetailsOld($page, $limit, $filters = []) {
 // $sql = "
        //     SELECT 
        //         p.id AS product_id,
        //         p.name AS product_name,
        //         p.slug AS product_slug,
        //         p.description AS product_description,
        //         p.image AS product_image,
        //         p.price AS product_price,
        //         p.date AS productdate,
        //         p.user_id AS prouserid,
        //         p.product_type AS product_type,
        //         p.discount_price AS product_discount_price,
        //         c.category_name AS category_name,
        //         s.subcategory_name AS subcategory_name,
        //         ci.name AS city_name,
        //         co.name AS country_name
        //     FROM 
        //         products p
        //     LEFT JOIN 
        //         categories c ON p.category_id = c.id
        //     LEFT JOIN 
        //         subcategories s ON p.subcategory_id = s.id
        //     LEFT JOIN 
        //         cities ci ON p.city_id = ci.id
        //     LEFT JOIN 
        //         countries co ON p.country_id = co.id
        //     WHERE 
        //         p.is_enable = 1
        // ";
    
        // $params = [];
        // if (!empty($filters['pid'])) {
        //     $sql .= " AND p.id LIKE :id";
        //     $params[':id'] = '%' . $filters['pid'] . '%';
        // }
        // if (!empty($filters['product_name'])) {
        //     $sql .= " AND p.name LIKE :product_name";
        //     $params[':product_name'] = '%' . $filters['product_name'] . '%';
        // }
        // if (!empty($filters['slug'])) {
        //     $sql .= " AND p.slug LIKE :slug";
        //     $params[':slug'] = '%' . $filters['slug'] . '%';
        // }
        // if (!empty($filters['min_price'])) {
        //     $sql .= " AND p.price >= :min_price";
        //     $params[':min_price'] = $filters['min_price'];
        // }
        // if (!empty($filters['max_price'])) {
        //     $sql .= " AND p.price <= :max_price";
        //     $params[':max_price'] = $filters['max_price'];
        // }
        // if (!empty($filters['category'])) {
        //     $sql .= " AND p.category_id = :category";
        //     $params[':category'] = $filters['category'];
        // }
        // if (!empty($filters['subcategory'])) {
        //     $sql .= " AND p.subcategory_id = :subcategory";
        //     $params[':subcategory'] = $filters['subcategory'];
        // }
        // if (!empty($filters['product_type'])) {
        //     $sql .= " AND p.product_type = :product_type";
        //     $params[':product_type'] = $filters['product_type'];
        // }
        // if (!empty($filters['country'])) {
        //     $sql .= " AND p.country_id = :country";
        //     $params[':country'] = $filters['country'];
        // }
        // if (!empty($filters['city'])) {
        //     $sql .= " AND p.city_id = :city";
        //     $params[':city'] = $filters['city'];
        // }
    
        // $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
    
        // $stmt = $this->pdo->prepare($sql);
    
        // foreach ($params as $key => $value) {
        //     $stmt->bindValue($key, $value);
        // }
        // $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        // $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        // $stmt->execute();
    
        // $getproduct = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // $totalSql = "SELECT COUNT(*) AS total FROM products WHERE is_enable = 1";
        // $totalStmt = $this->pdo->prepare($totalSql);
        // $totalStmt->execute();
        // $total = $totalStmt->fetchColumn();
    
        // $response = [
        //     'products' => [],
        //     'total' => $total
        // ];

        // if ($getproduct) {
        //     foreach ($getproduct as $pro) {
        //         $image = $this->urlval . $pro['product_image'];
        //         $response['products'][] = [
        //             'id' => $pro['product_id'],
        //             'name' => $pro['product_name'],
        //             'slug' => $pro['product_slug'],
        //             'description' => $pro['product_description'],
        //             'image' => $image,
        //             'price' => $pro['product_price'],
        //             'discount_price' => $pro['product_discount_price'],
        //             'product_type' => $pro['product_type'],
        //             'category' => $pro['category_name'],
        //             'subcategory' => $pro['subcategory_name'],
        //             'city' => $pro['city_name'],
        //             'country' => $pro['country_name'],
        //             'date' => $pro['productdate'],
        //             'prouserid' => $pro['prouserid'],
        //         ];
        //     }
        // }
    
        // return $response;
    }
    function getProductsWithDetails($page, $limit, $filters = [],$sortBy='custom') {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS product_slug,
                p.description AS product_description,
                p.image AS product_image,
                p.price AS product_price,
                p.date AS productdate,
                p.user_id AS prouserid,
                p.product_type AS product_type,
                p.discount_price AS product_discount_price,
                c.category_name AS category_name,
                s.subcategory_name AS subcategory_name,
                ci.name AS city_name,
                co.name AS country_name
            FROM 
                products p
            LEFT JOIN 
                categories c ON p.category_id = c.id
            LEFT JOIN 
                subcategories s ON p.subcategory_id = s.id
            LEFT JOIN 
                cities ci ON p.city_id = ci.id
            LEFT JOIN 
                countries co ON p.country_id = co.id
            WHERE 
                p.is_enable = 1
        ";
        
        $params = [];
        if (!empty($filters['pid'])) {
            $sql .= " AND p.id LIKE :id";
            $params[':id'] = '%' . $filters['pid'] . '%';
        }
        if (!empty($filters['product_name'])) {
            $sql .= " AND p.name LIKE :product_name";
            $params[':product_name'] = '%' . $filters['product_name'] . '%';
        }
        if (!empty($filters['slug'])) {
            $sql .= " AND p.slug LIKE :slug";
            $params[':slug'] = '%' . $filters['slug'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['subcategory'])) {
            $sql .= " AND p.subcategory_id = :subcategory";
            $params[':subcategory'] = $filters['subcategory'];
        }
        if (!empty($filters['product_type'])) {
            $sql .= " AND p.product_type = :product_type";
            $params[':product_type'] = $filters['product_type'];
        }
        if (!empty($filters['country'])) {
            $sql .= " AND p.country_id = :country";
            $params[':country'] = $filters['country'];
        }
        if (!empty($filters['aera_id'])) {
            $sql .= " AND p.aera_id = :aera_id";
            $params[':aera_id'] = $filters['aera_id'];
        }
        if (!empty($filters['city'])) {
            $sql .= " AND p.city_id = :city";
            $params[':city'] = $filters['city'];
        }
        
        // Sorting logic
        if ($sortBy === 'shuffle') {
            // Random sorting
            $sql .= " ORDER BY RAND()";
        } else {
            // Custom sorting by product type
            $sql .= "
                ORDER BY 
                    CASE 
                        WHEN p.product_type = 'premium' THEN 1
                        WHEN p.product_type = 'gold' THEN 2
                        WHEN p.product_type = 'standard' THEN 3
                        ELSE 4
                    END,
                    p.created_at DESC
            ";
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $getproduct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Total count query
        $totalSql = "SELECT COUNT(*) AS total FROM products WHERE is_enable = 1";
        $totalStmt = $this->pdo->prepare($totalSql);
        $totalStmt->execute();
        $total = $totalStmt->fetchColumn();
        
        $response = [
            'products' => [],
            'total' => $total
        ];
        
        if ($getproduct) {
            foreach ($getproduct as $pro) {
                $image = $this->urlval . $pro['product_image'];
                $response['products'][] = [
                    'id' => $pro['product_id'],
                    'name' => $pro['product_name'],
                    'slug' => $pro['product_slug'],
                    'description' => $pro['product_description'],
                    'image' => $image,
                    'price' => $pro['product_price'],
                    'discount_price' => $pro['product_discount_price'],
                    'product_type' => $pro['product_type'],
                    'category' => $pro['category_name'],
                    'subcategory' => $pro['subcategory_name'],
                    'city' => $pro['city_name'],
                    'country' => $pro['country_name'],
                    'date' => $pro['productdate'],
                    'prouserid' => $pro['prouserid'],
                ];
            }
        }
        
        return $response;
        
    }
    function getProductsWithDetailsAdmin($page, $limit, $filters = [],$sortBy='custom') {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS product_slug,
                p.description AS product_description,
                p.image AS product_image,
                p.is_enable AS is_enable,
                p.price AS product_price,
                p.date AS productdate,
                p.user_id AS prouserid,
                p.product_type AS product_type,
                p.discount_price AS product_discount_price,
                c.category_name AS category_name,
                s.subcategory_name AS subcategory_name,
                ci.name AS city_name,
                co.name AS country_name
            FROM 
                products p
            LEFT JOIN 
                categories c ON p.category_id = c.id
            LEFT JOIN 
                subcategories s ON p.subcategory_id = s.id
            LEFT JOIN 
                cities ci ON p.city_id = ci.id
            LEFT JOIN 
                countries co ON p.country_id = co.id
        ";
        
        $params = [];
        if (!empty($filters['pid'])) {
            $sql .= " AND p.id LIKE :id";
            $params[':id'] = '%' . $filters['pid'] . '%';
        }
        if (!empty($filters['product_name'])) {
            $sql .= " AND p.name LIKE :product_name";
            $params[':product_name'] = '%' . $filters['product_name'] . '%';
        }
        if (!empty($filters['slug'])) {
            $sql .= " AND p.slug LIKE :slug";
            $params[':slug'] = '%' . $filters['slug'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['subcategory'])) {
            $sql .= " AND p.subcategory_id = :subcategory";
            $params[':subcategory'] = $filters['subcategory'];
        }
        if (!empty($filters['product_type'])) {
            $sql .= " AND p.product_type = :product_type";
            $params[':product_type'] = $filters['product_type'];
        }
        if (!empty($filters['country'])) {
            $sql .= " AND p.country_id = :country";
            $params[':country'] = $filters['country'];
        }
        if (!empty($filters['city'])) {
            $sql .= " AND p.city_id = :city";
            $params[':city'] = $filters['city'];
        }
        
        // Sorting logic
        if ($sortBy === 'shuffle') {
            // Random sorting
            $sql .= " ORDER BY RAND()";
        } else {
            // Custom sorting by product type
            $sql .= "
                ORDER BY 
                    CASE 
                        WHEN p.product_type = 'premium' THEN 1
                        WHEN p.product_type = 'gold' THEN 2
                        WHEN p.product_type = 'standard' THEN 3
                        ELSE 4
                    END,
                    p.created_at DESC
            ";
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $getproduct = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Total count query
        $totalSql = "SELECT COUNT(*) AS total FROM products WHERE is_enable = 1";
        $totalStmt = $this->pdo->prepare($totalSql);
        $totalStmt->execute();
        $total = $totalStmt->fetchColumn();
        
        $response = [
            'products' => [],
            'total' => $total
        ];
        
        if ($getproduct) {
            foreach ($getproduct as $pro) {
                $image = $this->urlval . $pro['product_image'];
                $response['products'][] = [
                    'id' => $pro['product_id'],
                    'name' => $pro['product_name'],
                    'slug' => $pro['product_slug'],
                    'description' => $pro['product_description'],
                    'image' => $image,
                    'price' => $pro['product_price'],
                    'discount_price' => $pro['product_discount_price'],
                    'product_type' => $pro['product_type'],
                    'category' => $pro['category_name'],
                    'subcategory' => $pro['subcategory_name'],
                    'city' => $pro['city_name'],
                    'country' => $pro['country_name'],
                    'date' => $pro['productdate'],
                    'prouserid' => $pro['prouserid'],
                    'is_enable' => $pro['is_enable'],
                ];
            }
        }
        
        return $response;
        
    }
    public function searchData($table, $query) {
        $sql = "SELECT p.*, c.category_name, c.slug, c.category_image 
                FROM $table AS p
                JOIN categories AS c ON p.category_id = c.id
                WHERE p.name LIKE :query 
                OR p.brand LIKE :query 
                OR p.description LIKE :query 
                OR c.category_name LIKE :query
                LIMIT 10";
    
        $stmt = $this->pdo->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    function getCountryCityPairs() {
        $query = "
        SELECT countries.id AS country_id, countries.name AS country_name, 
               cities.id AS city_id, cities.name AS city_name 
        FROM countries
        INNER JOIN cities ON countries.id = cities.country_id
        ORDER BY countries.name, cities.name";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllcatandSubcat($categoryId = null) {
        try {
            $categoriesQueryStr = "SELECT * FROM categories WHERE is_show = 1 AND is_enable = 1";
            
            if ($categoryId !== null) {
                $categoriesQueryStr .= " AND id = :category_id";
            }
            
            $categoriesQuery = $this->pdo->prepare($categoriesQueryStr);
            if ($categoryId !== null) {
                $categoriesQuery->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            }
            
            $categoriesQuery->execute();
            $categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
            $result = [
                'status' => 'success',
                'data' => []
            ];
            
            foreach ($categories as $category) {
                $subcategoriesQuery = $this->pdo->prepare("SELECT * FROM subcategories WHERE category_id = :category_id");
                $subcategoriesQuery->bindParam(':category_id', $category['id'], PDO::PARAM_INT);
                $subcategoriesQuery->execute();
                $subcategories = $subcategoriesQuery->fetchAll(PDO::FETCH_ASSOC);
                
                $result['data'][] = [
                    'category_name' => $category['category_name'],
                    'subcategories' => $subcategories
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getProductDetailsBySlug($slug, $userId = null) {
        $sql = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.description AS product_description,
                p.price,
                p.conditions,
                p.product_type,
                p.created_at as prodate,
                p.image as proimage,
                p.user_id,
                p.category_id,
                p.discount_price,
                p.country_id,
                p.city_id,
                p.aera_id,
                p.brand,
                c.category_name,
                c.slug AS catslug,
                s.subcategory_name,
                cou.name AS con_name,
                city.name AS city_name,
                area.name AS area_name,  -- Area name
                city.longitude AS city_longitude,  -- Longitude of the city
                city.latitude AS city_latitude,    -- Latitude of the city
                pi.image_path AS image_path,
                CASE WHEN f.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_favorited
            FROM 
                products p
            LEFT JOIN 
                categories c ON p.category_id = c.id
            LEFT JOIN 
                subcategories s ON p.subcategory_id = s.id
            LEFT JOIN 
                product_images pi ON p.id = pi.product_id
            LEFT JOIN 
                favorites f ON p.id = f.product_id " . ($userId ? "AND f.user_id = :user_id" : "") . "
            LEFT JOIN 
                countries cou ON cou.id = p.country_id
            LEFT JOIN 
                cities city ON city.id = p.city_id
            LEFT JOIN 
                areas area ON area.id = p.aera_id  
            WHERE 
                p.slug = :slug
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug);
    
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
    
        $stmt->execute();
        $productDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($productDetails) {
            $firstProduct = $productDetails[0];
            $images = array_column($productDetails, 'image_path');
    
            return [
                'product' => $firstProduct,
                'gallery_images' => $images,
                'is_favorited' => $firstProduct['is_favorited'],
                'location' => $firstProduct['con_name'] . ' | ' . $firstProduct['city_name'] . ' | ' . $firstProduct['area_name'],
                'country' => $firstProduct['con_name'],
                'city' => $firstProduct['city_name'],
                'area' => $firstProduct['area_name'],
                'city_longitude' => $firstProduct['city_longitude'],  // Longitude
                'city_latitude' => $firstProduct['city_latitude'],    // Latitude
            ];
        }
    
        return null;
    }
    
    
    
    public function toggleFavorite($productId, $userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM favorites WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$productId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$productId, $userId]);
            return false; 
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO favorites (product_id, user_id) VALUES (?, ?)");
            $stmt->execute([$productId, $userId]);
            return true;
        }
    }
    public function getRelatedProducts($categoryId, $productId, $limit = 5) {
        $sql = "
            SELECT 
                p.id AS product_id,
                p.name AS title,
                p.slug,
                p.price,
                p.image AS image
            FROM 
                products p
            WHERE 
                p.category_id = :categoryId
                AND p.id != :productId
            LIMIT :limit
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getUserFavorites($userId) {
     
        $query = "
            SELECT p.id, p.name, p.slug, p.description, p.image, p.price
            FROM favorites f
            INNER JOIN products p ON f.product_id = p.id
            WHERE f.user_id = :user_id
            ORDER BY f.created_at DESC
        ";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $count = count($favorites);
        return [
            'count' => $count,
            'favorites' => $favorites
        ];
    }

    function getProductsForUser($userId, $lan) {
        if ($userId) {
            $query = "
                SELECT id, name, slug, description, image, price, created_at, product_type
                FROM products
                WHERE user_id = :user_id AND is_enable = 1 AND status = 'active'
                ORDER BY FIELD(product_type, 'premium', 'gold', 'standard'), created_at DESC
            ";

    
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
    
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (!empty($products)) {
                $this->displayProducts($products, $lan);
            } else {
                throw new Exception("No products found for user with ID: " . htmlspecialchars($userId));
            }
        } else {
            return $lan['No_products_found_for_user']; // Translated message
        }
    }
    
    function displayProducts($products, $lan) {
        foreach ($products as $product) {
            $description = $product['description'];
            $words = explode(" ", $description);
            $description = count($words) > 5 ? implode(" ", array_slice($words, 0, 5)) . '...' : $description;
    
            echo '
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img
                            src="' . htmlspecialchars($product['image']) . '"
                            class="card-img-top"
                            alt="' . htmlspecialchars($product['name']) . '"
                        />
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>
                            <p class="card-text">' . htmlspecialchars($description) . '</p>
                            <p class="card-text"><strong>' . $lan['price'] . ':</strong> $' . number_format($product['price'], 2) . '</p>
                            <p class="card-text">
                                <small class="text-muted">' . $lan['listed'] . ' ' . $this->dbfun->time_ago($product['created_at']) . '</small>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a class="btn btn-button btn-sm" href="'.$this->urlval.'productedit.php?productid='.$this->security->encrypt($product['id']).'">'.$lan['edit'].'</a>
                                
                                <div class="btn-delete-upload">';
                                if($product['product_type'] == 'standard'){
                                    echo'
                                    <a class="btn btn-button btn-sm btn-boost" href="'.$this->urlval.'productboost.php?productid='.base64_encode($product['id']).'">'.$lan['boost'].'</a>
                                    
                                    ';
                                }else{
                                    echo'
                                    
                                    <a class="btn btn-button btn-sm btn-boost" href="'.$this->urlval.'uploadgalvideo.php?productid='.base64_encode($product['id']).'">'.$lan['upload_gallery_video'].'</a>
                                    ';
                                }
                                echo'<button class="btn btn-button btn-sm btn-delete" data-product-id="' . $this->security->encrypt($product['id']) . '">' . $lan['delete'] . '</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }
    }
    
    public function GetUserId($id){
        if(isset($id)){
            $getdata = $this->dbfun->getDatanotenc('products', "id='$id'");
            if(isset($getdata[0])){
                $userId = $getdata[0]['user_id'];
                return $userId;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    public function PoplarProduct()
    {

        $conn = $this->pdo;
        $stmt = $conn->prepare("
            SELECT * 
            FROM products 
            WHERE product_type IN ('premium', 'gold') 
            AND is_enable = 1 
            AND status = 'active' 
            ORDER BY RAND() 
            LIMIT 1
        ");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            return $product;
        } else {
            return null;
        }
    }
    public function PoplarProductper()
    {

        $conn = $this->pdo;
        $stmt = $conn->prepare("
            SELECT * 
            FROM products 
            WHERE product_type IN ('premium') 
            AND is_enable = 1 
            AND status = 'active' 
            ORDER BY RAND() 
            LIMIT 1
        ");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            return $product;
        } else {
            return null;
        }
    }
    public function PoplarProductperMultipal()
    {
        $conn = $this->pdo;
        $stmt = $conn->prepare("
            SELECT * 
            FROM products 
            WHERE product_type IN ('premium') 
            AND is_enable = 1 
            AND status = 'active' 
            ORDER BY RAND()
        ");
        $stmt->execute();
        
        // Fetch all matching products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If products are found, return them; otherwise, return null
        if ($products) {
            return $products;
        } else {
            return null;
        }
    }
    public function PoplarProductgoldMultipal()
    {
        $conn = $this->pdo;
        $stmt = $conn->prepare("
            SELECT * 
            FROM products 
            WHERE product_type IN ('gold') 
            AND is_enable = 1 
            AND status = 'active' 
            ORDER BY RAND()
        ");
        $stmt->execute();
        
        // Fetch all matching products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If products are found, return them; otherwise, return null
        if ($products) {
            return $products;
        } else {
            return null;
        }
    }
    public function PoplarProductMuultipal()
    {
        $conn = $this->pdo;
        $stmt = $conn->prepare("
            SELECT * 
            FROM products 
            WHERE product_type IN ('premium','gold') 
            AND is_enable = 1 
            AND status = 'active' 
            ORDER BY RAND()
        ");
        $stmt->execute();
        
        // Fetch all matching products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If products are found, return them; otherwise, return null
        if ($products) {
            return $products;
        } else {
            return null;
        }
    }
    
    function getPremiumProductsWithVideos() {
        try {
            // Query to fetch 5 random premium products
            $productQuery = "
                SELECT 
                    p.id AS product_id,
                    p.name,
                    p.slug,
                    p.description,
                    p.brand,
                    p.conditions,
                    p.image,
                    p.category_id,
                    p.subcategory_id,
                    p.price,
                    p.discount_price,
                    p.is_enable,
                    p.status,
                    p.product_type,
                    p.user_id,
                    p.country_id,
                    p.city_id,
                    p.date,
                    p.created_at,
                    p.updated_at,
                    pv.video_paths
                FROM 
                    products p
                LEFT JOIN 
                    product_videos pv 
                ON 
                    p.id = pv.product_id
                WHERE 
                    p.product_type = 'premium' AND p.is_enable = 1
                ORDER BY 
                    RAND() 
                LIMIT 5
            ";
    
            $stmt = $this->pdo->prepare($productQuery);
            $stmt->execute();
    
            $premiumProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($premiumProducts as &$product) {
                // Check if video_paths exists and is not empty
                if (!empty($product['video_paths'])) {
                    $product['videos'] = explode(',', $product['video_paths']);
                } else {
                    $product['videos'] = []; // Default to an empty array
                }
                unset($product['video_paths']); // Remove the original key
            }
    
            return $premiumProducts;
        } catch (PDOException $e) {
            error_log("Error fetching premium products: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoriesWithChildren()
    {
        try {
          
            $categoriesStmt = $this->pdo->prepare("
                SELECT id, category_name, slug, category_image, icon, sort_order, is_enable, is_show 
                FROM categories 
                WHERE is_enable = 1 AND is_show = 1
                ORDER BY sort_order ASC
            ");
            $categoriesStmt->execute();
            $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    

            $subcategoriesStmt = $this->pdo->prepare("
                SELECT category_id, id,subcategory_name, slug, subcategory_image, icon, sort_order, is_enable 
                FROM subcategories 
                WHERE is_enable = 1 
                ORDER BY sort_order ASC
            ");
            $subcategoriesStmt->execute();
            $subcategories = $subcategoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
      
            $subcategoriesByCategory = [];
            foreach ($subcategories as $subcategory) {
                $subcategoriesByCategory[$subcategory['category_id']][] = $subcategory;
            }
    
         
            foreach ($categories as &$category) {
                $category['children'] = $subcategoriesByCategory[$category['id']] ?? [];
            }
    
            return [
                'status' => 'success',
                'data' => $categories
            ];
        } catch (Exception $e) {
            // Handle exception and return an empty array
            error_log('Error fetching categories with children: ' . $e->getMessage());
            return [
                'status' => 'error',
                'data' => []
            ];
        }
    }
    
    
    
}

?>