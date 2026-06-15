<?php
session_start();
include("../admin/inc/config.php");
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'totals'  => []
];

// Validate coupon code
if (!isset($_POST['coupon_code']) || empty(trim($_POST['coupon_code']))) {
    $response['message'] = "Coupon code is required.";
    echo json_encode($response);
    exit;
}

$coupon_code = mysqli_real_escape_string($con, trim($_POST['coupon_code']));

// Fetch all matching coupon rules (same coupon code can be assigned to different products)
$query  = "SELECT p_id, amount, type FROM tbl_coupon WHERE coupon_code = '$coupon_code'";
$result = mysqli_query($con, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    $response['message'] = "Invalid or expired coupon code.";
    echo json_encode($response);
    exit;
}

$rules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rules[] = [
        'p_id'   => (int)$row['p_id'],
        'amount' => (float)$row['amount'],
        'type'   => $row['type']
    ];
}

// Resolve user id
$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? 0;

// Ensure cart not empty
$query_cart_check = "SELECT COUNT(*) AS item_count FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = '0'";
$result_check = mysqli_query($con, $query_cart_check);
$count_data = mysqli_fetch_assoc($result_check);
if ((int)$count_data['item_count'] <= 0) {
    $response['message'] = "Cannot apply coupon on an empty cart.";
    echo json_encode($response);
    exit;
}

// Pull cart and compute totals
$query_cart  = "SELECT p_id, p_actual_price, p_gst, p_price, no_of_item, weight 
                FROM tbl_cart 
                WHERE user_id = '$user_id' AND is_ordered = '0'";
$result_cart = mysqli_query($con, $query_cart);

$sub_total = 0.0;
$gst       = 0.0;
$total     = 0.0;
$p_total_weight = 0.0;
$applied_coupon = 0.0;
$eligible_items_count = 0;

// Identify if there is a global rule
$global_rule = null;
foreach ($rules as $rule) {
    if ($rule['p_id'] === 0) {
        $global_rule = $rule;
        break;
    }
}

while ($cart = mysqli_fetch_assoc($result_cart)) {
    $qty          = (int)$cart['no_of_item'];
    $line_actual  = (float)$cart['p_actual_price'] * $qty;
    $line_gst     = (float)$cart['p_gst'] * $qty;
    $line_total   = (float)$cart['p_price'] * $qty;
    $line_weight  = (float)$cart['weight'] * $qty;
    $p_id         = (int)$cart['p_id'];

    $sub_total      += $line_total;   // Base subtotal
    $gst            += $line_gst;     // Total GST
    $total          += $line_actual;  // Total including GST
    $p_total_weight += $line_weight;

    // Check if there is a specific rule for this product in rules list
    $specific_rule = null;
    foreach ($rules as $rule) {
        if ($rule['p_id'] === $p_id) {
            $specific_rule = $rule;
            break;
        }
    }

    if ($specific_rule) {
        $eligible_items_count++;
        if ($specific_rule['type'] === 'percent') {
            $discount = $line_actual * ($specific_rule['amount'] / 100);
        } else { // flat
            $discount = $specific_rule['amount'];
        }
        $applied_coupon += min($line_actual, $discount);
    } else if ($global_rule) {
        $eligible_items_count++;
        if ($global_rule['type'] === 'percent') {
            $discount = $line_actual * ($global_rule['amount'] / 100);
            $applied_coupon += min($line_actual, $discount);
        }
    }
}

// Apply global flat rule if present to the remaining total
if ($global_rule && $global_rule['type'] === 'flat') {
    $remaining_eligible = max(0, $total - $applied_coupon);
    $applied_coupon += min($remaining_eligible, $global_rule['amount']);
}

// If no eligible items, coupon cannot apply
if ($eligible_items_count === 0 && empty($global_rule)) {
    $response['message'] = "This coupon is not applicable to the products in your cart.";
    echo json_encode($response);
    exit;
}

// Weight-based shipping charge
$shipping_charge = 0;
if ($p_total_weight >= 500 && $p_total_weight < 1000) {
    $shipping_charge = 30;
} elseif ($p_total_weight >= 1000 && $p_total_weight < 2000) {
    $shipping_charge = 60;
} elseif ($p_total_weight >= 2000 && $p_total_weight < 3000) {
    $shipping_charge = 90;
} elseif ($p_total_weight >= 3000 && $p_total_weight < 5000) {
    $shipping_charge = 120;
} elseif ($p_total_weight >= 5000) {
    $shipping_charge = 120;
}

// Base shipping zone charge + free shipping threshold
$base_shipping_charge = 0.0;
$free_shipping = 0.0;
$s_state = $_SESSION['s_state'] ?? '';
if (!empty($s_state)) {
    $q_zone = "SELECT shipping_charge AS base_shipping_charge, free_shipping 
               FROM tbl_shipping_zone 
               WHERE state_name = '".mysqli_real_escape_string($con, $s_state)."' LIMIT 1";
    $r_zone = mysqli_query($con, $q_zone);
    if ($zone = mysqli_fetch_assoc($r_zone)) {
        $base_shipping_charge = (float)$zone['base_shipping_charge'];
        $free_shipping        = (float)$zone['free_shipping'];
    }
}

$total_shipping = $base_shipping_charge + $shipping_charge;

// Free shipping rule (cart total BEFORE discount)
if ($total >= $free_shipping && $free_shipping > 0) {
    $total_shipping = 0.0;
}

// Final grand total
$grand_total = max(0, ($total - $applied_coupon + $total_shipping));

// Persist coupon in session
$_SESSION['coupon'] = [
    'code'   => $coupon_code,
    'amount' => $applied_coupon,
    'p_id'   => count($rules) === 1 ? $rules[0]['p_id'] : -1,
    'type'   => count($rules) === 1 ? $rules[0]['type'] : 'mixed'
];

// Response
$response['success'] = true;
$response['message'] = "Coupon applied successfully.";
$response['totals'] = [
    'sub_total'       => number_format($sub_total, 2, '.', ''),
    'gst'             => number_format($gst, 2, '.', ''),
    'total'           => number_format($total, 2, '.', ''),
    'coupon_code'     => $coupon_code,
    'coupon_amount'   => number_format($applied_coupon, 2, '.', ''),
    'shipping_charge' => number_format($total_shipping, 2, '.', ''),
    'grand_total'     => number_format($grand_total, 2, '.', '')
];

echo json_encode($response);
?>
