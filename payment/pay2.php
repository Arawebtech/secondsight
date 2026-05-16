<?php
error_reporting(0);
session_start();
include("../admin/inc/config.php");
include("payu_config.php"); // has $MERCHANT_KEY, $SALT, $PAYU_BASE_URL

// fallback base url
if (empty($base_url)) {
    $base_url = "https://secondsightfoundation.in/";  // replace with your real domain
}

// Get payment data
$last_pay_id = base64_decode($_GET['last_pay_id'] ?? '');
$query_pay = "SELECT * FROM tbl_payment WHERE id = '$last_pay_id' LIMIT 1";
$result_pay = mysqli_query($con, $query_pay);

if (!$result_pay || mysqli_num_rows($result_pay) == 0) {
    die("Invalid payment request.");
}
$data_pay = mysqli_fetch_assoc($result_pay);

// Always ensure payable amount is 2 decimals
$amount = number_format((float)$data_pay['payable_amount'], 2, '.', '');
$order_id = $data_pay['order_id'];

// Get user info
$query_user = "SELECT * FROM tbl_user WHERE id = '".$data_pay['user_id']."' LIMIT 1";
$result_user = mysqli_query($con, $query_user);
$user = mysqli_fetch_assoc($result_user);

$firstname = $user['name'] ?? "Customer";
$email     = $user['email'] ?? "customer@example.com";
$phone     = $user['phone'] ?? "9999999999";

// Use order_id as txnid (max 20 chars)
$txnid = substr($order_id, 0, 20);

// Safer productinfo
$productinfo = json_encode(["name" => "Order #".$order_id]);

// Hash string
$hash_string = $MERCHANT_KEY.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||||||||'.$SALT;
$hash = strtolower(hash('sha512', $hash_string));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Redirecting to PayU...</title>
</head>
<body onload="document.forms['payuForm'].submit();">
    <p style="text-align:center;font-family:Arial;margin-top:50px;">
        Redirecting to secure payment page, please wait...
    </p>

<form action="<?= $PAYU_BASE_URL ?>" method="post" name="payuForm">
    <input type="hidden" name="key" value="<?= $MERCHANT_KEY ?>" />
    <input type="hidden" name="hash" value="<?= $hash ?>"/>
    <input type="hidden" name="txnid" value="<?= $txnid ?>" />
    <input type="hidden" name="amount" value="<?= $amount ?>" />
    <input type="hidden" name="firstname" value="<?= htmlspecialchars($firstname) ?>" />
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>" />
    <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>" />
    <input type="hidden" name="productinfo" value='<?= $productinfo ?>' />
    <input type="hidden" name="surl" value="<?= $base_url ?>payment/payment-success.php" />
    <input type="hidden" name="furl" value="<?= $base_url ?>payment/payment-failed.php" />
</form>

</body>
</html>
