<?php
include("admin/inc/config.php");

echo "<h3>Resetting Commission Data for Clean Testing...</h3>";

// 1. Clear existing tracking tables
mysqli_query($con, "TRUNCATE TABLE tbl_affiliate_commission");
mysqli_query($con, "TRUNCATE TABLE tbl_commission_payment");
echo "Cleared all commission and payment records.<br>";

// 2. Re-migrate the 9 successful orders
echo "Re-migrating real orders from tbl_order...<br>";

$q = mysqli_query($con, "SELECT o.* FROM tbl_order o WHERE o.commission_user_id != 0 AND o.order_status = 'Success'");
$inserted = 0;

while ($ord = mysqli_fetch_assoc($q)) {
    $order_id = $ord['order_id'];
    $p_id = $ord['p_id'];
    $partner_id = $ord['commission_user_id'];
    
    // Find percentage
    $coupon_code = $ord['applied_coupon'];
    $perc = 0;
    if (!empty($coupon_code)) {
        $stmt_cp = $pdo->prepare("SELECT uc.percentage 
                                FROM tbl_user_coupon uc 
                                JOIN tbl_coupon c ON uc.coupon_id = c.id 
                                WHERE uc.user_id = ? AND LOWER(c.coupon_code) = LOWER(?) LIMIT 1");
        $stmt_cp->execute([$partner_id, $coupon_code]);
        $perc = $stmt_cp->fetchColumn() ?: 0;
    }
    if ($perc == 0) {
        $stmt_cp = $pdo->prepare("SELECT percentage FROM tbl_user_coupon WHERE user_id = ? AND (p_id = ? OR p_id IS NULL) ORDER BY p_id DESC LIMIT 1");
        $stmt_cp->execute([$partner_id, $p_id]);
        $perc = $stmt_cp->fetchColumn() ?: 0;
    }

    if ($perc > 0) {
        $comm_amt = ($ord['p_actual_price'] * $ord['no_of_item']) * ($perc / 100);
        $sql_ins = "INSERT INTO tbl_affiliate_commission (order_id, p_id, user_id, buyer_id, commission_percentage, commission_amount, order_date, status) 
                    VALUES ('$order_id', '$p_id', '$partner_id', '".$ord['user_id']."', '$perc', '$comm_amt', '".$ord['order_date']."', 'Pending')";
        mysqli_query($con, $sql_ins);
        $inserted++;
    }
}

echo "Done! Reset $inserted commission records. Balances should now be accurate.<br>";
?>
