<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "DESC tbl_user");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
