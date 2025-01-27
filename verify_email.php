<?php
require_once("global.php");

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $user = $dbFunctions->getDatanotenc('users', "email = '$email' AND verification_token = '$token'");

    if ($user && is_array($user) && isset($user[0]['id'])) {
        $userId = $user[0]['id']; 
        $userRole = $user[0]['role'] ?? '0';  // fallback if not found

        $data = [
            'email_verified_at' => $currentDateTime,
            'verification_token' => 0 
        ];

        $updateQuery = $dbFunctions->updateData('users', $data, $userId);

        if ($updateQuery['success'] === true) {
            // Decide redirect based on user role
            // [1,3,4] => http://localhost/fennec/admin/login.php
            // 0 or 2 => http://localhost/fennec/LoginRegister.php
            if (in_array($userRole, ['1','3','4'])) {
                echo "
                <script>
                    alert('Email verified successfully! Redirecting to Admin login page...');
                    window.location.href ='".$urlval."admin/login.php';
                </script>
                ";
                exit(); 
            } else {
                // role 0 or 2 or anything else => go to normal login
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
                window.location.href = '".$urlval."LoginRegister.php';
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
        alert('No token or email provided!');
        window.location.href = '".$urlval."LoginRegister.php';
    </script>
    ";
    exit(); 
}
?>
