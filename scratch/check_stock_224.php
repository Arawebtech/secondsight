<?php
include("../admin/inc/config.php");

$query = "SELECT * FROM tbl_product_price WHERE p_id = 224";
$result = mysqli_query($con, $query);

echo "Listing prices for product ID 224:\n";
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>
