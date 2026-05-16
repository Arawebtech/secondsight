<?php
include("../admin/inc/config.php");
include("payu_config.php"); // $MERCHANT_KEY, $SALT

$status        = $_POST["status"] ?? 'failure';
$firstname     = $_POST["firstname"] ?? '';
$amount        = $_POST["amount"] ?? '';
$txnid         = $_POST["txnid"] ?? '';
$posted_hash   = $_POST["hash"] ?? '';
$key           = $_POST["key"] ?? '';
$productinfo   = $_POST["productinfo"] ?? '';
$email         = $_POST["email"] ?? '';
$transaction_id = $_POST['mihpayid'] ?? '';

/* ---------- Step 1: Verify PayU Hash ---------- */
$salt = $SALT;

if (isset($_POST["additionalCharges"])) {
    $additionalCharges = $_POST["additionalCharges"];
    $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status
                .'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
} else {
    $retHashSeq = $salt.'|'.$status
                .'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$calculated_hash = strtolower(hash("sha512", $retHashSeq));
if ($calculated_hash !== $posted_hash) {
    $status = "tampered";
}

/* ---------- Step 2: Get payment record ---------- */
$query = "SELECT * FROM tbl_payment WHERE order_id = '$txnid' LIMIT 1";
$result = mysqli_query($con, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $data_pay = mysqli_fetch_assoc($result);
    $order_id = $data_pay['order_id'];
    $user_id  = $data_pay['user_id'];

    /* ---------- Step 3: Update payment & order status ---------- */
    mysqli_query($con, "UPDATE tbl_payment 
                        SET transaction_id = '$transaction_id', 
                            payment_status = 'Failed' 
                        WHERE order_id = '$order_id'");
    mysqli_query($con, "UPDATE tbl_order SET order_status = 'Payment Failed' WHERE order_id = '$order_id'");
}

/* ---------- Step 4: Notify admin ---------- */
$body = "Payment Failed!\r\n\r\n";
$body .= "Order ID: #{$txnid}\r\n";
$body .= "User: {$firstname} ({$email})\r\n";
$body .= "Status: {$status}\r\n";
$body .= "Amount: ₹{$amount}\r\n";
$body .= "Transaction ID: {$transaction_id}\r\n";

$subject = "Payment Failed - Order #{$txnid}";
$headers  = "From: no-reply@arawebtechnologies.com\r\n";
$headers .= "Reply-To: smo@arawebtechnologies.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail("gurujimanishsharma@gmail.com", $subject, $body, $headers);

$base_url = "https://secondsightfoundation.in/";
?>
<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Failed | Second Sight Foundation</title>
    <link rel="icon" type="image/png" href="<?= $base_url;?>assets/img/favicon.png">
    
    <!-- Dependency Styles -->
    <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="libs/slick/css/slick.css">
    <link rel="stylesheet" href="libs/slick/css/slick-theme.css">
    <link rel="stylesheet" href="libs/mmenu/css/jquery.mmenu.all.css">
    <link rel="stylesheet" href="libs/slider/css/jquery.slider.css">

    <!-- Site Styles -->
    <link rel="stylesheet" href="assets/css/app.css">
<body class="page">
    <div id="page" class="hfeed page-wrapper">

        <div id="site-main" class="site-main">
            <div id="main-content" class="main-content">
                <div id="primary" class="content-area">
                    <div id="title" class="page-title" style="background-image: url(<?= $base_url;?>assets/img/banner.webp); background-size: 100% 100%;">
                        <div class="section-container">
                            <div class="content-title-heading">
                                <h1 class="text-title-heading">Payment Failed</h1>
                            </div>
                            <div class="breadcrumbs">
                                <a href="<?= $base_url; ?>">Home</a><span class="delimiter"></span>
                                Payment Failed
                            </div>
                        </div>
                    </div>

                    <div class="section-padding">
                        <div class="section-container p-l-r">
                            <div class="page-login-register">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="box-form-login">
                                            <h2 class="register">Your Payment Failed</h2>
                                            <p>Unfortunately, we could not process your payment.</p>
                                            <p>Order ID: <strong><?= htmlspecialchars($txnid); ?></strong></p>
                                            <p>Please try again or contact our support if the amount was deducted.</p>
                                            <a href="<?= $base_url; ?>checkout.php" class="btn btn-cart mt-3">Retry Payment</a>
                                            <a href="<?= $base_url; ?>" class="btn btn-cart mt-3">Go Back to Home</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div><!-- #primary -->
            </div><!-- #main-content -->
        </div>
        
    </div>

    <?php include("../include/footer.php");?>
</body>
</html>
