<?php
$setSession = $fun->isSessionSet();

$redirectUrl = $urlval . 'admin/logout.php'; 
if ($setSession == false) {
   
    
        echo '
        <script>
            window.location.href = "' . $redirectUrl . '";
        </script>'; 
        exit();


}

if($_SESSION['role'] != 1){
        echo '
        <script>
            window.location.href = "' . $redirectUrl . '";
        </script>'; 
        exit();

}
$current_url = $_SERVER['REQUEST_URI'];

function isActive($link) {
    global $current_url;
    return strpos($current_url, $link) !== false ? 'active' : '';
}

$profile = $_SESSION['profile'] === "" ? $urlval . 'images/profile.jpg' : $_SESSION['profile'];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">


    <title>Dashboard</title>


    <link href="<?php echo $urlval?>admin/asset/css/font-face.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet"
        media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet"
        media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/mdi-font/css/material-design-iconic-font.min.css"
        rel="stylesheet" media="all">


    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <link href="<?php echo $urlval?>admin/asset/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css"
        rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $urlval?>admin/asset/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet"
        media="all">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link href="<?php echo $urlval?>admin/asset/css/theme.css" rel="stylesheet" media="all">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css" />
    <style>
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='black' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");

    }

    @media (max-width: 991px) {
        .mobileview {
            display: flex;
            justify-content: space-between;
        }
    }
    </style>

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
                                        <i
                                            class="zmdi zmdi-comment-more <?=isActive('/fennec/admin/messange.php');  ?>"></i>
                                        <!-- <span class="quantity">1</span> -->
                                        <div class="mess-dropdown js-dropdown">
                                        </div>
                                </a>
                            </div>


                        </div>
                        <div class="account-wrap">
                            <div class="account-item clearfix js-item-menu">
                                <div class="image">
                                    <img src="<?php echo $profile?>" alt="Image Not found" />
                                </div>
                                <div class="content">
                                    <a class="js-acc-btn" href="#"><?php echo $_SESSION['username']?></a>
                                </div>
                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="image">
                                            <a href="#">
                                                <img src="<?php echo $profile?>" alt="Image Not found" />
                                            </a>
                                        </div>
                                        <div class="content">
                                            <h5 class="name">
                                                <a href="#"><?php echo $_SESSION['username']?></a>
                                            </h5>
                                            <span class="email"><?php echo $_SESSION['email']?></span>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <a href="<?= $urlval?>admin/account.php">
                                                <i class="zmdi zmdi-account"></i>Account</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-settings"></i>Setting</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-money-box"></i>Billing</a>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__footer">
                                        <a href="<?= $urlval?>admin/logout.php">
                                            <i class="zmdi zmdi-power"></i>Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </header>