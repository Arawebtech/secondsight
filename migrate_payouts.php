<?php
include("admin/inc/config.php");
$sql = "CREATE TABLE IF NOT EXISTS tbl_commission_payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    coupon_id INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    notes TEXT
)";
if (mysqli_query($con, $sql)) {
    echo "Successfully created tbl_commission_payment table.\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}
?>
