<?php
include("config.php");
require 'vendor/autoload.php'; // Path to autoload.php in your project

use Razorpay\Api\Api;

// $keyId = 'rzp_test_GxRTSzUIlNlO4W';
// $keySecret = 'ogPcSpZwzuUIWqBo0Dc80lHF';

$api = new Api($keyId, $keySecret);

$payment_id = $_POST['razorpay_payment_id'];

$payment = $api->payment->fetch($payment_id);

// Verify payment
if ($payment->status == 'captured') {
    // Payment successful, update your database or perform necessary actions
    // echo "<pre>";
    // print_r($payment);
    
    $order_id = $payment->order_id;
    $payment_id = $payment->id;
    $payment_method = $payment->method;
    $payment_status = "Success";
    $payment_date = date('d-m-Y h:i:s A');

    $query = "update tbl_payment set payment_id = '$payment_id', payment_method = '$payment_method', payment_status = '$payment_status', payment_date = '$payment_date' where payment_order_id = '$order_id'";
    // echo "Payment successful!";
    mysqli_query($con, $query);

    // exit;
    echo "<script>window.location = '../payment-success.php';</script>";
} else {
    echo "<script>window.location = '../payment-failed.php';</script>";
    // echo "Payment failed!";
}
?>