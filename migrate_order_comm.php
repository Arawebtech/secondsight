<?php
include("admin/inc/config.php");

try {
    // Add commission_user_id column to tbl_order
    $sql = "ALTER TABLE tbl_order ADD COLUMN commission_user_id INT(11) DEFAULT 0 AFTER applied_coupon";
    $pdo->exec($sql);
    echo "Successfully added 'commission_user_id' column to 'tbl_order'.\n";
    
    // Create an index for performance
    $sql_index = "CREATE INDEX idx_commission_user ON tbl_order(commission_user_id)";
    $pdo->exec($sql_index);
    echo "Added index for performance.\n";

} catch (PDOException $e) {
    echo "Error or already exists: " . $e->getMessage() . "\n";
}
?>
