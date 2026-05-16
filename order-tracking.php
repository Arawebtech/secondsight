<?php
include("admin/inc/config.php");
session_start();
$user_id = "";
if (isset($_SESSION['user_id']))
  $user_id = $_SESSION['user_id'];
else if (isset($_SESSION['temp_user_id']))
  $user_id = $_SESSION['temp_user_id'];

include('include/header.php');

?>

  
    <!-- Order Detail Section Start -->
    <section class="order-detail">
        <div class="container-fluid-lg">
            <div class="row g-sm-4 g-3">
                <div class="col-xxl-3 col-xl-4 col-lg-6">
                    <div class="order-image bg-light">
                        <img src="https://5.imimg.com/data5/SELLER/Default/2023/7/327613606/SA/UE/TK/105984570/1.png" class="img-fluid  lazyload" alt="">
                    </div>
                </div>

                <div class="col-xxl-9 col-xl-8 col-lg-6">
                    <div class="row g-sm-4 g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i data-feather="package" class="text-content"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">Tracking Code</h5>
                                    <h2 class="theme-color">MH4285UY</h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i data-feather="truck" class="text-content"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">Service</h5>
                                    <img src="https://themes.pixelstrap.com/fastkart/assets/images/inner-page/brand-name.svg"
                                        class="img-fluid blur-up lazyload" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i class="text-content" data-feather="info"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">Package Info</h5>
                                    <h4>Letter</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i class="text-content" data-feather="crosshair"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">From</h5>
                                    <h4>STR. Smardan 9, Bucuresti, romania.</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i class="text-content" data-feather="map-pin"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">Destination</h5>
                                    <h4>Flokagata 24, 105 Reykjavik, Iceland</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="order-details-contain">
                                <div class="order-tracking-icon">
                                    <i class="text-content" data-feather="calendar"></i>
                                </div>

                                <div class="order-details-name">
                                    <h5 class="text-content">Estimated Time</h5>
                                    <h4>7 Frb, 05:05pm</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 overflow-hidden">
                            <ol class="progtrckr">
                                <li class="progtrckr-done">
                                    <h5>Order Processing</h5>
                                    <h6>05:43 AM</h6>
                                </li>
                                <li class="progtrckr-done">
                                    <h5>Pre-Production</h5>
                                    <h6>01:21 PM</h6>
                                </li>
                                <li class="progtrckr-done">
                                    <h5>In Production</h5>
                                    <h6>Processing</h6>
                                </li>
                                <li class="progtrckr-todo">
                                    <h5>Shipped</h5>
                                    <h6>Pending</h6>
                                </li>
                                <li class="progtrckr-todo">
                                    <h5>Delivered</h5>
                                    <h6>Pending</h6>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Order Detail Section End -->

    <!-- Order Table Section Start -->
    <section class="order-table-section section-b-space">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table order-tab-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Order Placed</td>
                                    <td>26 Sep 2021</td>
                                    <td>12:00 AM</td>
                                    <td>California</td>
                                </tr>

                                <tr>
                                    <td>Preparing to Ship</td>
                                    <td>03 Oct 2021</td>
                                    <td>12:00 AM</td>
                                    <td>Canada</td>
                                </tr>

                                <tr>
                                    <td>Shipped</td>
                                    <td>04 Oct 2021</td>
                                    <td>12:00 AM</td>
                                    <td>America</td>
                                </tr>

                                <tr>
                                    <td>Delivered</td>
                                    <td>10 Nav 2021</td>
                                    <td>12:00 AM</td>
                                    <td>Germany</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Order Table Section End -->

  <?php
include ('include/footer.php');

?>