<?php
include("admin/inc/config.php");

echo "Starting migration of old orders to tbl_affiliate_commission...<br>";

// 1. Fetch all successful orders with a commission partner
$q = mysqli_query($con, "SELECT o.*, u.id as partner_id 
                         FROM tbl_order o 
                         JOIN tbl_user u ON o.commission_user_id = u.id 
                         WHERE o.order_status = 'Success'");

$inserted = 0;
$skipped = 0;

while ($ord = mysqli_fetch_assoc($q)) {
    $order_id = $ord['order_id'];
    $p_id = $ord['p_id'];
    $partner_id = $ord['commission_user_id'];
    
    // Check if record already exists to avoid duplicates
    $check = mysqli_query($con, "SELECT id FROM tbl_affiliate_commission WHERE order_id = '$order_id' AND p_id = '$p_id' AND user_id = '$partner_id'");
    if (mysqli_num_rows($check) > 0) {
        $skipped++;
        continue;
    }

    // Find the commission percentage assigned to this user for this product or coupon
    // We'll try to match the applied coupon or the product
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
    
    // If no coupon match, try product match or general match
    if ($perc == 0) {
        $stmt_cp = $pdo->prepare("SELECT percentage FROM tbl_user_coupon WHERE user_id = ? AND (p_id = ? OR p_id IS NULL) ORDER BY p_id DESC LIMIT 1");
        $stmt_cp->execute([$partner_id, $p_id]);
        $perc = $stmt_cp->fetchColumn() ?: 0;
    }

    if ($perc > 0) {
        $comm_amt = ($ord['p_actual_price'] * $ord['no_of_item']) * ($perc / 100);
        $status = 'Paid'; // Assume old orders are already accounted for or mark as paid if they were
        
        // Let's check if this order was already paid in tbl_commission_payment? 
        // Actually, let's just mark as 'Pending' and let the balance logic handle it.
        // If they have a payment record, the balance will naturally go to 0.
        
        $sql_ins = "INSERT INTO tbl_affiliate_commission (order_id, p_id, user_id, buyer_id, commission_percentage, commission_amount, order_date, status) 
                    VALUES ('$order_id', '$p_id', '$partner_id', '".$ord['user_id']."', '$perc', '$comm_amt', '".$ord['order_date']."', 'Pending')";
        mysqli_query($con, $sql_ins);
        $inserted++;
    }
}

echo "Migration finished.<br>";
echo "Inserted: $inserted records.<br>";
echo "Skipped: $skipped (already exists).";
?>
