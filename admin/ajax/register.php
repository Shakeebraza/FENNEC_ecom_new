<?php
$setSession = $fun->isSessionSet();
$redirectUrl = $urlval . 'admin/logout.php';

// 1. Verify session is set
if (!$setSession) {
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>';
    exit();
}

// 2. Check that the user’s role is in [1,3,4]
if (!in_array($_SESSION['role'], [1, 3, 4])) {
    echo '
    <script>
        window.location.href = "' . $redirectUrl . '";
    </script>';
    exit();
}

// For a "pretty" name in the badge:
$roleNames = [
    1 => 'Super Admin',
    3 => 'Admin',
    4 => 'Moderator'
];
$roleName = $roleNames[$_SESSION['role']] ?? 'Unknown';

// Continue with the rest of the header code
$current_url = $_SERVER['REQUEST_URI'];

function isActive($link) {
    global $current_url;
    return strpos($current_url, $link) !== false ? 'active' : '';
}

// Use a default profile image if none is set
$profile = empty($_SESSION['profile']) 
    ? $urlval . 'images/profile.jpg' 
    : $_SESSION['profile'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... your <head> content (meta tags, styles, etc.) ... -->
</head>

<body class="animsition">
    <div class="page-wrapper">
        <?php include_once('sidebar.php');?>

        <header class="header-desktop">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="header-wrap">
                        <form class="form-header" action="" method="POST">
                            <input class="au-input au-input--xl" type="text" name="search"
                                   placeholder="Search for datas &amp; reports..." />
                            <button class="au-btn--submit" type="submit">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </form>
                        <div class="header-button">
                            <div class="noti-wrap">
                                <a href="<?php echo $urlval?>admin/messange.php">
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-comment-more <?= isActive('/fennec/admin/messange.php'); ?>"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="account-wrap">
                                <div class="account-item clearfix js-item-menu">
                                    <div class="image">
                                        <img src="<?php echo $profile ?>" alt="Profile" />
                                    </div>
                                    <div class="content">
                                        <!-- We show the user’s name and a badge for their role -->
                                        <a class="js-acc-btn" href="#">
                                            <?php echo $_SESSION['username'] ?>
                                        </a>
                                        <span class="badge bg-primary text-white" style="margin-left: 6px;">
                                            <?php echo $roleName ?>
                                        </span>
                                    </div>
                                    <div class="account-dropdown js-dropdown">
                                        <div class="info clearfix">
                                            <div class="image">
                                                <a href="#">
                                                    <img src="<?php echo $profile ?>" alt="Profile" />
                                                </a>
                                            </div>
                                            <div class="content">
                                                <h5 class="name">
                                                    <a href="#"><?php echo $_SESSION['username'] ?></a>
                                                </h5>
                                                <span class="email"><?php echo $_SESSION['email'] ?></span>
                                                <span class="badge bg-primary text-white d-block mt-2">
                                                    <?php echo $roleName ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="account-dropdown__body">
                                            <div class="account-dropdown__item">
                                                <a href="<?= $urlval ?>admin/account.php">
                                                    <i class="zmdi zmdi-account"></i>Account
                                                </a>
                                            </div>
                                            <div class="account-dropdown__item">
                                                <a href="#">
                                                    <i class="zmdi zmdi-settings"></i>Setting
                                                </a>
                                            </div>
                                            <div class="account-dropdown__item">
                                                <a href="#">
                                                    <i class="zmdi zmdi-money-box"></i>Billing
                                                </a>
                                            </div>
                                        </div>
                                        <div class="account-dropdown__footer">
                                            <a href="<?= $urlval ?>admin/logout.php">
                                                <i class="zmdi zmdi-power"></i>Logout
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- header-button -->
                    </div><!-- header-wrap -->
                </div><!-- container-fluid -->
            </div><!-- section__content -->
        </header>
        <!-- ... rest of your page content ... -->
