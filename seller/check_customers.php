<?php
include('admin/include/db_config.php');
$res = mysqli_query($conn3, "SHOW COLUMNS FROM tbl_user");
while($row = mysqli_fetch_row($res)) echo $row[0] . " ";
?>
