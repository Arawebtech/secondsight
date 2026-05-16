<?php
$c = mysqli_connect("localhost", "root", "", "jhbewdmy_ssf_in");
$r = mysqli_query($c, "DESC tbl_user");
while ($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}
?>
