<?php
include("admin/inc/config.php");
$sql = "CREATE TABLE IF NOT EXISTS tbl_payout_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Paid', 'Rejected') DEFAULT 'Pending',
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_date DATETIME,
    admin_remark TEXT
)";
if(mysqli_query($con, $sql)) {
    echo "Table tbl_payout_requests created successfully";
} else {
    echo "Error creating table: " . mysqli_error($con);
}
?>
