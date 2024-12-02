<?php
require_once("../global.php");
include_once('header.php');

$username = $_SESSION['username'] ?? ''; 
$formemail = $_SESSION['email'] ?? ''; 
$userid = intval(base64_decode($_SESSION['userid'])) ?? 0; 
$userData = $dbFunctions->getDatanotenc('user_detail', "userid = '$userid'");
$profileImage = $_SESSION['profile'] === '' ? $urlval . 'images/profile.jpg' : $_SESSION['profile'];
$csrfError = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$CsrfProtection->validateToken($_POST['csrf_token'])) {
        $csrfError = "Invalid CSRF token!";
    } else {
      
        $newUsername = $_POST['username'] ?? '';
        $newNumber = $_POST['number'] ?? '';
        $newAddress = $_POST['address'] ?? '';
        $newCountry = $_POST['country'] ?? '';
        $newCity = $_POST['city'] ?? '';
        $profileImagePath = ''; 

        
        if ($newUsername != '') {
            $checkUsername = $dbFunctions->getDatanotenc('users', "username = '$newUsername'AND id != '$userid'");
            if ($checkUsername) {
                $csrfError = "This username is already taken. Please choose another.";
            } else {
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $targetDir = "../upload/";
                    
                    
                    $originalFilename = pathinfo($_FILES['profile_image']['name'], PATHINFO_FILENAME);
                    $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                    $randomString = bin2hex(random_bytes(8));
                    $newFilename = $originalFilename . '_' . $randomString . '.' . $fileExtension;
                    $profileImagePath = $targetDir . $newFilename;
                    $databaseProfilePath='upload/'.$newFilename;

                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath)) {
                        $userData=[
                            'username'=>$newUsername,
                            'profile'=>$databaseProfilePath

                        ];
                        $chnggg=$dbFunctions->updateData('users', $userData, $userid);
                        var_dump($chnggg);
                        $_SESSION['profile'] = $urlval.$databaseProfilePath;
                        $_SESSION['username'] = $newUsername;
                    } else {
                        $csrfError = "Failed to upload profile image.";
                    }
                }else{
                    $userData=[
                        'username'=>$newUsername,

                    ];
                    $dbFunctions->updateData('users', $userData, $userid);
                    $_SESSION['username'] = $newUsername;
                }
                if (empty($newNumber) || empty($newAddress) || empty($newCountry) || empty($newCity)) {
                    $csrfError = "Please fill all the fields!";
                } else {

                    $data_userDetails = [
                        'number' => $newNumber,
                        'address' => $newAddress,
                        'country' => $newCountry,
                        'city' => $newCity,
                        'userid'=>$userid,
                    ];


                    $existingUserData = $dbFunctions->getDatanotenc('user_detail', "userid = '$userid'");
              
                    if ($existingUserData) {
                        $dbFunctions->updateData('user_detail', $data_userDetails, $existingUserData[0]['id']);
                        $successMessage = "Account information updated successfully!";
                    } else {
                        $newUserId = $dbFunctions->setData('user_detail', $data_userDetails);
                        $successMessage = "Account information inserted successfully!";
                    }

                    $_SESSION['username'] = $newUsername;
                }
            }
        }
    }
}
?>

<style>
.text-heading {
    background-color: whitesmoke;
}
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-lg">
                            <div class="card-header text-black text-center text-heading">
                                <h3>Account Information</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($csrfError): ?>
                                    <div class="alert alert-danger"><?php echo $csrfError; ?></div>
                                <?php elseif ($successMessage): ?>
                                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>

                                <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <div class="row form-group">
                                        <div class="col text-center">
                                            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" width="150px" class="rounded-circle mb-3">
                                            <div class="custom-file mb-3">
                                                <input type="file" class="custom-file-input" id="profile-image" name="profile_image">
                                                <label class="custom-file-label" for="profile-image">Choose Profile Image</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control form-control-lg " placeholder="Enter your username">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formemail); ?>" class="form-control form-control-lg " readonly>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="number" class="form-label">Phone Number</label>
                                            <input type="text" id="number" name="number" value="<?php echo isset($userData[0]['number']) ? htmlspecialchars($userData[0]['number']) : ''; ?>" class="form-control form-control-lg " placeholder="Enter your phone number">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" id="address" name="address" value="<?php echo isset($userData[0]['address']) ? htmlspecialchars($userData[0]['address']) : ''; ?>" class="form-control form-control-lg " placeholder="Enter your address">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" id="country" name="country" value="<?php echo isset($userData[0]['country']) ? htmlspecialchars($userData[0]['country']) : ''; ?>" class="form-control form-control-lg " placeholder="Enter your country">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" id="city" name="city" value="<?php echo isset($userData[0]['city']) ? htmlspecialchars($userData[0]['city']) : ''; ?>" class="form-control form-control-lg " placeholder="Enter your city">
                                        </div>
                                    </div>
                                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $CsrfProtection->generateToken(); ?>">
                                    <div class="row form-group">
                                        <div class="col text-center">
                                            <button type="submit" class="btn btn-success btn-lg">Update Account</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>



</body>

</html>