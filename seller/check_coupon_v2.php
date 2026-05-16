<?php
$c = mysqli_connect("localhost", "root", "", "jhbewdmy_ssf_in");
if($c){
    $res = mysqli_query($c, "DESC tbl_coupon");
    while($row = mysqli_fetch_assoc($res)) echo $row['Field'] . " ";
}
?>
