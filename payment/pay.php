<?php
error_reporting(0);
session_start();

include("../admin/inc/config.php");
$last_pay_id = base64_decode($_GET['last_pay_id']);
$query_pay = "select * from tbl_payment where id = '$last_pay_id'";
$result_pay = mysqli_query($con, $query_pay);
$count = mysqli_num_rows($result_pay);
if($count > 0)
{
    $data_pay = mysqli_fetch_assoc($result_pay);
    $amount = $data_pay['amount'];
    $discount = $data_pay['discount'];
    $shipping_charge = $data_pay['shipping_charge'];
    $payable_amount = $data_pay['payable_amount'];
}
// echo $last_pay_id;
// exit;
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>Payment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
.razorpay-payment-button{border-radius: 5px !important; font-size: 14px; color: #fff; text-transform: uppercase; background: #7a4d99; line-height: 1.5; padding: 10px 15px; font-weight: 500;}
.enqSc{padding-bottom: 0;}
.row{padding: 0px 20px !important;}
.p-2{padding: 5px !important;}
.label{color: #fff;}
.form-control{padding:3px 8px; }
.frm_div{background: #000;border-radius: 20px; width:35%; margin:40px auto}
a{text-decoration: none;}
.navigation ul ul {padding-left:0;}
.back-btn{
    border-radius: 5px !important;font-size: 14px; color: #fff; text-transform: uppercase; background: #4d4f99; line-height: 1.5; padding: 10px 15px; font-weight: 500;
}

@media (max-width: 767px)
{
    .frm_div{width:100%; margin:0 auto;}
}
</style>

</head>


<body>
<!-- header section start -->
<?php //include('../includes/header.php'); ?>

<div class="blogSc" style="background: url(images/bg3.jpg); background-size: cover;padding:40px 0">
    <div class="container">
        <div class="enqSc">
			<div class="enqForm">
				<div class="frm_div">
                    <h2 style="padding: 10px;background: #7a4d99;border-top-left-radius: 20px;border-top-right-radius: 20px;color: #fff;font-size: 28px;margin-bottom:25px">Payment Review</h2>
				    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12 col-xl-7 p-2">
                            <div class="form-holder form-holder-2">
    							<label for="member_name" class="label">Amount (Including GST)</label>
    						</div>
                        </div>
                        <div class="col-lg-12 col-sm-12 col-md-12 col-xl-5 p-2">
                            <div class="form-holder form-holder-2">
    							<label class="form-control" name="member_name" id="member_name">
                                    <?= $amount; ?>
                                </label>
    						</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-7 p-2">
                            <div class="form-holder form-holder-2">
    							<label for="member_name" class="label">Discount</label>
    						</div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-xl-5 p-2">
                            <div class="form-holder form-holder-2">
    							<label class="form-control" name="member_name" id="member_name">
                                    <?= $discount; ?>
                                </label>
    						</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-7 p-2">
                            <div class="form-holder form-holder-2">
    							<label for="member_name" class="label">Shipping Charge</label>
    						</div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-xl-5 p-2">
                            <div class="form-holder form-holder-2">
    							<label class="form-control" name="member_name" id="member_name">
                                    <?= $shipping_charge; ?>
                                </label>
    						</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-7 p-2">
                            <div class="form-holder form-holder-2">
    							<label for="member_name" class="label">Payable Amount</label>
    						</div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-xl-5 p-2">
                            <div class="form-holder form-holder-2">
    							<label class="form-control" name="member_name" id="member_name">
                                    <?= $payable_amount; ?>
                                </label>
    						</div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:20px;padding-bottom: 15px !important;">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-5 p-2" style="text-align: right;">
                            <div class="form-holder form-holder-2">
    							<label for="" class="label">
                                    <button class="back-btn" onclick="history.back()">Back</button>
                                </label>
    						</div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-xl-7 p-2">
                            <div class="form-holder form-holder-2">





<?php
require 'vendor/autoload.php'; // Path to autoload.php in your project

use Razorpay\Api\Api;

// $keyId = 'rzp_live_EA2GfQw1fDbQ23';
// $keySecret = 'Ull1SPj1Kp6IeDAOQev1MKm2';

$api = new Api($keyId, $keySecret);

$order = $api->order->create(array(
    'receipt' => 'order_rcptid_11',
    'amount' => $payable_amount * 100, // amount in the smallest currency unit
    'currency' => 'INR'
));
$order_id = $order->id;
$query = "update tbl_payment set payment_order_id='$order_id' where id = '$last_pay_id'";
mysqli_query($con, $query);
?>
<form action="verify.php" method="POST">
    <script
        src="https://checkout.razorpay.com/v1/checkout.js"
        data-key="<?php echo $keyId; ?>"
        data-amount="<?php echo $order->amount; ?>"
        data-currency="INR"
        data-order_id="<?php echo $order->id; ?>"
        data-buttontext="Pay with Goonmala"
        data-name="Goonmala"
        data-description=""
        data-prefill.name="Goonmala"
        data-prefill.email="goonmala2021@gmail.com"
        data-theme.color="#F37254"
    ></script>
    <input type="hidden" custom="Hidden Element" name="hidden">
</form>





                </div>
            </div>
          </div>
		</div>
      </div>
   </div>
  </div>
</div>



</body>
</html>