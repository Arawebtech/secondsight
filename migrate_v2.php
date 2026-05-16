<?php
include("admin/inc/config.php");

try {
    // 1. Add ref_code to tbl_user if not exists
    $res = mysqli_query($con, "SHOW COLUMNS FROM tbl_user LIKE 'ref_code'");
    if (mysqli_num_rows($res) == 0) {
        mysqli_query($con, "ALTER TABLE tbl_user ADD COLUMN ref_code VARCHAR(50) UNIQUE AFTER id");
        echo "Added 'ref_code' column to 'tbl_user'.<br>";
        
        // Populate existing users with a ref_code (e.g., SSF001, SSF002...)
        $users = mysqli_query($con, "SELECT id FROM tbl_user");
        while ($u = mysqli_fetch_assoc($users)) {
            $code = "SSF" . str_pad($u['id'], 3, '0', STR_PAD_LEFT);
            mysqli_query($con, "UPDATE tbl_user SET ref_code = '$code' WHERE id = " . $u['id']);
        }
        echo "Populated 'ref_code' for existing users.<br>";
    }

    // 2. Create tbl_affiliate_commission
    $sql_comm = "CREATE TABLE IF NOT EXISTS tbl_affiliate_commission (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(50),
        p_id INT,
        user_id INT, -- The Partner
        buyer_id INT, -- The Customer
        commission_percentage DECIMAL(5,2),
        commission_amount DECIMAL(10,2),
        order_date VARCHAR(50),
        status ENUM('Pending', 'Paid') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($con, $sql_comm);
    echo "Created 'tbl_affiliate_commission' table.<br>";

    // 3. Add index for faster lookups
    mysqli_query($con, "CREATE INDEX IF NOT EXISTS idx_order_id ON tbl_affiliate_commission(order_id)");
    mysqli_query($con, "CREATE INDEX IF NOT EXISTS idx_partner_id ON tbl_affiliate_commission(user_id)");

    echo "Migration completed successfully.";

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage();
}
?>
