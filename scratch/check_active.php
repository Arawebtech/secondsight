<?php
include("../admin/inc/config.php");

$query = "SELECT p_id, p_name, p_is_active FROM tbl_product WHERE p_id = 227";
$result = mysqli_query($con, $query);

echo "Checking product status for ID 227:\n";
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>
