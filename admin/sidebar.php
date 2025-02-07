<?php
// FIRST: Role checks
$role = $_SESSION['arole'] ?? 0;
// If user not in [1,3,4], redirect out.
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
// $isAdmin will be true for role=1 (Super Admin) or 3 (Admin)
$isAdmin = in_array($role, [1,3]);
?>

<!-- SIDEBAR (MOBILE/OFFCANVAS) -->
<nav>
    <div class="container-fluid mobileview">
        <a class="navbar-brand" href="#">
            <img src="<?= $urlval ?>/custom/asset/Capture-removebg-preview.png" alt="Cool Admin Logo" width="40" height="40">
            <span>Fennec</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#coolAdminOffcanvas" aria-controls="coolAdminOffcanvas" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="coolAdminOffcanvas" aria-labelledby="coolAdminOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="coolAdminOffcanvasLabel">fennec</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <!-- Dashboard -->
                    <li class="nav-item <?= isActive('/fennec/admin/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    <!-- USERS SECTION -->
                    <li class="nav-item has-sub <?= isActive('/fennec/admin/user/index.php'); ?> <?= isActive('/fennec/admin/user/adduser.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-users"></i> Users
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/user/index.php">All Users</a>
                            </li>
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval ?>admin/user/adduser.php">Add User</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- TRANSACTIONS -->
                    <?php if ($isAdmin): ?>
                    <li class="<?= isActive('/fennec/admin/addtransaction.php'); ?>">
                        <a href="<?php echo $urlval ?>admin/addtransaction.php">
                            <i class="fas fa-money-bill-wave"></i> Add Transaction
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- BANNERS -->
                    <li class="nav-item has-sub <?= isActive('/fennec/admin/banner/index.php'); ?> <?= isActive('/fennec/admin/banner/addbanner.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-sliders"></i> Banners
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/banner/index.php">Manage Banners</a>
                            </li>
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval ?>admin/banner/addbanner.php">Add Banner</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- MENUS -->
                    <li class="<?= isActive('/fennec/admin/menu/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/menu/index.php">
                            <i class="fas fa-bars"></i> Menus
                        </a>
                    </li>

                    <!-- PAGES -->
                    <li class="nav-item has-sub <?= isActive('/fennec/admin/page/index.php'); ?> <?= isActive('/fennec/admin/page/addpage.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-copy"></i> Pages
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/page/index.php">All Pages</a>
                            </li>
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval ?>admin/page/addpage.php">Add Page</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- EMAIL (Separate Category) -->
                    <?php if ($isAdmin): ?>
                    <li class="nav-item has-sub <?= isActive('/fennec/admin/email_templates/index.php'); ?> <?= isActive('/fennec/admin/send_emails/index.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/email_templates/index.php">Email Templates</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/send_emails/index.php">Send Email</a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- ADS SECTION -->
                    <li class="nav-item has-sub <?= isActive('/fennec/admin/categories/index.php'); ?> <?= isActive('/fennec/admin/subcategories/index.php'); ?>">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fa fa-folder"></i> Ads
                            <!-- <span id="pending-count-badge" class="badge badge-danger" style="display:none;"></span> -->
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/categories/index.php">All Categories</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/subcategories/index.php">All Sub-Categories</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/product/index.php">All Ads</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/delete_closed_classifieds/index.php">Delete Expired Ads</a>
                            </li>
                        </ul>
                    </li>

                    <!-- BOX -->
                    <li class="<?= isActive('/fennec/admin/box/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/box/index.php">
                            <i class="fas fa-desktop"></i> Box
                        </a>
                    </li>

                    <!-- PACKAGES -->
                    <li class="<?= isActive('/fennec/admin/packages/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/packages/index.php">
                            <i class="fas fa-shopping-cart"></i> Packages
                        </a>
                    </li>

                    <!-- PAYMENT -->
                    <li class="<?= isActive('/fennec/admin/packages/payment.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/packages/payment.php">
                            <i class="fas fa-credit-card"></i> Payment
                        </a>
                    </li>

                    <!-- LANGUAGES -->
                    <li class="<?= isActive('/fennec/admin/lan/index.php'); ?> <?= isActive('/fennec/admin/lan/add.php'); ?> <?= isActive('/fennec/admin/lan/edit.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/lan/index.php">
                            <i class="fas fa-language"></i> Languages
                        </a>
                    </li>

                    <!-- LOCATION -->
                    <li class="<?= isActive('/fennec/admin/location/index.php'); ?> <?= isActive('/fennec/admin/location/add.php'); ?> <?= isActive('/fennec/admin/location/edit.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/location/index.php">
                            <i class="fa-solid fa-location-dot"></i> Location
                        </a>
                    </li>

                    <!-- CONTACT -->
                    <li class="<?= isActive('/fennec/admin/contact/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/contact/index.php">
                            <i class="fa-solid fa-address-book"></i> Contact
                        </a>
                    </li>

                    <!-- REPORT -->
                    <li class="<?= isActive('/fennec/admin/report/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/report/index.php">
                            <i class="fa-solid fa-bug"></i> Report
                        </a>
                    </li>

                    <!-- NEW: SELECT SCHEME (Add/Edit Icon Scheme) -->
                    <?php if ($isAdmin): ?>
                    <li class="<?= isActive('/fennec/admin/scheme/index.php'); ?>">
                        <a class="nav-link" href="<?php echo $urlval ?>admin/scheme/index.php">
                            <i class="fas fa-palette"></i> Select Scheme
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- SETTINGS & CONFIGURATION -->
                    <li class="nav-item has-sub 
                        <?= isActive('/fennec/admin/setting.php'); ?> 
                        <?= isActive('/fennec/admin/setting/web_setting.php'); ?>
                        <?php if ($isAdmin): ?>
                            <?= isActive('/fennec/admin/cleanup/index.php'); ?>
                            <?= isActive('/fennec/admin/approval_parameters/index.php'); ?>
                            <?= isActive('/fennec/admin/billing_settings/index.php'); ?>
                            <?= isActive('/fennec/admin/configure_billing_fees/index.php'); ?>
                        <?php endif; ?>
                    ">
                        <a class="nav-link js-arrow" href="#">
                            <i class="fas fa-cogs"></i> Settings &amp; Configuration
                        </a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="<?php echo $urlval ?>admin/setting.php">Site Settings</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/setting/web_setting.php">Website Settings</a>
                            </li>
                            <?php if ($isAdmin): ?>
                            <li>
                                <a href="<?php echo $urlval ?>admin/cleanup/index.php">Clean Up</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/approval_parameters/index.php">Approval Parameters</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/billing_settings/index.php">Billing Settings</a>
                            </li>
                            <li>
                                <a href="<?php echo $urlval ?>admin/configure_billing_fees/index.php">Billing Fees for Featured Ads</a>
                            </li>
                            <?php endif; ?>
                        </ul>
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
            <img src="<?php echo $urlval ?>admin/asset/images/icon/logo.png" alt="Cool Admin" />
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <!-- Dashboard -->
                <li class="<?= isActive('/fennec/admin/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- USERS -->
                <li class="has-sub <?= isActive('/fennec/admin/user/index.php'); ?> <?= isActive('/fennec/admin/user/adduser.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/user/index.php">All Users</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval ?>admin/user/adduser.php">Add User</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- TRANSACTIONS -->
                <?php if ($isAdmin): ?>
                <li class="<?= isActive('/fennec/admin/addtransaction.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/addtransaction.php">
                        <i class="fas fa-money-bill-wave"></i> Add Transaction
                    </a>
                </li>
                <?php endif; ?>

                <!-- BANNERS -->
                <li class="has-sub <?= isActive('/fennec/admin/banner/index.php'); ?> <?= isActive('/fennec/admin/banner/addbanner.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-sliders"></i> Banners
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/banner/index.php">Manage Banners</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval ?>admin/banner/addbanner.php">Add Banner</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- MENUS -->
                <li class="<?= isActive('/fennec/admin/menu/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/menu/index.php">
                        <i class="fas fa-bars"></i> Menus
                    </a>
                </li>

                <!-- PAGES -->
                <li class="has-sub <?= isActive('/fennec/admin/page/index.php'); ?> <?= isActive('/fennec/admin/page/addpage.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-copy"></i> Pages
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/page/index.php">All Pages</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval ?>admin/page/addpage.php">Add Page</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- EMAIL (Separate Category) -->
                <?php if ($isAdmin): ?>
                <li class="has-sub <?= isActive('/fennec/admin/email_templates/index.php'); ?> <?= isActive('/fennec/admin/send_emails/index.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/email_templates/index.php">Email Templates</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/send_emails/index.php">Send Email</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- ADS -->
                <li class="has-sub <?= isActive('/fennec/admin/categories/index.php'); ?> <?= isActive('/fennec/admin/subcategories/index.php'); ?>">
                    <a class="js-arrow" href="#">
                        <i class="fa fa-folder"></i> Ads
                        <span id="pending-count-badge" class="badge badge-danger" style="display:none;"></span>
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/categories/index.php">All Categories</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/subcategories/index.php">All Sub-Categories</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/product/index.php">All Ads</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/delete_closed_classifieds/index.php">Delete Expired Ads</a>
                        </li>
                    </ul>
                </li>

                <!-- BOX -->
                <li class="<?= isActive('/fennec/admin/box/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/box/index.php">
                        <i class="fas fa-desktop"></i> Box
                    </a>
                </li>

                <!-- PACKAGES -->
                <li class="<?= isActive('/fennec/admin/packages/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/packages/index.php">
                        <i class="fas fa-shopping-cart"></i> Packages
                    </a>
                </li>

                <!-- PAYMENT -->
                <li class="<?= isActive('/fennec/admin/packages/payment.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/packages/payment.php">
                        <i class="fas fa-credit-card"></i> Payment
                    </a>
                </li>

                <!-- LANGUAGES -->
                <li class="<?= isActive('/fennec/admin/lan/index.php'); ?> <?= isActive('/fennec/admin/lan/add.php'); ?> <?= isActive('/fennec/admin/lan/edit.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/lan/index.php">
                        <i class="fas fa-language"></i> Languages
                    </a>
                </li>

                <!-- LOCATION -->
                <li class="<?= isActive('/fennec/admin/location/index.php'); ?> <?= isActive('/fennec/admin/location/add.php'); ?> <?= isActive('/fennec/admin/location/edit.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/location/index.php">
                        <i class="fa-solid fa-location-dot"></i> Location
                    </a>
                </li>

                <!-- CONTACT -->
                <li class="<?= isActive('/fennec/admin/contact/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/contact/index.php">
                        <i class="fa-solid fa-address-book"></i> Contact
                    </a>
                </li>

                <!-- REPORT -->
                <li class="<?= isActive('/fennec/admin/report/index.php'); ?>">
                    <a href="<?php echo $urlval ?>admin/report/index.php">
                        <i class="fa-solid fa-bug"></i> Report
                    </a>
                </li>

                <!-- NEW: SELECT SCHEME (Add/Edit Icon Scheme) -->
                <?php if ($isAdmin): ?>
                <li class="<?= isActive('/fennec/admin/scheme/index.php'); ?>">
                    <a class="nav-link" href="<?php echo $urlval ?>admin/scheme/index.php">
                        <i class="fas fa-palette"></i> Select Scheme
                    </a>
                </li>
                <?php endif; ?>

                <!-- SETTINGS & CONFIGURATION -->
                <li class="has-sub 
                    <?= isActive('/fennec/admin/setting.php'); ?> 
                    <?= isActive('/fennec/admin/setting/web_setting.php'); ?>
                    <?php if ($isAdmin): ?>
                        <?= isActive('/fennec/admin/cleanup/index.php'); ?>
                        <?= isActive('/fennec/admin/approval_parameters/index.php'); ?>
                        <?= isActive('/fennec/admin/billing_settings/index.php'); ?>
                        <?= isActive('/fennec/admin/configure_billing_fees/index.php'); ?>
                    <?php endif; ?>
                ">
                    <a class="js-arrow" href="#">
                        <i class="fas fa-cogs"></i> Settings &amp; Configuration
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li>
                            <a href="<?php echo $urlval ?>admin/setting.php">Site Settings</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/setting/web_setting.php">Website Settings</a>
                        </li>
                        <?php if ($isAdmin): ?>
                        <li>
                            <a href="<?php echo $urlval ?>admin/cleanup/index.php">Clean Up</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/approval_parameters/index.php">Approval Parameters</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/billing_settings/index.php">Billing Settings</a>
                        </li>
                        <li>
                            <a href="<?php echo $urlval ?>admin/configure_billing_fees/index.php">Billing Fees for Featured Ads</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM fully loaded. Attempting to fetch pending count...");
    fetchPendingCount();
});

function fetchPendingCount() {
    // Ensure $urlval is set correctly (e.g. includes a trailing slash if needed)
    const endpoint = "<?= $urlval ?>admin/ajax/pending_count.php";
    console.log("Fetching endpoint:", endpoint);

    fetch(endpoint)
        .then(response => {
            console.log("Response received:", response);
            if (!response.ok) {
                throw new Error("Network response was not ok: " + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);
            if (data.pending_count > 0) {
                const badge = document.getElementById('pending-count-badge');
                badge.innerText = data.pending_count;   // e.g. "5"
                badge.style.display = "inline-block";   // or "block", your call
            }
        })
        .catch(error => console.error('Error fetching pending ads count:', error));
}
</script>


