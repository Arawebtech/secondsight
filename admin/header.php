<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if(!isset($_SESSION['user'])) {
	header('location: login.php');
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin Panel</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" href="<?=BASE_URL;?>assets/images/logo-fav.png" type="image/png">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/jquery.fancybox.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">
	<link rel="stylesheet" href="css/on-off-switch.css"/>
	<link rel="stylesheet" href="css/summernote.css">
	<link rel="stylesheet" href="style.css">
</head>

<body class="hold-transition fixed skin-blue sidebar-mini">
	<div class="wrapper">
		<header class="main-header">
			<a href="index.php" class="logo">
				<span class="logo-lg">Second Sight Foundation</span>
			</a>
			<nav class="navbar navbar-static-top">
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<span style="float:left;line-height:50px;color:#fff;padding-left:15px;font-size:18px;">Admin Panel</span>
    			<!-- Top Bar ... User Inforamtion .. Login/Log out Area -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<!-- Notifications Menu -->
						<?php
						$q_noti = mysqli_query($con, "SELECT pr.*, u.full_name FROM tbl_payout_requests pr JOIN tbl_user u ON pr.user_id = u.id WHERE pr.status = 'Pending' ORDER BY pr.id DESC LIMIT 5");
						$noti_count = mysqli_num_rows($q_noti);
						?>
						<li class="dropdown notifications-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-bell-o"></i>
								<?php if($noti_count > 0): ?>
									<span class="label label-warning"><?= $noti_count; ?></span>
								<?php endif; ?>
							</a>
							<ul class="dropdown-menu">
								<li class="header">You have <?= $noti_count; ?> pending payouts</li>
								<li>
									<ul class="menu">
										<?php while($n = mysqli_fetch_assoc($q_noti)): ?>
										<li>
											<a href="payout-requests.php">
												<i class="fa fa-money text-aqua"></i> ₹<?= $n['amount']; ?> requested by <?= $n['full_name']; ?>
											</a>
										</li>
										<?php endwhile; ?>
									</ul>
								</li>
								<li class="footer"><a href="payout-requests.php">View all</a></li>
							</ul>
						</li>

						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../assets/img/admin/<?php echo $_SESSION['user']['photo']; ?>" class="user-image w-100" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['user']['full_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Edit Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Log out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>

  		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>
		<!-- Side Bar to Manage Shop Activities -->
  		<aside class="main-sidebar">
    		<section class="sidebar">
      			<ul class="sidebar-menu">
			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
			          </a>
			        </li>
			        <!-- <li class="treeview <?php if( ($cur_page == 'settings.php') ) {echo 'active';} ?>">
			          <a href="settings.php">
			            <i class="fa fa-sliders"></i> <span>Website Settings</span>
			          </a>
			        </li> -->
                    <li class="treeview <?php if( ($cur_page == 'size.php') || ($cur_page == 'size-add.php') || ($cur_page == 'size-edit.php') || ($cur_page == 'color.php') || ($cur_page == 'color-add.php') || ($cur_page == 'color-edit.php') || ($cur_page == 'country.php') || ($cur_page == 'country-add.php') || ($cur_page == 'country-edit.php') || ($cur_page == 'shipping-cost.php') || ($cur_page == 'shipping-cost-edit.php') || ($cur_page == 'top-category.php') || ($cur_page == 'top-category-add.php') || ($cur_page == 'top-category-edit.php') || ($cur_page == 'mid-category.php') || ($cur_page == 'mid-category-add.php') || ($cur_page == 'mid-category-edit.php') || ($cur_page == 'end-category.php') || ($cur_page == 'end-category-add.php') || ($cur_page == 'end-category-edit.php') ) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-cogs"></i>
                            <span>Shop Settings</span>
                            <span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="size.php"><i class="fa fa-circle-o"></i> Size</a></li>
                            <li><a href="color.php"><i class="fa fa-circle-o"></i> Color</a></li>
                            <!-- <li><a href="country.php"><i class="fa fa-circle-o"></i> Country</a></li> -->
                            <!-- <li><a href="shipping-cost.php"><i class="fa fa-circle-o"></i> Shipping Cost</a></li> -->
                            <li><a href="top-category.php"><i class="fa fa-circle-o"></i> Top Level Category</a></li>
                            <li><a href="mid-category.php"><i class="fa fa-circle-o"></i> Mid Level Category</a></li>
                            <li><a href="end-category.php"><i class="fa fa-circle-o"></i> End Level Category</a></li>
                        </ul>
                    </li>
                    <li class="treeview <?php if( ($cur_page == 'product.php') || ($cur_page == 'product-add.php') || ($cur_page == 'product-edit.php') ) {echo 'active';} ?>">
                        <a href="product.php">
                            <i class="fa fa-shopping-bag"></i> <span>Product Management</span>
                        </a>
                    </li>
                    <li class="treeview <?php if( ($cur_page == 'product-detail.php') || ($cur_page == 'product-detail-add.php') || ($cur_page == 'product-detail-edit.php') ) {echo 'active';} ?>">
                        <a href="product-detail.php">
                            <i class="fa fa-shopping-bag"></i> <span>Product Detail</span>
                        </a>
                    </li>
                    <li class="treeview <?php if( ($cur_page == 'order.php') ) {echo 'active';} ?>">
                        <a href="order.php">
                            <i class="fa fa-sticky-note"></i> <span>Order Management</span>
                        </a>
                    </li>
                    <li class="treeview <?php if( ($cur_page == 'payment.php') ) {echo 'active';} ?>">
                        <a href="payment.php">
                            <i class="fa fa-sticky-note"></i> <span>Payment History</span>
                        </a>
                    </li>
			        <li class="treeview <?php if( ($cur_page == 'coupon.php') ) {echo 'active';} ?>">
			          <a href="coupon.php">
			            <i class="fa fa-picture-o"></i> <span>Coupon</span>
			          </a>
			        </li>                     
			        <li class="treeview <?php if( ($cur_page == 'user-coupon.php') || ($cur_page == 'user-coupon-add.php') || ($cur_page == 'user-coupon-edit.php') ) {echo 'active';} ?>">
			          <a href="user-coupon.php">
			            <i class="fa fa-percent"></i> <span>User Coupon</span>
			          </a>
			        </li>                     
			        <li class="treeview <?php if( ($cur_page == 'commission-report.php') ) {echo 'active';} ?>">
			          <a href="commission-report.php">
			            <i class="fa fa-money"></i> <span>Commission Report</span>
			          </a>
			        </li>
                    <?php
                        $q_payout = mysqli_query($con, "SELECT id FROM tbl_payout_requests WHERE status = 'Pending'");
                        $payout_count = mysqli_num_rows($q_payout);
                    ?>
                    <li class="treeview <?php if($cur_page == 'payout-requests.php') {echo 'active';} ?>">
                        <a href="payout-requests.php">
                            <i class="fa fa-hand-holding-usd"></i> <span>Payout Requests</span>
                            <?php if($payout_count > 0): ?>
                                <span class="pull-right-container"><span class="label label-danger pull-right"><?= $payout_count; ?></span></span>
                            <?php endif; ?>
                        </a>
                    </li>
			        <li class="treeview <?php if( ($cur_page == 'shipping-zone.php') ) {echo 'active';} ?>">
			          <a href="shipping-zone.php">
			            <i class="fa fa-list-ol"></i> <span>Shipping Zone</span>
			          </a>
			        </li>
			        <!-- <li class="treeview <?php if( ($cur_page == 'faq.php') ) {echo 'active';} ?>">
			          <a href="faq.php">
			            <i class="fa fa-question-circle"></i> <span>FAQ</span>
			          </a>
			        </li> -->
					<li class="treeview <?php if( ($cur_page == 'customer.php') || ($cur_page == 'customer-add.php') || ($cur_page == 'customer-edit.php') ) {echo 'active';} ?>">
			          <a href="customer.php">
			            <i class="fa fa-user-plus"></i> <span>Users</span>
			          </a>
			        </li>
			        
			        <li class="treeview <?php if( ($cur_page == 'tagline.php') ) {echo 'active';} ?>">
			          <a href="tagline.php">
			            <i class="fa fa-picture-o"></i> <span>Tagline</span>
			          </a>
			        </li>
			        <!-- <li class="treeview <?php if( ($cur_page == 'page.php') ) {echo 'active';} ?>">
			          <a href="page.php">
			            <i class="fa fa-tasks"></i> <span>Page Settings</span>
			          </a>
			        </li> -->
			        <!-- <li class="treeview <?php if( ($cur_page == 'social-media.php') ) {echo 'active';} ?>">
			          <a href="social-media.php">
			            <i class="fa fa-globe"></i> <span>Social Media</span>
			          </a>
			        </li> -->
			        <!-- <li class="treeview <?php if( ($cur_page == 'subscriber.php')||($cur_page == 'subscriber.php') ) {echo 'active';} ?>">
			          <a href="subscriber.php">
			            <i class="fa fa-hand-o-right"></i> <span>Subscriber</span>
			          </a>
			        </li> -->
      			</ul>
    		</section>
  		</aside>

  		<div class="content-wrapper">