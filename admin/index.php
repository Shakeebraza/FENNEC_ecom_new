<?php
require_once("../global.php");
include_once('header.php');

$userData = $fun->getUserRegistrationData();
$datesJS = json_encode($userData['dates']);
$userCountsJS = json_encode($userData['userCounts']);
$totalNewMembers = $userData['totalNewMembers'];

$productData = $fun->getProductAdditionData();

$productCountsJS = json_encode($productData['productCounts']);
$productLabelsJS = json_encode($productData['dates']);
$totalProducts = $productData['totalProducts'];

$productCounts = $fun->getProductCounts();
$standardCount = $productCounts['standard'];
$premiumCount = $productCounts['premium'];
$goldCount = $productCounts['gold'];
$totalProducts = $standardCount + $premiumCount + $goldCount;

// Compute percentages
// $standardPercentage = ($standardCount / $totalProducts) * 100;
// $premiumPercentage = ($premiumCount / $totalProducts) * 100;
// $goldPercentage = ($goldCount / $totalProducts) * 100;

$userVerificationCounts = $fun->getUserVerificationCounts();
$verifiedCount = $userVerificationCounts['verified_count'];
$notVerifiedCount = $userVerificationCounts['not_verified_count'];
$verificationData = json_encode([$verifiedCount, $notVerifiedCount]);

?>


<div class="page-container">

    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="overview-wrap">
                            <h2 class="title-1">overview</h2>
                            <button class="au-btn au-btn-icon au-btn--blue">
                                <!-- <i class="zmdi zmdi-plus"></i>add item</button> -->
                        </div>
                    </div>
                </div>
                <div class="row m-t-25">
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c1">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="zmdi zmdi-account-o"></i>
                                    </div>
                                    <div class="text">
                                        <h2><?= $totalNewMembers ?></h2>
                                        <span>New members this Month</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c2">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="zmdi zmdi-shopping-cart"></i>
                                    </div>
                                    <div class="text">
                                        <h2><?= $totalProducts ?></h2>
                                        <span>Total Product</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c3">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="zmdi zmdi-calendar-note"></i>
                                    </div>
                                    <div class="text">
                                        <h2>1,086</h2>
                                        <span>this week</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c4">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="zmdi zmdi-money"></i>
                                    </div>
                                    <div class="text">
                                        <h2>$1,060,386</h2>
                                        <span>total earnings</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart4"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="au-card recent-report">
                            <div class="au-card-inner">
                                <h3 class="title-2">Product Ratio</h3>
                                <div class="chart-info">
                                    <div class="chart-info__left">
                                        <div class="chart-note">
                                            <span class="dot dot--blue"></span>
                                            <span>Standard</span>
                                        </div>
                                        <div class="chart-note mr-0">
                                            <span class="dot dot--green" style="background: gold;"></span>
                                            <span>gold</span>
                                        </div>
                                        <div class="chart-note mr-0">
                                            <span class="dot dot--green"></span>
                                            <span>premium</span>
                                        </div>
                                    </div>
                                    <div class="chart-info__right">
                                        <!-- <div class="chart-statis">
                                                    <span class="index incre">
                                                        <i class="zmdi zmdi-long-arrow-up"></i>25%</span>
                                                    <span class="label">products</span>
                                                </div>
                                                <div class="chart-statis mr-0">
                                                    <span class="index decre">
                                                        <i class="zmdi zmdi-long-arrow-down"></i>10%</span>
                                                    <span class="label">services</span>
                                                </div> -->
                                    </div>
                                </div>
                                <div class="recent-report__chart">
                                    <canvas id="recent-rep-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="au-card chart-percent-card">
                            <div class="au-card-inner">
                                <h3 class="title-2 tm-b-5">Verified User %</h3>
                                <div class="row no-gutters">
                                    <div class="col-xl-6">
                                        <div class="chart-note-wrap">
                                            <div class="chart-note mr-0 d-block">
                                                <span class="dot dot--blue"></span>
                                                <span>Verified</span>
                                            </div>
                                            <div class="chart-note mr-0 d-block">
                                                <span class="dot dot--red"></span>
                                                <span>Not Verified</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="percent-chart">
                                            <canvas id="percent-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">

                    <div class="top-campaign">
                        <h3 class="title-3 m-b-30">Site Statistics</h3>
                        <div class="table-responsive">
                            <table class="table table-top-campaign">
                                <tbody>
                                    <?php
                                $statistics = $fun->getSiteStatistics(); 
                                foreach ($statistics as $key => $value) {
                                    echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>


<?php
include_once('footer.php');
include_once('chats.php');


?>

</body>

</html>