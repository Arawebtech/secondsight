<?php
$conn3 = mysqli_connect("localhost", "root", "", "jhbewdmy_ssf_in");
$res = mysqli_query($conn3, "SHOW TABLES");
echo "Tables:\n";
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}

echo "\nStructure of tbl_user_coupon:\n";
$res2 = mysqli_query($conn3, "DESC tbl_user_coupon");
while($row = mysqli_fetch_assoc($res2)) {
    print_r($row);
}

echo "\nStructure of tbl_coupon:\n";
$res3 = mysqli_query($conn3, "DESC tbl_coupon");
if($res3) {
    while($row = mysqli_fetch_assoc($res3)) {
        print_r($row);
    }
} else {
    echo "tbl_coupon not found.\n";
}
?>
