<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "ALTER TABLE tbl_user_coupon ADD COLUMN p_id int(11) NULL AFTER coupon_id, ADD COLUMN custom_url varchar(255) NULL AFTER p_id");
if($res) echo "Table altered successfully";
else echo "Error: " . mysqli_error($con);
?>
