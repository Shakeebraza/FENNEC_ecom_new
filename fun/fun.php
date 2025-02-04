<?php
class Fun {
    private $pdo;
    private $security;
    private $dbfun;

    private $urlval;

    public function __construct($database, $security, $dbfun,$urlval) {
        $this->pdo = $database->getConnection();
        $this->security = $security;
        $this->dbfun = $dbfun;
        $this->urlval = $urlval;
    }


    public function getBox($tablename = null) {
        try {
            if ($tablename !== null) {
                $tabledata = $this->dbfun->getData('box', "title = '$tablename'");
                if (empty($tabledata)) {
                    echo "No data found or an issue with the query.";
                    return [];
                } else {
                    // echo "Data retrieved successfully: <br>";
                }
    
                foreach ($tabledata as &$row) {
                    foreach ($row as $key => $value) {
                        $row[$key] = $this->security->decrypt($value);
                    }
                }
    
                return $tabledata;
            } else {
                echo "No table name provided.";
                return [];
            }
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function getAllBox($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('box','', '', 'created_at', 'DESC', $start, $length); 
            if (empty($tabledata)) {
               
                return [];
            } else {
                
            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata; 
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
 
    public function getTotalBoxCount() {
        $query = "SELECT COUNT(*) AS total FROM box";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function boost_plansTotalCount() {
        $query = "SELECT COUNT(*) AS total FROM boost_plans";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getAllboost_plans($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('boost_plans','', '', 'created_at', 'DESC', $start, $length); 
            if (empty($tabledata)) {
               
                return [];
            } else {
                
            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata; 
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function getBoxPermission($id) {
        $data = $this->dbfun->getData('boxsetting', "boxid = '$id'");
        if ($data) {
            $formattedData = [];
            foreach ($data as $record) {
                $formattedData[] = [
                    'boxid' => $this->security->decrypt($record['boxid']),
                    'phara' => $this->security->decrypt($record['phara']),
                    'image' => $this->security->decrypt($record['image']),
                    'image2' => $this->security->decrypt($record['image2']),
                    'text' => $this->security->decrypt($record['text']),
                    'longtext' => $this->security->decrypt($record['longtext']),
                    'link' => $this->security->decrypt($record['link'])
                ];
            }
            return $formattedData;
        } else {

            return [];
        }
    }
    public function uploadImage($file) {
        $uploadDir = __DIR__ . '/../upload/';
        $uploadnewDir = 'upload/';
        $fileName = basename($file['name']);
        $uniqueFileName = uniqid() . '_' . $fileName;
        $targetFilePath = $uploadDir . $uniqueFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return null;
        }

        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return $uploadnewDir . $uniqueFileName;
        } else {
            return null;
        }
    }
    public function uploadLanFiles($file) {
        $uploadDir = __DIR__ . '/../languages/';
        $uploadnewDir = 'languages/';
        $fileName = basename($file['name']);
        $uniqueFileName = uniqid() . '_' . $fileName;
        $targetFilePath = $uploadDir . $uniqueFileName;
    
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($fileExtension !== 'php') {
            return null; 
        }
    

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return $uploadnewDir . $uniqueFileName;
        } else {
            return null;
        }
    }
public function getUserBalance($userId)
{
    // If you have access to the PDO connection in $this->pdo, do something like:
    // (Adjust table/column names as needed)
    $sql = "SELECT wallet_balance FROM users WHERE id = :userId LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['wallet_balance'])) {
        return (float)$row['wallet_balance'];
    }

    // If no row found or something else, return 0 (or handle as needed)
    return 0.0;
}

    public function FindAllLan() {
     
        $getLan = $this->dbfun->getDatanotenc('languages');
        if ($getLan && !empty($getLan)) {
            return $getLan;
        } else {
            return null;
        }
    }
    
    public function deleteData($id) {
        if (isset($id)) {

            $checkBoxSetting = $this->dbfun->getData('boxsetting', "boxid = '$id'");

            if ($checkBoxSetting) {

                $result = $this->dbfun->delData('boxsetting', "boxid = '$id'");

                if ($result['success'] == true) {
                    echo "Attempting to delete from box with id = $id";

                    $result = $this->dbfun->delData('box', "id = '$id'");

                    if ($result['success'] == true) {
                        return ['success' => true, 'message' => 'Record deleted successfully from both tables.'];
                    } else {

                        return ['success' => false, 'message' => 'Failed to delete record from box table. Error: ' . $result['message']];
                    }
                } else {
                    return ['success' => false, 'message' => 'Failed to delete record from boxsetting table.'];
                }
            } else {
                return ['success' => false, 'message' => 'Record not found in boxsetting.'];
            }
        }
    }


    public function getTotalMenuCount() {
        $query = "SELECT COUNT(*) AS total FROM menus";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getTotalBannerCount() {
        $query = "SELECT COUNT(*) AS total FROM banners";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getAllMenu($start,$length,$where='') {
        try {
            $tabledata = $this->dbfun->getData('menus',$where, '', 'updated_at', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function getAllBanner($start,$length,$where='') {
        try {
            $tabledata = $this->dbfun->getData('banners',$where, '', 'id', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function getTotalPageCount() {
        $query = "SELECT COUNT(*) AS total FROM pages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAllPages($start,$length,$where='') {
        try {
            $tabledata = $this->dbfun->getData('pages',$where, '', 'created_at', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    

    public function getTotalCatCount() {
        $query = "SELECT COUNT(*) AS total FROM categories";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
  

    public function getAllcat($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('categories','', '', 'created_at', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function getTotalLanCount() {
        $query = "SELECT COUNT(*) AS total FROM languages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getTotalContactCount() {
        $query = "SELECT COUNT(*) AS total FROM contacts";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function getAllLan($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('languages','', '', 'id', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function getAllContact($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('contacts','', '', 'id', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function findAllsubcat($categoryId) {
        try {
            $subcatData = $this->dbfun->getData('subcategories',"category_id='$categoryId' ", '', 'created_at', 'DESC');
    
            if (empty($subcatData)) {
                return [];
            }
    
           
            foreach ($subcatData as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }
    
            return $subcatData; 
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function ordercat($order, $parent_id) {
   
        $stmt = $this->pdo->prepare("UPDATE subcategories SET sort_order = ? , category_id = ? WHERE id = ? ");
        
        foreach ($order as $position => $id) {
            $stmt->execute([$position,  $parent_id,$id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Order updated successfully.']);
    }

    public function getTotalSubCatCount() {
        $query = "SELECT COUNT(*) AS total FROM subcategories";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAllSubcat($start,$length) {
        try {
            $tabledata = $this->dbfun->getData('subcategories','', '', 'created_at', 'DESC', $start, $length);
            if (empty($tabledata)) {

                return [];
            } else {

            }
            foreach ($tabledata as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }

            return $tabledata;
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function findAllPerentcat($categoryId) {
        try {
            $subcatData = $this->dbfun->getData('categories',"id='$categoryId' ");
    
            if (empty($subcatData)) {
                return [];
            }
    
           
            foreach ($subcatData as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->security->decrypt($value);
                }
            }
    
            return $subcatData; 
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function sessionSet($email = NULL) {
        if (isset($email) && !empty($email)) {
            $userData = $this->dbfun->getDatanotenc('users', "email = '$email'");
            
            if ($userData) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['userid'] = base64_encode($userData[0]['id']);
                $_SESSION['username'] = $userData[0]['username'];
                $_SESSION['email'] = $userData[0]['email'];
                $_SESSION['email_verified_at'] = $userData[0]['email_verified_at'];
                $_SESSION['role'] = $userData[0]['role'];
                $_SESSION['profile'] = $this->urlval.$userData[0]['profile'];
                $_SESSION['remember'] = $userData[0]['remember_token'];
                
        
                return true;
            }
        }
        
        
        return false;
    }

    
        // Helper function to write logs to a custom file
        protected function writeLog($message) {
            // Define the path to the log file; you can change this path as necessary.
            $logFile = __DIR__ . '/admin_session.log';
            $date = date('Y-m-d H:i:s');
            // Append the message to the log file with a newline
            file_put_contents($logFile, "$date - $message" . PHP_EOL, FILE_APPEND);
        }
        
        public function sessionSetAdmin($email = NULL) {
            if (isset($email) && !empty($email)) {
                $this->writeLog("sessionSetAdmin: Attempting to set admin session for email: $email");
                
                $userData = $this->dbfun->getDatanotenc('admins', "email = '$email'");
                
                if ($userData) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                        $this->writeLog("sessionSetAdmin: PHP session started.");
                    }
                    
                    $_SESSION['auserid'] = base64_encode($userData[0]['id']);
                    $_SESSION['ausername'] = $userData[0]['username'];
                    $_SESSION['aemail'] = $userData[0]['email'];
                    $_SESSION['aemail_verified_at'] = $userData[0]['email_verified_at'];
                    $_SESSION['arole'] = $userData[0]['role'];
                    $_SESSION['aprofile'] = $this->urlval . $userData[0]['profile'];
                    $_SESSION['aremember'] = $userData[0]['remember_token'];
                    
                    $this->writeLog("sessionSetAdmin: Admin session successfully set for user id: " . $userData[0]['id']);
                    
                    return true;
                } else {
                    $this->writeLog("sessionSetAdmin: No admin found with email: $email");
                }
            } else {
                $this->writeLog("sessionSetAdmin: Email parameter is empty or not set.");
            }
            
            return false;
        }
    
    public function RequestSessioncheck(){
        if(isset($_SESSION['userid']) && isset($_SESSION['remember'])){
            $id= base64_decode($_SESSION['userid']);
            $remember = $_SESSION['remember'];
            $valid=$this->dbfun->getDatanotenc('users',"id = '$id' AND remember_token = '$remember'");
            if($valid){
                return true;

            }else{
                return false;

            }

        }
        return false;
    }
    
    public function rememberTokenCheckByCookie($remember_token = NULL) {
        if (isset($remember_token) && !empty($remember_token)) {
            $userData = $this->dbfun->getDatanotenc('users', "remember_token = '$remember_token'");
            
            if ($userData) {
          
                if (isset($userData[0]['email_verified_at'])) {
                    session_start();
                    $$_SESSION['userid'] = base64_encode($userData[0]['id']);
                    $_SESSION['username'] = $userData[0]['username'];
                    $_SESSION['email'] = $userData[0]['email'];
                    $_SESSION['email_verified_at'] = $userData[0]['email_verified_at'];
                    $_SESSION['role'] = $userData[0]['role'];
                    $_SESSION['profile'] = $this->urlval.$userData[0]['profile'];
                    return true; 
                } else {
                    $this->dbfun->updateData('users', ['remember_token' => ''], $this->security->decrypt($userData[0]['id']));
                    $this->destroyRememberMe();
                    return false; 
                }
            }
        }
    
  
        $this->destroyRememberMe();
        return false;
    }
    

    private function destroyRememberMe() {
      
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/'); 
        }
    

        session_start();
        session_unset();
        session_destroy();
    }
    public function isAdminSessionSet()
    {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_name('AdminSession');
            session_start();
        }
    
        // Define the admin roles
        $adminRoles = [1, 3, 4];
    
        // Check if the necessary session variables are set and if the user's role is an admin role
        if (
            isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['role']) &&
            in_array($_SESSION['role'], $adminRoles, true) // Check if role is in the adminRoles array
        ) {
            return true;
        }
    
        return false;
    }
    

    public function isSessionSet() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if (isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['email']) &&
            !empty($_SESSION['userid']) &&
            !empty($_SESSION['username']) &&
            !empty($_SESSION['email'])) {
            return true; 
        }
    
        return false; 
    }
    public function isSessionSetAdmin() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if (isset($_SESSION['auserid'], $_SESSION['ausername'], $_SESSION['aemail']) &&
            !empty($_SESSION['auserid']) &&
            !empty($_SESSION['ausername']) &&
            !empty($_SESSION['aemail'])) {
            return true; 
        }
    
        return false; 
    }

    public function isUserSessionSet()
    {
        // If no session is active, create/start the "UserSession"
        if (session_status() === PHP_SESSION_NONE) {
            session_name('UserSession'); 
            session_start();
        }

        // Check if user info is set and not empty
        if (
            isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['email'], $_SESSION['role']) &&
            !empty($_SESSION['userid']) &&
            !empty($_SESSION['username']) &&
            !empty($_SESSION['email']) &&
            in_array($_SESSION['role'], [0, 2]) // Only allow roles 0 or 2
        ) {
            return true;
        }

        return false;
    }

    
    function getUserRegistrationData() {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count 
                  FROM users 
                  WHERE created_at >= NOW() - INTERVAL 30 DAY 
                  GROUP BY DATE(created_at) 
                  ORDER BY DATE(created_at) ASC"; 
        $registrationData = $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
        $dates = [];
        $userCounts = [];
        foreach ($registrationData as $data) {
            $dates[] = $data['date'];         
            $userCounts[] = $data['count']; 
        }
        $fullDates = [];
        $fullCounts = [];
        for ($i = 29; $i >= 0; $i--) { 
            $date = date('Y-m-d', strtotime("-$i days"));
            $fullDates[] = $date;
            $fullCounts[] = 0; 
            foreach ($registrationData as $data) {
                if ($data['date'] == $date) {
                    $fullCounts[29 - $i] = $data['count'];
                    break;
                }
            }
        }
        $totalNewMembers = array_sum($fullCounts);
        return [
            'dates' => $fullDates,
            'userCounts' => $fullCounts,
            'totalNewMembers' => $totalNewMembers
        ];
    }
    function getProductAdditionData() {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count 
                  FROM products 
                  WHERE created_at >= NOW() - INTERVAL 6 MONTH 
                  GROUP BY DATE(created_at) 
                  ORDER BY DATE(created_at) ASC"; 
    
        $productData = $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
        $fullDates = [];
        $fullCounts = [];

        for ($i = 5; $i >= 0; $i--) { 
          
            $date = date('Y-m-01', strtotime("-$i months"));
            $fullDates[] = $date;
            $fullCounts[] = 0; 
        }
    
   
        foreach ($productData as $data) {
      
            $dateIndex = array_search(date('Y-m-01', strtotime($data['date'])), $fullDates);
            if ($dateIndex !== false) {
                $fullCounts[$dateIndex] = $data['count']; 
            }
        }
    
        $totalProducts = array_sum($fullCounts);
   
        return [
            'dates' => array_map(function($d) { return date('F', strtotime($d)); }, $fullDates), 
            'productCounts' => $fullCounts,
            'totalProducts' => $totalProducts
        ];
    }
    
    function getProductCounts() {
        $query = "SELECT product_type, COUNT(*) as count FROM products GROUP BY product_type";
        $productData = $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
        // Initialize counts
        $counts = [
            'standard' => 0,
            'premium' => 0,
            'gold' => 0
        ];
    
  
        foreach ($productData as $data) {
            $counts[$data['product_type']] = (int)$data['count'];
        }
    
        return $counts;
    }

    public function getUserVerificationCounts() {
        $query = "SELECT 
                    COUNT(CASE WHEN email_verified_at IS NOT NULL THEN 1 END) AS verified_count,
                    COUNT(CASE WHEN email_verified_at IS NULL THEN 1 END) AS not_verified_count
                  FROM users";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function generateSettingsForm() {
        $settings = $this->dbfun->getDatanotenc('site_settings', '', '', '', 'ASC', 0, 100);
        $formHtml = '<form method="POST" action="" enctype="multipart/form-data" style="max-width: 100%; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">';
    
        foreach ($settings as $setting) {
            $key = htmlspecialchars($setting['key']);
            $value = htmlspecialchars($setting['value']);  // Encode only when displaying data
            $inputType = htmlspecialchars($setting['input_type']);
            
            $label = ucwords(str_replace('_', ' ', $key));
    
            $formHtml .= "<div style='margin-bottom: 15px;'>";
            $formHtml .= "<label for='{$key}' style='display: block; margin-bottom: 5px; font-weight: bold; color: #333;'>{$label}</label>";
    
            switch ($inputType) {
                case 'text':
                    $formHtml .= "<input type='text' id='{$key}' name='{$key}' value='{$value}' required style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
                    break;
    
                case 'url':
                    $formHtml .= "<input type='url' id='{$key}' name='{$key}' value='{$value}' required style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
                    break;
    
                case 'image':
                    $formHtml .= "<input type='file' id='{$key}' name='{$key}' value='".$this->urlval . $value."' required placeholder='Image URL' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
                    $formHtml .= "<img src='".$this->urlval . $value."' alt='{$key}' style='width: 100px; height: auto; margin-top: 5px;'><br>";
                    $formHtml .= "<small style='color: #555;'>Upload a new image URL above if needed.</small>";
                    break;
    
                default:
                    $formHtml .= "<input type='text' id='{$key}' name='{$key}' value='{$value}' required style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
                    break;
            }
    
            $formHtml .= "</div>";
        }
    
        $formHtml .= '<div>
                        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Save Settings</button>
                      </div>';
        $formHtml .= '</form>';
    
        return $formHtml;
    }
    
    public function updateDatasiteseeting($table, $dataArray) {
        foreach ($dataArray as $key => $value) {
            $sql = "UPDATE {$table} SET `value` = :value WHERE `key` = :key";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
        }
        return true; 
    }

    public function TopLocations() {
        try {
            // Fetch country name, city name, and city id
            $stmt = $this->pdo->prepare("SELECT countries.name AS country_name, cities.name AS city_name, cities.id AS city_id 
                                         FROM countries LEFT JOIN cities ON countries.id = cities.country_id");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return [
                'status' => 'success',
                'code' => 200,
                'data' => $data
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    function getSiteSettingValue($key) {
        global $dbFunctions;

        $query = "SELECT `value` FROM `site_settings` WHERE `key` = :key";
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':key', $key);
        

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
  
        return $result ? $result['value'] : null; 
    }
    function getMenus() {
       
        $menuQuery = "SELECT * FROM menus WHERE is_enabled = 1";
        $stmt = $this->pdo->prepare($menuQuery);
        $stmt->execute();
        

        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        $menuData = [];
    
        foreach ($menus as $menu) {
            $menuId = $menu['id'];
            

            $itemQuery = "SELECT * FROM menu_items WHERE menu_id = :menu_id AND is_enable = 1";
            $itemStmt = $this->pdo->prepare($itemQuery);
            $itemStmt->bindParam(':menu_id', $menuId);
            $itemStmt->execute();
            

            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
            
 
            $menuData[] = [
                'menu' => $menu,
                'items' => $items,
            ];
        }
    
        return $menuData;
    }
    function getBoostPlans() {
        $query = "SELECT * FROM boost_plans WHERE status = 'active'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // function updateBoostPlanStatus($planId, $txnId, $userId, $amount, $productId) {
    //     global $pdo;
    
    //     try {
    //         $pdo->beginTransaction();
    
    //         // Insert into payments table
    //         $stmt2 = $pdo->prepare("INSERT INTO payments (plan_id,proid, user_id, txn_id, amount, status) VALUES (?,?, ?, ?, ?, 'completed')");
    //         $stmt2->execute([$planId,$productId, $userId, $txnId, $amount]);
    
    //         // Determine product type based on plan ID
    //         $productType = '';
    //         switch ($planId) {
    //             case 1:
    //                 $productType = 'standard';
    //                 break;
    //             case 2:
    //                 $productType = 'premium';
    //                 break;
    //             case 3:
    //                 $productType = 'gold';
    //                 break;
    //             default:
    //                 $productType = 'standard';
    //                 break;
    //         }
    
    //         // Update the product table's product_type based on the selected plan
    //         $stmt3 = $pdo->prepare("UPDATE products SET product_type = ? WHERE id = ?");
    //         $stmt3->execute([$productType, $productId]);
    
    //         $pdo->commit();
    //         return true;
    
    //     } catch (Exception $e) {
    //         $pdo->rollBack();
    //         echo "Failed: " . $e->getMessage();
    //         return false;
    //     }
    // }
    function updateBoostPlanStatus($planId, $txnId, $userId, $amount, $productId) {
        global $pdo;
    
        try {
            // Start transaction
            $pdo->beginTransaction();
    
            // Check if the product already has the same active plan
            $stmt1 = $pdo->prepare("SELECT product_type FROM products WHERE id = :productId");
            $stmt1->execute([':productId' => $productId]);
            $currentPlan = $stmt1->fetchColumn();
    
            // Determine the requested product type based on the plan ID
            $productType = '';
            switch ($planId) {
                case 1:
                    $productType = 'gold';
                    break;
                case 2:
                    $productType = 'premium';
                    break;
                default:
                    $productType = 'standard';
                    break;
            }
    
            // If the current product type matches the requested type, return early with a specific code
            if ($currentPlan === $productType) {
                $pdo->rollBack(); // Roll back transaction
                return 'already_active'; // Return specific status for already active plan
            }
    
            // Insert payment record
            $stmt2 = $pdo->prepare("INSERT INTO payments (plan_id, proid, user_id, txn_id, amount, status) VALUES (?, ?, ?, ?, ?, 'completed')");
            $stmt2->execute([$planId, $productId, $userId, $txnId, $amount]);
    
            // Update the product's plan type
            $stmt3 = $pdo->prepare("UPDATE products SET product_type = ? WHERE id = ?");
            $stmt3->execute([$productType, $productId]);
    
            // Commit transaction
            $pdo->commit();
            return true;
    
        } catch (Exception $e) {
            // Roll back transaction on error
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    
    
    
    
    public function getTotalPayment()
        {
            $query = "SELECT COUNT(*) as total FROM payments";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }

        // Function to get paginated payment data
public function getPaymentData($start, $length)
{
    $query = "
        SELECT 
            p.id, 
            p.plan_id, 
            p.proid, 
            p.user_id, 
            p.txn_id, 
            p.amount, 
            p.status, 
            p.created_at,
            u.username, 
            u.email,
            pr.name as product_name,
            bp.name as plan_name
        FROM payments p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN products pr ON p.proid = pr.id
        LEFT JOIN boost_plans bp ON p.plan_id = bp.id
        ORDER BY p.created_at DESC
        LIMIT :start, :length
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':length', $length, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loadLanguage($lang) {
    $file = __DIR__ . "/../languages/{$lang}.php";

    if (file_exists($file)) {
        return include($file);
    }
    
    // Default language file (English)
    return include(__DIR__ . "/../languages/en.php");
}


function getUserTransactionsAndProducts($startDate, $endDate, $productName = null, $priceRange = null) {
    global $pdo;
    $userId = base64_decode($_SESSION['userid']);
    
    $query = "
        SELECT 
            pay.id AS payment_id,
            pay.txn_id AS payment_txn_id,
            pay.amount AS payment_amount,
            pay.status AS payment_status,
            pay.created_at AS payment_created_at,
            p.id AS product_id,
            p.name AS product_name,
            p.slug AS product_slug,
            p.description AS product_description,
            p.brand AS product_brand,
            p.conditions AS product_conditions,
            p.image AS product_image,
            p.category_id AS product_category_id,
            p.subcategory_id AS product_subcategory_id,
            p.price AS product_price,
            p.discount_price AS product_discount_price,
            p.is_enable AS product_is_enable,
            p.status AS product_status,
            p.product_type AS product_type
        FROM 
            payments pay
        INNER JOIN 
            products p ON pay.proid = p.id
        WHERE 
            pay.user_id = :user_id
        AND 
            pay.created_at BETWEEN :start_date AND :end_date
    ";
    
    // Add product name condition if provided
    if ($productName) {
        $query .= " AND p.name LIKE :product_name";
    }
    
    // Add price range condition if provided
    if ($priceRange) {
        if (isset($priceRange['min_price'])) {
            $query .= " AND p.price >= :min_price";
        }
        if (isset($priceRange['max_price'])) {
            $query .= " AND p.price <= :max_price";
        }
    }

    // Prepare and execute the SQL query
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    
    // Bind additional parameters if provided
    if ($productName) {
        $stmt->bindValue(':product_name', '%' . $productName . '%', PDO::PARAM_STR);
    }
    if ($priceRange) {
        if (isset($priceRange['min_price'])) {
            $stmt->bindParam(':min_price', $priceRange['min_price'], PDO::PARAM_INT);
        }
        if (isset($priceRange['max_price'])) {
            $stmt->bindParam(':max_price', $priceRange['max_price'], PDO::PARAM_INT);
        }
    }


    $stmt->execute();
    
   
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFieldData($fieldName, $id = 1) {
     try {
      
        $stmt = $this->pdo->prepare("SELECT $fieldName FROM websettings WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result[$fieldName] ?? null; 
    } catch (Exception $e) {
  
        return "Error: " . $e->getMessage();
    }
}
// function getRandomBannerByPlacement($placement) {
//     $query = "SELECT id, image, title, description, btn_text, btn_url, text_color, btn_color, bg_color, placement, is_active, created_at, updated_at
//               FROM banners
//               WHERE placement = :placement AND is_active = 1
//               ORDER BY RAND()
//               LIMIT 1";
//     $stmt = $this->pdo->prepare($query);
//     $stmt->bindParam(':placement', $placement, PDO::PARAM_STR);
//     $stmt->execute();
//     $banner = $stmt->fetch(PDO::FETCH_ASSOC);
//     return $banner;
// }
function getRandomBannerByPlacement($placement) {
    $query = "
        SELECT 
            id, 
            CONCAT('../', image) AS image, 
            title, 
            description, 
            btn_text, 
            btn_url, 
            text_color, 
            btn_color, 
            bg_color, 
            placement, 
            is_active, 
            created_at, 
            updated_at
        FROM banners
        WHERE placement = :placement 
          AND is_active = 1
        ORDER BY RAND()
        LIMIT 1
    ";
    
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':placement', $placement, PDO::PARAM_STR);
    $stmt->execute();
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    return $banner;
}

function getSiteStatistics() {
   
    $queries = [
        'Users' => "SELECT COUNT(*) as count FROM users",
        'Main Categories' => "SELECT COUNT(*) as count FROM categories",
        'Sub Categories' => "SELECT COUNT(*) as count FROM subcategories",
        'Ads' => "SELECT COUNT(*) as count FROM products",
        'Approved Ads' => "SELECT COUNT(*) as count FROM products WHERE status = 'active'",
        'Unapproved Ads' => "SELECT COUNT(*) as count FROM products WHERE status = 'inactive'",
        'New/Updated Awaiting Approval' => "SELECT COUNT(*) as count FROM products WHERE status = 'pending'",
        'Posted Today Ads' => "SELECT COUNT(*) as count FROM products WHERE date = CURDATE()",
        'Posted Yesterday Ads' => "SELECT COUNT(*) as count FROM products WHERE date = CURDATE() - INTERVAL 1 DAY",
        'Posted in Last 7 Days Ads' => "SELECT COUNT(*) as count FROM products WHERE date >= CURDATE() - INTERVAL 7 DAY",
        'Posted in Last 30 Days Ads' => "SELECT COUNT(*) as count FROM products WHERE date >= CURDATE() - INTERVAL 30 DAY",
    ];

    $statistics = [];
    foreach ($queries as $key => $query) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $statistics[$key] = $result['count'] ?? 0;
    }

    return $statistics;
}

function calculatePostingDuration($created_at) {
    $createdDate = new DateTime($created_at);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($createdDate);
    return $interval->y + ($interval->m / 12); 
}

function getTotalItems($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchColumn();
}

function getCategories($user_id) {
    global $pdo; 
    $stmt = $pdo->prepare("
        SELECT c.category_name, COUNT(p.id) AS count
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.user_id = :user_id
        GROUP BY c.category_name
    ");
    $stmt->execute(['user_id' => $user_id]);

    $categories = [];
    while ($row = $stmt->fetch()) {
        $categories[] = htmlspecialchars($row['category_name']) . " (" . htmlspecialchars($row['count']) . ")";
    }
    return implode(", ", $categories);
}



function getListings($user_id) {
    global $pdo; 

    $stmt = $pdo->prepare("
        SELECT p.*, COUNT(pi.id) AS photo_count
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.user_id = :user_id AND p.is_enable = 1
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute(['user_id' => $user_id]);
    $listings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $createdDate = new DateTime($row['created_at']);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($createdDate);
        $daysAgo = $interval->days;

        $name = htmlspecialchars($row['name'] ?? 'N/A');
        $id = $row['id'] ?? 0;
        $slug = $row['slug'] ?? 0;
        $image = htmlspecialchars($row['image'] ?? '') ?: 'https://via.placeholder.com/150';
        $price = htmlspecialchars(number_format((float)$row['price'], 2));
        $photoCount = (int)$row['photo_count'];

        $listings[] = [
            'title' => $name,               
            'id' => $id,               
            'slug' => $slug,               
            'image_url' => $image,    
            'photo_count' => $photoCount,   
            'price' => $price,
            'posted_days_ago' => $daysAgo,
        ];
    }
    return $listings;
}







}