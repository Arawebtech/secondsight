<?php
include('admin/include/db_config.php');
$r = mysqli_query($conn3, "SHOW TABLES");
while($row = mysqli_fetch_row($r)) echo $row[0] . " ";
?>
