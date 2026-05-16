<?php
include("admin/inc/config.php");
$sql = "ALTER TABLE tbl_user 
        ADD bank_name VARCHAR(100), 
        ADD account_no VARCHAR(50), 
        ADD ifsc_code VARCHAR(20), 
        ADD account_holder VARCHAR(100)";
if(mysqli_query($con, $sql)) {
    echo "Columns added successfully";
} else {
    echo "Error adding columns: " . mysqli_error($con);
}
?>
