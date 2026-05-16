<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "DESC tbl_cart");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
