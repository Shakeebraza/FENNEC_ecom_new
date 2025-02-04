<?php
// FIRST: role checks
$role = $_SESSION['arole'] ?? 0;
// If user not in [1,3,4], redirect out. 
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
// $isAdmin will be true for role=1 (Super Admin) or 3 (Admin)
$isAdmin = in_array($role, [1,3]);
?>

<!-- SIDEBAR -->
<nav>
    <div class="container-fluid mobileview">
        <a class="navbar-brand" href="#">
            <img src="<?= $urlval?>/custom/asset/Capture-removebg-preview.png" alt="Cool Admin Logo" width="40"
                height="40">
            <span>Fennec</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#coolAdminOffcanvas"
            aria-controls="coolAdminOffcanvas" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="coolAdminOffcanvas"
            aria-labelledby="coolAdminOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="coolAdminOffcanvasLabel">fennec</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item <?= isActive('/fennec/admin/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval?>admin/index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    <!-- USERS SECTION -->
                    <li
                        class="nav-item has-sub <?= isActive('/fennec/admin/user/index.php'); ?> <?= isActive('/fennec/admin/user/adduser.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-users"></i> Users
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval?>admin/user/index.php">All Users</a>
                            </li>
                            <!-- Show "Add Users" only if $isAdmin -->
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval?>admin/user/adduser.php">Add Users</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- BANNERS -->
                    <li
                        class="nav-item has-sub <?= isActive('/fennec/admin/banner/index.php'); ?> <?= isActive('/fennec/admin/banner/addbanner.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-sliders"></i> Banners
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval?>admin/banner/index.php">Manage Banners</a>
                            </li>
                            <!-- Only Admin or Super Admin can "Add Banner" -->
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval?>admin/banner/addbanner.php">Add Banner</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item <?= isActive('/fennec/admin/menu/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval?>admin/menu/index.php">
                            <i class="fas fa-bars"></i> Menus
                        </a>
                    </li>

                    <!-- PAGES -->
                    <li
                        class="nav-item has-sub <?= isActive('/fennec/admin/page/index.php'); ?> <?= isActive('/fennec/admin/page/addpage.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-copy"></i> Pages
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval?>admin/page/index.php">All Page</a>
                            </li>
                            <!-- "Add Page" only if isAdmin -->
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval?>admin/page/addpage.php">Add Page</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- ADS SECTION -->
                    <li
                        class="nav-item has-sub <?= isActive('/fennec/admin/subcategories/index.php'); ?> <?= isActive('/fennec/admin/categories/index.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fa fa-folder"></i> Ads
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval?>admin/categories/index.php">All Categories</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval?>admin/subcategories/index.php">All Sub-Categories</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval?>admin/product/index.php">All Ads</a>
                            </li>
                        </ul>
                    </li>

                    <li class="<?= isActive('/fennec/admin/box/index.php'); ?>">
                        <a href="<?php echo $urlval?>admin/box/index.php">
                            <i class="fas fa-desktop"></i> Box
                        </a>
                    </li>
                    <li class="<?= isActive('/fennec/admin/packages/index.php'); ?>">
                        <a href="<?php echo $urlval?>admin/packages/index.php">
                            <i class="fas fa-shopping-cart"></i> Packages
                        </a>
                    </li>
                    <li class="<?= isActive('/fennec/admin/packages/payment.php'); ?>">
                        <a href="<?php echo $urlval?>admin/packages/payment.php">
                            <i class="fas fa-credit-card"></i> Payment
                        </a>
                    </li>

                    <!-- LANGUAGES -->
                    <li
                        class="<?= isActive('/fennec/admin/lan/index.php'); ?> <?= isActive('/fennec/admin/lan/add.php'); ?> <?= isActive('/fennec/admin/lan/edit.php'); ?>">
                        <a href="<?php echo $urlval?>admin/lan/index.php">
                            <i class="fas fa-language"></i> Languages
                        </a>
                    </li>

                    <!-- LOCATION -->
                    <li
                        class="<?= isActive('/fennec/admin/location/index.php'); ?> <?= isActive('/fennec/admin/location/add.php'); ?> <?= isActive('/fennec/admin/location/edit.php'); ?>">
                        <a href="<?php echo $urlval?>admin/location/index.php">
                            <i class="fa-solid fa-location-dot"></i> Location
                        </a>
                    </li>

                    <!-- CONTACT -->
                    <li class="<?= isActive('/fennec/admin/contact/index.php'); ?>">
                        <a href="<?php echo $urlval?>admin/contact/index.php">
                            <i class="fa-solid fa-address-book"></i> Contact
                        </a>
                    </li>

                    <!-- REPORT -->
                    <li class="<?= isActive('/fennec/admin/report/index.php'); ?>">
                        <a href="<?php echo $urlval?>admin/report/index.php">
                            <i class="fa-solid fa-bug"></i> Report
                        </a>
                    </li>


                    <!-- SETTINGS -->
                    <li class="<?= isActive('/fennec/admin/setting.php'); ?>">
                        <a href="<?php echo $urlval?>admin/setting.php">
                            <i class="fas fa-cog"></i> Site Setting
                        </a>
                    </li>
                    <li class="<?= isActive('/fennec/admin/setting/web_setting.php'); ?>">
                        <a href="<?php echo $urlval?>admin/setting/web_setting.php">
                            <i class="fas fa-cog"></i> Website Setting
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END HEADER MOBILE-->

<!-- MENU SIDEBAR (DESKTOP) -->
<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <a href="#">
            <img src="<?php echo $urlval?>admin/asset/images/icon/logo.png" alt="Cool Admin" />
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li class="<?= isActive('/fennec/admin/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/index.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>

                <!-- USERS -->
                <li
                    class="has-sub <?= isActive('/fennec/admin/user/index.php'); ?> <?= isActive('/fennec/admin/user/adduser.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-users"></i>Users
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval?>admin/user/index.php">All Users</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval?>admin/user/adduser.php">Add users</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Add this code where you want the menu item to appear in both mobile and desktop sidebars -->
                <?php if ($isAdmin): ?>
                <li class="<?= isActive('/fennec/admin/addtransaction.php'); ?>">
                    <a href="<?php echo $urlval?>admin/addtransaction.php">
                        <i class="fas fa-money-bill-wave"></i> Add Transaction
                    </a>
                </li>
                <?php endif; ?>

                <!-- BANNERS -->
                <li
                    class="has-sub <?= isActive('/fennec/admin/banner/index.php'); ?> <?= isActive('/fennec/admin/banner/addbanner.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-sliders"></i>Banners
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval?>admin/banner/index.php">Manage Banners</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval?>admin/banner/addbanner.php">Add Banner</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="<?= isActive('/fennec/admin/menu/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/menu/index.php">
                        <i class="fas fa-bars"></i>Menus
                    </a>
                </li>

                <!-- PAGES -->
                <li
                    class="has-sub <?= isActive('/fennec/admin/page/index.php'); ?> <?= isActive('/fennec/admin/page/addpage.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-copy"></i>Pages
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval?>admin/page/index.php">All Page</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval?>admin/page/addpage.php">Add Pages</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- ADS (Categories, Sub-Categories, All Ads) -->
                <li
                    class="has-sub <?= isActive('/fennec/admin/categories/index.php'); ?> <?= isActive('/fennec/admin/subcategories/index.php'); ?> <?= isActive('/fennec/admin/product/index.php'); ?>">
                    <a class="js-arrow " href="#">
                        <i class="fa fa-folder"></i>Ads
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval?>admin/categories/index.php">All Categories</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval?>admin/subcategories/index.php">All Sub-Categories</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval?>admin/product/index.php">All Ads</a>
                        </li>
                    </ul>
                </li>

                <li class="<?= isActive('/fennec/admin/box/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/box/index.php">
                        <i class="fas fa-desktop"></i>Box
                    </a>
                </li>

                <li class="<?= isActive('/fennec/admin/packages/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/packages/index.php">
                        <i class="fas fa-shopping-cart"></i>Packages
                    </a>
                </li>

                <li class="<?= isActive('/fennec/admin/packages/payment.php'); ?>">
                    <a href="<?php echo $urlval?>admin/packages/payment.php">
                        <i class="fas fa-credit-card"></i>Payment
                    </a>
                </li>

                <!-- LANGUAGES -->
                <li
                    class="<?= isActive('/fennec/admin/lan/index.php'); ?> <?= isActive('/fennec/admin/lan/add.php'); ?> <?= isActive('/fennec/admin/lan/edit.php'); ?>">
                    <a href="<?php echo $urlval?>admin/lan/index.php">
                        <i class="fas fa-language"></i>Languages
                    </a>
                </li>

                <!-- LOCATION -->
                <li
                    class="<?= isActive('/fennec/admin/location/index.php'); ?> <?= isActive('/fennec/admin/location/add.php'); ?> <?= isActive('/fennec/admin/location/edit.php'); ?>">
                    <a href="<?php echo $urlval?>admin/location/index.php">
                        <i class="fa-solid fa-location-dot"></i>Location
                    </a>
                </li>

                <!-- CONTACT -->
                <li class="<?= isActive('/fennec/admin/contact/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/contact/index.php">
                        <i class="fa-solid fa-address-book"></i>Contact
                    </a>
                </li>

                <!-- REPORT -->
                <li class="<?= isActive('/fennec/admin/report/index.php'); ?>">
                    <a href="<?php echo $urlval?>admin/report/index.php">
                        <i class="fa-solid fa-bug"></i>Report
                    </a>
                </li>

                <!-- SETTINGS -->
                <li class="<?= isActive('/fennec/admin/setting.php'); ?>">
                    <a href="<?php echo $urlval?>admin/setting.php">
                        <i class="fas fa-cog"></i>Site Setting
                    </a>
                </li>
                <li class="<?= isActive('/fennec/admin/setting/web_setting.php'); ?>">
                    <a href="<?php echo $urlval?>admin/setting/web_setting.php">
                        <i class="fas fa-cog"></i>Website Setting
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>