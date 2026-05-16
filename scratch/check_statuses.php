<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "SELECT order_status, COUNT(*) as cnt FROM tbl_order GROUP BY order_status");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
