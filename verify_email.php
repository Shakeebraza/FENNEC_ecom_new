<?php
require_once("global.php");

$currentDateTime = date('Y-m-d H:i:s');

if (isset($_GET['token']) && isset($_GET['email']) && isset($_GET['role'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $roleParam = strtolower($_GET['role']); // e.g. "admin" or "user"
    
    // Determine which table to check based on role parameter.
    if ($roleParam === 'admin') {
        $record = $dbFunctions->getDatanotenc('admins', "email = '$email' AND verification_token = '$token'");
    } else {
        $record = $dbFunctions->getDatanotenc('users', "email = '$email' AND verification_token = '$token'");
    }
    
    if ($record && is_array($record) && isset($record[0]['id'])) {
        $userId = $record[0]['id'];
        // Optionally, you could get a user role from the record if needed:
        $userRole = $record[0]['role'] ?? '0';
        
        $data = [
            'email_verified_at' => $currentDateTime,
            'verification_token' => 0
        ];
        
        // Update the appropriate table.
        if ($roleParam === 'admin') {
            $updateQuery = $dbFunctions->updateData('admins', $data, $userId);
        } else {
            $updateQuery = $dbFunctions->updateData('users', $data, $userId);
        }
        
        if (isset($updateQuery['success']) && $updateQuery['success'] === true) {
            if ($roleParam === 'admin') {
                echo "
                <script>
                    alert('Email verified successfully! Redirecting to Admin login page...');
                    window.location.href ='".$urlval."admin/login.php';
                </script>
                ";
                exit(); 
            } else {
                echo "
                <script>
                    alert('Email verified successfully! Redirecting to main login page...');
                    window.location.href = '".$urlval."LoginRegister.php';
                </script>
                ";
                exit(); 
            }
        } else {
            echo "
            <script>
                alert('Something went wrong, please contact the administrator.');
                window.location.href = '".$urlval.($roleParam === 'admin' ? "admin/login.php" : "LoginRegister.php")."';
            </script>
            ";
            exit();
        }
        
    } else {
        echo "
        <script>
            alert('Invalid verification token or email!');
            window.location.href = '".$urlval."LoginRegister.php';
        </script>
        ";
        exit(); 
    }
} else {
    echo "
    <script>
        alert('No token, email, or role provided!');
        window.location.href = '".$urlval."LoginRegister.php';
    </script>
    ";
    exit();
}
