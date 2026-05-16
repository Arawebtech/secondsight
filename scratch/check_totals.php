<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "SELECT SUM(commission_amount) as total FROM tbl_affiliate_commission");
print_r(mysqli_fetch_assoc($res));

$res2 = mysqli_query($con, "SELECT SUM(amount_paid) as total FROM tbl_commission_payment");
print_r(mysqli_fetch_assoc($res2));
?>
