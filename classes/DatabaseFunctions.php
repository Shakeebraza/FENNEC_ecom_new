<?php
class DatabaseFunctions {
    private $pdo;
    private $security;

    public function __construct($database, $security) {
        $this->pdo = $database->getConnection();
        $this->security = $security;
    }

    public function getData($tableName, $where = '', $groupBy = '', $orderBy = '', $orderDirection = 'ASC', $start = 0, $length = 10) {

        $query = "SELECT * FROM `$tableName`";
    
        if ($where) {
            $query .= " WHERE $where";
        }
    
        if ($groupBy) {
            $query .= " GROUP BY $groupBy";
        }
    
        if ($orderBy) {
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
            $query .= " ORDER BY $orderBy $orderDirection";
        }
    
        if ($length > 0) {
            $query .= " LIMIT :start, :length";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
        $stmt->execute();
    
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($results as &$record) {
            foreach ($record as $key => $value) {
                $record[$key] = $this->security->encrypt($value);
            }
        }
    
        return $results;
    }
    
    public function getDatanotenc($tableName, $where = '', $groupBy = '', $orderBy = '', $orderDirection = 'ASC', $start = 0, $length = 10) {

        $query = "SELECT * FROM `$tableName`";
    
        if ($where) {
            $query .= " WHERE $where";
        }
    
        if ($groupBy) {
            $query .= " GROUP BY $groupBy";
        }
    
        if ($orderBy) {
            $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
            $query .= " ORDER BY $orderBy $orderDirection";
        }
    
        if ($length > 0) {
            $query .= " LIMIT :start, :length";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
        $stmt->execute();
    
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($results as &$record) {
            foreach ($record as $key => $value) {
                $record[$key] = $value;
            }
        }
    
        return $results;
    }

    public function getDataById($tableName, $id) {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);

        $stmt = $this->pdo->prepare("SELECT * FROM `$tableName` WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record) {
            foreach ($record as $key => $value) {
                $record[$key] = $this->security->encrypt($value);
            }
        }

        return $record;
    }
    public function getCount($tableName, $field = '*', $where = '') {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    
        if ($field !== '*') {
            $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        }
    
        $query = "SELECT COUNT($field) AS count FROM `$tableName`";
        if ($where) {
            $query .= " WHERE $where";
        }
    
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return ['Error' => true, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    

    public function setData($tableName, $data, $where = null) {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    
        $sanitizedData = [];
    
        foreach ($data as $key => $value) {
            $value = strip_tags($value); // Remove HTML tags
            if ($key !== 'name') { // Exclude htmlspecialchars for specific fields (e.g., 'name')
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            $sanitizedData[$key] = $value;
        }
    
        try {
            if ($where) {
                $set = '';
                $params = [];
                foreach ($sanitizedData as $key => $value) {
                    $set .= "`$key` = :$key, ";
                    $params[":$key"] = $value;
                }
                $set = rtrim($set, ', ');
                $whereClause = '';
                foreach ($where as $col => $val) {
                    $whereClause .= "`$col` = :$col AND ";
                    $params[":$col"] = $val;
                }
                $whereClause = rtrim($whereClause, ' AND ');
    
                $stmt = $this->pdo->prepare("UPDATE `$tableName` SET $set WHERE $whereClause");
    
            } else {
                $columns = implode('`, `', array_keys($sanitizedData));
                $placeholders = ':' . implode(', :', array_keys($sanitizedData));
                $stmt = $this->pdo->prepare("INSERT INTO `$tableName` (`$columns`) VALUES ($placeholders)");
                foreach ($sanitizedData as $key => $value) {
                    $params[":$key"] = $value;
                }
            }
            $stmt->execute($params);
            return ['success' => true, 'message' => 'Data saved successfully.'];
    
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'Duplicate entry error: The email or field you are trying to use already exists.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    

    public function setDataWithHtmlAllowed($tableName, $data, $where = null) {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        
        $sanitizedData = [];
        
        foreach ($data as $key => $value) {
       
         
                $sanitizedData[$key] = $value; 
            
        }
        
        try {
            if ($where) {
      
                $set = '';
                $params = [];
                foreach ($sanitizedData as $key => $value) {
                    $set .= "`$key` = :$key, ";
                    $params[":$key"] = $value;
                }
                $set = rtrim($set, ', ');
                
                $whereClause = '';
                foreach ($where as $col => $val) {
                    $whereClause .= "`$col` = :$col AND ";
                    $params[":$col"] = $val;
                }
                $whereClause = rtrim($whereClause, ' AND ');
        
                $stmt = $this->pdo->prepare("UPDATE `$tableName` SET $set WHERE $whereClause");
            } else {

                $columns = implode('`, `', array_keys($sanitizedData));
                $placeholders = ':' . implode(', :', array_keys($sanitizedData));
                $stmt = $this->pdo->prepare("INSERT INTO `$tableName` (`$columns`) VALUES ($placeholders)");
                foreach ($sanitizedData as $key => $value) {
                    $params[":$key"] = $value;
                }
            }
  
            $stmt->execute($params);
            return ['success' => true, 'message' => 'Data saved successfully.'];
    
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'Duplicate entry error: The email or field you are trying to use already exists.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    
    
    
    function delData($tableName, $whereCondition) {
    
        try {
            // Prepare the SQL delete statement
            $sql = "DELETE FROM " . $tableName . " WHERE " . $whereCondition;
            $stmt = $this->pdo->prepare($sql);
            
            // Execute the delete query
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Record deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Error deleting record.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateData($tableName, $data, $id) {

        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    

        $sanitizedData = [];
        foreach ($data as $key => $value) {
            $value = strip_tags($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $sanitizedData[$key] = $value;
        }
    

        $setClause = [];
        foreach ($sanitizedData as $key => $value) {
            $setClause[] = "`$key` = :$key";
        }
        $setClause = implode(', ', $setClause);
    
        try {

            $stmt = $this->pdo->prepare("UPDATE `$tableName` SET $setClause WHERE id = :id");
            

            $sanitizedData['id'] = $id; 
    

            foreach ($sanitizedData as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
    
     
            $stmt->execute();
    
     
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Data updated successfully.'];
            } else {
                return ['success' => false, 'message' => 'No changes made; the data was the same.'];
            }
        } catch (PDOException $e) {
 
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    function getLastIdshort($tableName) {
        try {
            $query = $this->pdo->prepare("SELECT MAX(sort_order) as last_id FROM $tableName");
            $query->execute();
            

            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['last_id'] : 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
    function getCategories() {
        // Fetch categories ordered by sort_order
        $stmt = $this->pdo->query("SELECT id, category_name, slug, category_image, icon, sort_order, is_enable FROM categories WHERE is_enable = 1 ORDER BY sort_order ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $categoryTree = [];
        foreach ($categories as $category) {
            $categoryTree[$category['id']] = [
                'id' => $category['id'],
                'name' => $category['category_name'],
                'slug' => $category['slug'],
                'category_image' => $category['category_image'],
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'children' => [] 
            ];
        }
    
        // Fetch subcategories ordered by sort_order
        $stmt = $this->pdo->query("SELECT id, subcategory_name, slug, subcategory_image, icon, sort_order, is_enable, category_id FROM subcategories WHERE is_enable = 1 ORDER BY sort_order ASC");
        $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($subcategories as $subcategory) {
            if (isset($categoryTree[$subcategory['category_id']])) {
                $categoryTree[$subcategory['category_id']]['children'][] = [
                    'id' => $subcategory['id'], 
                    'name' => $subcategory['subcategory_name'],
                    'slug' => $subcategory['slug'],
                    'subcategory_image' => $subcategory['subcategory_image'],
                    'icon' => $subcategory['icon'],
                    'sort_order' => $subcategory['sort_order'],
                    'category_id' => $subcategory['category_id'],
                ];
            }
        }
    
        // Sort the children by sort_order as well, in case they are not sorted correctly after fetching
        foreach ($categoryTree as &$category) {
            usort($category['children'], function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
        }
    
        return $categoryTree;
    }
    function time_ago($datetime, $full = false) {
        if (!$datetime) {
            return 'Invalid date';
        }
        $timezone = new DateTimeZone('Asia/Karachi');
    
        $now = new DateTime('now', $timezone);
        $ago = new DateTime($datetime, $timezone);
    
        if ($ago > $now) {
            return 'This product will be available in the future';
        }
    
        $diff = $now->diff($ago);
    
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }
    
    
    
}
?>
