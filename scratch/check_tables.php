<?php
include("admin/inc/config.php");

echo "<h3>All Tables:</h3>";
$res = mysqli_query($con, "SHOW TABLES");
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . "<br>";
}

echo "<h3>tbl_user Structure:</h3>";
$res = mysqli_query($con, "DESCRIBE tbl_user");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

echo "<h3>tbl_coupon Structure:</h3>";
$res = mysqli_query($con, "DESCRIBE tbl_coupon");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

echo "<h3>tbl_user_coupon Structure:</h3>";
$res = mysqli_query($con, "DESCRIBE tbl_user_coupon");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . " - Null: " . $row['Null'] . "<br>";
}
echo "<h3>tbl_order Structure:</h3>";
$res = mysqli_query($con, "DESCRIBE tbl_order");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
echo "<h3>tbl_product Structure:</h3>";
$res = mysqli_query($con, "DESCRIBE tbl_product");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
?>
