<?php
include('include/db_config.php');

if (isset($_GET['cust_id'])) {
    $cust_id = $_GET['cust_id'];
    
    // Fetch from the new SSF database with JOIN to include matched coupon details
    $query = "
    SELECT 
        u.full_name as name, 
        u.phone, 
        u.email,
        c.coupon_code,
        uc.percentage
    FROM tbl_user u
    LEFT JOIN tbl_user_coupon uc ON u.id = uc.user_id
    LEFT JOIN tbl_coupon c ON uc.coupon_id = c.id
    WHERE u.id = '$cust_id'
    ";
    
    $res = mysqli_query($conn3, $query);
    
    if ($res) {
        $data = mysqli_fetch_assoc($res);
        
        // Return null for empty fields if no match
        $data['coupon_code'] = $data['coupon_code'] ?? '';
        $data['percentage'] = $data['percentage'] ?? '';
        
        echo json_encode($data);
    } else {
        echo json_encode(['error' => mysqli_error($conn3)]);
    }
}
?>
