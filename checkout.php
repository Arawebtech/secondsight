<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("admin/inc/config.php");
if (!isset($_SESSION['user_id'])) {
    // Redirect non-logged-in users to login or cart page
    echo "<script>alert('You must be logged in to access checkout'); window.location.href = 'login.php';</script>";
    exit;
}
// Get user ID from session
$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
if (!$user_id) {
    die("User not logged in.");
}

// Check if cart is empty
$cart_check_query = "SELECT COUNT(*) as total_items FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = '0'";
$cart_check_result = mysqli_query($con, $cart_check_query);
$cart_data = mysqli_fetch_assoc($cart_check_result);

if ($cart_data['total_items'] == 0) {
    echo "<script>alert('Your cart is empty. Please add items before proceeding to checkout.'); window.location.href='cart.php';</script>";
    exit;
}
// echo $user_id; exit;

// --- Prefill addresses from DB ---
$query_s = "SELECT * FROM tbl_shipping_address WHERE user_id = '$user_id' LIMIT 1";
$result_s = mysqli_query($con, $query_s);
if ($result_s && mysqli_num_rows($result_s) > 0) {
    $data_s = mysqli_fetch_assoc($result_s);
    $s_state = $data_s['state'];
    $full_shipping_address = $data_s['name'] . ", " . $data_s['phone_no'] . ", " . $data_s['building_no'] . ", " . $data_s['street_address'] . ", " . $data_s['landmark'] . ", " . $data_s['town'] . ", " . $data_s['district'] . ", " . $data_s['state'] . ", " . $data_s['pincode'] . ", " . $data_s['gst_no'];
    $_SESSION['full_shipping_address'] = $full_shipping_address;
    $_SESSION['s_state'] = $s_state;
}

$s_state = $_SESSION['s_state'] ?? '';
$full_billing_address = $_SESSION['full_billing_address'] ?? '';

// --- Shipping Zone Query ---
$query = "SELECT shipping_charge base_shipping_charge, free_shipping 
          FROM tbl_shipping_zone 
          WHERE state_name = '$s_state'";
$result = mysqli_query($con, $query);
$info = mysqli_fetch_assoc($result);
$base_shipping_charge = $info['base_shipping_charge'] ?? 0;
// $free_shipping = $info['free_shipping'] ?? 0;

$base_shipping_charge = (float) $base_shipping_charge;
$shipping_charge = isset($_POST['shipping_charge']) ? (float) $_POST['shipping_charge'] : 0;
$total_shipping_charge = $base_shipping_charge + $shipping_charge;

?>

<?php
$b_full_name = $b_contact_no = $b_building_no = $b_street_address = $b_landmark = $b_town = $b_district = $b_state = $b_postcode = $b_gst_no = $b_AddressType = "";
$s_full_name = $s_contact_no = $s_building_no = $s_street_address = $s_landmark = $s_town = $s_district = $s_state = $s_postcode = $s_gst_no = $s_AddressType = "";


$is_b_address_exist = 0;
$is_s_address_exist = 0;

if ($user_id) {
    // Billing Address
    $query_b = "SELECT * FROM tbl_billing_address WHERE user_id = '$user_id' LIMIT 1";
    $result_b = mysqli_query($con, $query_b);
    if ($result_b && mysqli_num_rows($result_b) > 0) {
        $data_b = mysqli_fetch_assoc($result_b);
        $is_b_address_exist = 1;
        $b_full_name = $data_b['name'];
        $b_contact_no = $data_b['phone_no'];
        $b_building_no = $data_b['building_no'];
        $b_street_address = $data_b['street_address'];
        $b_landmark = $data_b['landmark'];
        $b_town = $data_b['town'];
        $b_district = $data_b['district'];
        $b_state = $data_b['state'];
        $b_postcode = $data_b['pincode'];
        $b_gst_no = $data_b['gst_no'];

        // ✅ Build full billing address & save in session
        $full_billing_address = "$b_full_name, $b_contact_no, $b_building_no, $b_street_address, $b_landmark, $b_town, $b_district, $b_state, $b_postcode, $b_gst_no";
        $_SESSION['full_billing_address'] = $full_billing_address;
    }

    // Shipping Address
    $query_s = "SELECT * FROM tbl_shipping_address WHERE user_id = '$user_id' LIMIT 1";
    $result_s = mysqli_query($con, $query_s);
    if ($result_s && mysqli_num_rows($result_s) > 0) {
        $data_s = mysqli_fetch_assoc($result_s);
        $is_s_address_exist = 1;
        $s_full_name = $data_s['name'];
        $s_contact_no = $data_s['phone_no'];
        $s_building_no = $data_s['building_no'];
        $s_street_address = $data_s['street_address'];
        $s_landmark = $data_s['landmark'];
        $s_town = $data_s['town'];
        $s_district = $data_s['district'];
        $s_state = $data_s['state'];
        $s_postcode = $data_s['pincode'];
        $s_gst_no = $data_s['gst_no'];

        // Build full shipping address
        $full_shipping_address = "$s_full_name, $s_contact_no, $s_building_no, $s_street_address, $s_landmark, $s_town, $s_district, $s_state, $s_postcode, $s_gst_no";

        // ✅ Store in session for shipping charge calc
        $_SESSION['full_shipping_address'] = $full_shipping_address;
        $_SESSION['s_state'] = $s_state;

        // Also update the variable so it's available right now
        $s_state = $_SESSION['s_state'];
    }

}


if (isset($_POST['submit_address'])) {
    $b_full_name = mysqli_real_escape_string($con, $_POST['b_full_name']);
    $b_contact_no = mysqli_real_escape_string($con, $_POST['b_contact_no']);
    $b_building_no = mysqli_real_escape_string($con, $_POST['b_building_no']);
    $b_street_address = mysqli_real_escape_string($con, $_POST['b_street_address']);
    $b_landmark = mysqli_real_escape_string($con, $_POST['b_landmark']);
    $b_town = mysqli_real_escape_string($con, $_POST['b_town']);
    $b_district = mysqli_real_escape_string($con, $_POST['b_district']);
    $b_state = mysqli_real_escape_string($con, $_POST['b_state']);
    $b_postcode = mysqli_real_escape_string($con, $_POST['b_postcode']);
    $b_gst_no = mysqli_real_escape_string($con, $_POST['b_gst_no']);
    $b_AddressType = ""; 
    
    $full_billing_address = $b_full_name . ', ' . $b_contact_no . ', ' . $b_building_no . ', ' . $b_street_address . ', ' . $b_landmark . ', ' . $b_town . ', ' . $b_district . ', ' . $b_state . ', ' . $b_postcode . ', ' . $b_gst_no . ', ' . $b_AddressType;
    $_SESSION['full_billing_address'] = $full_billing_address;

    if ($is_b_address_exist) {
        $sql_b = "UPDATE tbl_billing_address SET name = '$b_full_name', phone_no = '$b_contact_no',
                    building_no = '$b_building_no', street_address = '$b_street_address', landmark = '$b_landmark', town = '$b_town', district = '$b_district', state = '$b_state', pincode = '$b_postcode', gst_no = '$b_gst_no', AddressType = '$b_AddressType'
                    WHERE user_id = '$user_id'";
    } else {
        $sql_b = "INSERT INTO tbl_billing_address (user_id, name, phone_no, building_no, street_address, landmark, town, district, state, pincode, gst_no , AddressType)
                VALUES ('$user_id', '$b_full_name', '$b_contact_no', '$b_building_no', '$b_street_address', '$b_landmark', '$b_town', '$b_district', '$b_state', '$b_postcode', '$b_gst_no', '$b_AddressType')";
    }
    $result_b = mysqli_query($con, $sql_b);

    if (isset($_POST['ship_to_different_address'])) {
        $s_full_name = mysqli_real_escape_string($con, $_POST['s_full_name']);
        $s_contact_no = mysqli_real_escape_string($con, $_POST['s_contact_no']);
        $s_building_no = mysqli_real_escape_string($con, $_POST['s_building_no']);
        $s_street_address = mysqli_real_escape_string($con, $_POST['s_street_address']);
        $s_landmark = mysqli_real_escape_string($con, $_POST['s_landmark']);
        $s_town = mysqli_real_escape_string($con, $_POST['s_town']);
        $s_district = mysqli_real_escape_string($con, $_POST['s_district']);
        $s_state = mysqli_real_escape_string($con, $_POST['s_state']);
        $s_postcode = mysqli_real_escape_string($con, $_POST['s_postcode']);
        $s_gst_no = mysqli_real_escape_string($con, $_POST['s_gst_no']);
        $s_AddressType = ""; 
        $full_shipping_address = $s_full_name . ', ' . $s_contact_no . ', ' . $s_building_no . ', ' . $s_street_address . ', ' . $s_landmark . ', ' . $s_town . ', ' . $s_district . ', ' . $s_state . ', ' . $s_postcode . ', ' . $s_gst_no . ', ' . $s_AddressType;
        $_SESSION['full_shipping_address'] = $full_shipping_address;
        $_SESSION['s_state'] = $s_state;
    } else {
        $s_full_name = $b_full_name;
        $s_contact_no = $b_contact_no;
        $s_building_no = $b_building_no;
        $s_street_address = $b_street_address;
        $s_landmark = $b_landmark;
        $s_town = $b_town;
        $s_district = $b_district;
        $s_state = $b_state;
        $s_postcode = $b_postcode;
        $s_gst_no = $b_gst_no;
        $s_AddressType = ""; 
        $full_shipping_address = $s_full_name . ', ' . $s_contact_no . ', ' . $s_building_no . ', ' . $s_street_address . ', ' . $s_landmark . ', ' . $s_town . ', ' . $s_district . ', ' . $s_state . ', ' . $s_postcode . ', ' . $s_gst_no . ', ' . $s_AddressType;
        $_SESSION['full_shipping_address'] = $full_shipping_address;
        $_SESSION['s_state'] = $s_state;
    }

    if ($is_s_address_exist) {
        $sql_s = "UPDATE tbl_shipping_address SET name = '$s_full_name', phone_no = '$s_contact_no', building_no = '$s_building_no', street_address = '$s_street_address', landmark = '$s_landmark', town = '$s_town', district = '$s_district', state = '$s_state', pincode = '$s_postcode', gst_no = '$s_gst_no', AddressType = '$s_AddressType' WHERE user_id = '$user_id'";
    } else {
        $sql_s = "INSERT INTO tbl_shipping_address (user_id, name, phone_no, building_no, street_address, landmark, town, district, state, pincode, gst_no, AddressType)
                    VALUES ('$user_id', '$s_full_name', '$s_contact_no', '$s_building_no', '$s_street_address', '$s_landmark', '$s_town', '$s_district', '$s_state', '$s_postcode', '$s_gst_no', '$s_AddressType')";
    }
    $result_s = mysqli_query($con, $sql_s);

    echo "<script>window.location = 'checkout.php';</script>";
}
?>

<?php
// ✅ Get coupon amount from session
// Default coupon values
$coupon_amount = 0;
$coupon_code = "";

// Use existing coupon session data if present
if (isset($_SESSION['coupon']) && is_array($_SESSION['coupon'])) {
    $coupon_code = $_SESSION['coupon']['code'] ?? '';
    $coupon_amount = $_SESSION['coupon']['amount'] ?? 0;
}
?>

<?php
if (isset($_POST['checkout_place_order'])) {
    // Check if billing address exists
    $query_b = "SELECT * FROM tbl_billing_address WHERE user_id = '$user_id'";
    $result_b = mysqli_query($con, $query_b);
    $count_b = mysqli_num_rows($result_b);

    // Check if shipping address exists
    $query_s = "SELECT * FROM tbl_shipping_address WHERE user_id = '$user_id'";
    $result_s = mysqli_query($con, $query_s);
    $count_s = mysqli_num_rows($result_s);

    if ($count_b == 0 || $count_s == 0) {
        echo "<script>alert('Please enter your billing and shipping address before placing the order.'); window.location = 'checkout.php';</script>";
        exit;
    }

    $data_b = mysqli_fetch_assoc($result_b);
    $data_s = mysqli_fetch_assoc($result_s);

    $b_name = mysqli_real_escape_string($con, $data_b['name']);
    $b_phone = mysqli_real_escape_string($con, $data_b['phone_no']);
    $b_building = mysqli_real_escape_string($con, $data_b['building_no']);
    $b_street = mysqli_real_escape_string($con, $data_b['street_address']);
    $b_landmark = mysqli_real_escape_string($con, $data_b['landmark']);
    $b_town = mysqli_real_escape_string($con, $data_b['town']);
    $b_district = mysqli_real_escape_string($con, $data_b['district']);
    $b_state = mysqli_real_escape_string($con, $data_b['state']);
    $b_pincode = mysqli_real_escape_string($con, $data_b['pincode']);
    $b_gst = mysqli_real_escape_string($con, $data_b['gst_no']);

    $s_name = mysqli_real_escape_string($con, $data_s['name']);
    $s_phone = mysqli_real_escape_string($con, $data_s['phone_no']);
    $s_building = mysqli_real_escape_string($con, $data_s['building_no']);
    $s_street = mysqli_real_escape_string($con, $data_s['street_address']);
    $s_landmark = mysqli_real_escape_string($con, $data_s['landmark']);
    $s_town = mysqli_real_escape_string($con, $data_s['town']);
    $s_district = mysqli_real_escape_string($con, $data_s['district']);
    $s_state = mysqli_real_escape_string($con, $data_s['state']);
    $s_pincode = mysqli_real_escape_string($con, $data_s['pincode']);
    $s_gst = mysqli_real_escape_string($con, $data_s['gst_no']);

    // ✅ Build addresses from DB
    $full_billing_address = $b_name . ', ' . $b_phone . ', ' . $b_building . ', ' . $b_street . ', ' . $b_landmark . ', ' . $b_town . ', ' . $b_district . ', ' . $b_state . ', ' . $b_pincode . ', ' . $b_gst;
    $full_shipping_address = $s_name . ', ' . $s_phone . ', ' . $s_building . ', ' . $s_street . ', ' . $s_landmark . ', ' . $s_town . ', ' . $s_district . ', ' . $s_state . ', ' . $s_pincode . ', ' . $s_gst;



    $query_last_order_id = "SELECT MAX(id) as last_order_id FROM tbl_payment";
    $result_last_order_id = mysqli_query($con, $query_last_order_id);
    $row = mysqli_fetch_assoc($result_last_order_id);
    $last_order_id = $row['last_order_id'];
    $last_order_id += 1;
    if ($last_order_id < 10) {
        $last_order_id = "000" . $last_order_id;
    } else if ($last_order_id >= 10 && $last_order_id < 100) {
        $last_order_id = "00" . $last_order_id;
    } else if ($last_order_id >= 100 && $last_order_id < 1000) {
        $last_order_id = "0" . $last_order_id;
    }
    $_SESSION['order_id'] = $order_id = "CB" . $last_order_id;

    // Get coupon code from session or POST early for use in tbl_order
    $entered_coupon_code = $_POST['coupon_code'] ?? ($_SESSION['coupon']['code'] ?? '');

    // 🔥 Identify which partner this commission belongs to (Only from Referral URL now)
    $commission_user_id = 0;
    
    // Check Referral Link
    if (isset($_SESSION['ref_user_id'])) {
        $commission_user_id = $_SESSION['ref_user_id'];
    }



    $insertOrderQuery = "INSERT INTO tbl_order (user_id, order_id, p_id, p_name, p_color, p_size, p_price, p_actual_price, p_gst, gst_Amount, igst, igst_Amount, cgst, cgst_Amount, sgst, sgst_Amount, p_image, p_quantity, no_of_item, weight, unit, sku, applied_coupon, commission_user_id, b_name, b_phone, b_building, b_street, b_landmark, b_town, b_district, b_state, b_pincode, b_gst, s_name, s_phone, s_building, s_street, s_landmark, s_town, s_district, s_state, s_pincode, s_gst, billing_address, shipping_address, order_status, order_date) VALUES ";

    $values = [];
    $query_cart = "SELECT * FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = '0'";
    $result_cart = mysqli_query($con, $query_cart);
    while ($data_cart = mysqli_fetch_assoc($result_cart)) {
        $p_id = $data_cart['p_id'];
        $p_name = $data_cart['p_name'];
        $p_color = $data_cart['p_color'];
        $p_size = $data_cart['p_size'];
        $p_price = $data_cart['p_price'];
        $p_actual_price = $data_cart['p_actual_price'];
        $p_gst = $data_cart['p_gst'];
        $p_image = $data_cart['p_image'];
        $p_quantity = $data_cart['p_quantity'];
        $no_of_item = $data_cart['no_of_item'];
        $weight = $data_cart['weight'];
        $unit = $data_cart['unit'];
        $sku = $data_cart['sku'];

        $query_gst = "SELECT p_gst FROM tbl_product WHERE p_id = '$p_id'";
        $result_gst = mysqli_query($con, $query_gst);
        if ($row_gst = mysqli_fetch_assoc($result_gst)) {
            $p_gst = $row_gst['p_gst'];
        }

        $query_gst_cart = "select p_gst from tbl_cart where p_id = '$p_id'";
        $result_gst_cart = mysqli_query($con, $query_gst_cart);
        if ($row_gst_cart = mysqli_fetch_assoc($result_gst_cart)) {
            $gst_Amount = $row_gst_cart['p_gst'];
        }

        $tmp_s_state = strtolower($s_state);
        if ($tmp_s_state == "haryana") {
            $sgst = $p_gst / 2;
            $sgst_Amount = $gst_Amount / 2;
            $cgst = $p_gst / 2;
            $cgst_Amount = $gst_Amount / 2;
            $igst = 0;
            $igst_Amount = 0;
        } else {
            $sgst = 0;
            $sgst_Amount = 0;
            $cgst = 0;
            $cgst_Amount = 0;
            $igst = $p_gst;
            $igst_Amount = $gst_Amount;
        }
        $payment_method = $_POST['payment_method'] ?? 'online';
        $order_status = ($payment_method == 'online') ? "Payment Pending" : "Success";
        $order_date = date("d/m/Y h:i:sa");

        // --- Commission Calculation per Item ---
        $item_commission_user_id = 0;
        $commission_perc = 0;
        $commission_amt = 0;

        // 1. Check product-specific referral first (STRICT MODE)
        if (isset($_SESSION['product_ref'][$p_id])) {
            $item_commission_user_id = $_SESSION['product_ref'][$p_id];
        }

        if ($item_commission_user_id > 0) {
            // Fetch commission percentage from tbl_user_coupon
            // Priority: Specific Product > General assignment for this user
            $stmt_cp = $pdo->prepare("SELECT percentage FROM tbl_user_coupon WHERE user_id = ? AND (p_id = ? OR p_id IS NULL) ORDER BY p_id DESC LIMIT 1");
            $stmt_cp->execute([$item_commission_user_id, $p_id]);
            $row_cp = $stmt_cp->fetch(PDO::FETCH_ASSOC);
            
            if ($row_cp) {
                $commission_perc = $row_cp['percentage'];
                $commission_amt = ($p_actual_price * $no_of_item) * ($commission_perc / 100);
                
                // Store commission record
                $stmt_ins_comm = $pdo->prepare("INSERT INTO tbl_affiliate_commission (order_id, p_id, user_id, buyer_id, commission_percentage, commission_amount, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_ins_comm->execute([$order_id, $p_id, $item_commission_user_id, $_SESSION['user_id'] ?? 0, $commission_perc, $commission_amt, $order_date, $order_status]);
            } else {
                // If no assignment exists for this partner, no commission
                $item_commission_user_id = 0;
            }
        }

        $values[] = "('$user_id', '$order_id', '$p_id', '$p_name', '$p_color', '$p_size', '$p_price', '$p_actual_price', '$p_gst', '$gst_Amount', '$igst', '$igst_Amount', '$cgst', '$cgst_Amount', '$sgst', '$sgst_Amount', '$p_image', '$p_quantity', '$no_of_item', '$weight', '$unit', '$sku', '$entered_coupon_code', '$item_commission_user_id', '$b_name', '$b_phone', '$b_building', '$b_street', '$b_landmark', '$b_town', '$b_district', '$b_state', '$b_pincode', '$b_gst', '$s_name', '$s_phone', '$s_building', '$s_street', '$s_landmark', '$s_town', '$s_district', '$s_state', '$s_pincode', '$s_gst', '$full_billing_address', '$full_shipping_address', '$order_status', '$order_date')";
    }

    $insertOrderQuery .= implode(", ", $values);

    $result_insert = mysqli_query($con, $insertOrderQuery);
    if (!$result_insert) {
        die("Error placing order: " . mysqli_error($con));
    }


    // $coupon_code = $_POST['coupon_code'];
    $user_id = $_SESSION['user_id'];
    // Use null coalescing operator to avoid undefined index errors
    $coupon_amount = $_POST['coupon_amount'] ?? 0;
    $shipping_charge = $_POST['shipping_charge'] ?? 0;
    $grand_total = $_POST['grand_total'] ?? 0;
    $sub_total = $_POST['sub_total'] ?? 0;
    $gst = $_POST['gst'] ?? 0;
    $amount = (float)$sub_total + (float)$gst;
    $payment_id = 0;
    $transaction_id = 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_date = date("Y-m-d H:i:s");

    // Set coupon code from session or POST
    // $entered_coupon_code = $_POST['coupon_code'] ?? ($_SESSION['coupon']['code'] ?? ''); // Moved up

    // Check if payment method is selected
    $payment_method = $_POST['payment_method'] ?? 'online';
    $payment_status = ($payment_method == 'online') ? "Pending" : "Success";



    $insertPaymentQuery = "INSERT INTO tbl_payment (user_id, order_id, payment_order_id, amount, coupon_code, discount, shipping_charge, cod_charge, payable_amount, payment_id, transaction_id, payment_method, payment_status, payment_date)
		   VALUES ('$user_id', '$order_id', '', '$amount', '$entered_coupon_code', '$coupon_amount',
                      '$shipping_charge', 0, '$grand_total', '$payment_id', '$transaction_id', '$payment_method', '$payment_status', '$payment_date')";
    $result_insert_payment = mysqli_query($con, $insertPaymentQuery);
    $last_pay_id = mysqli_insert_id($con);

    if ($result_insert_payment) {
        // Mark cart items as ordered
        $update_cart_query = "UPDATE tbl_cart SET is_ordered = '1' WHERE user_id = '$user_id' AND is_ordered = '0'";
        mysqli_query($con, $update_cart_query);

        // Clear referral sessions
        unset($_SESSION['coupon']);
        unset($_SESSION['product_ref']);
        unset($_SESSION['ref_user_id']);

        $_SESSION['order_success'] = true;

        if ($payment_method == "online") {
            // Encode the payment ID for PayU script
            $encoded_id = base64_encode($last_pay_id);
            echo "<script>window.location = 'payment/pay2.php?last_pay_id=$encoded_id';</script>";
        } else {
            echo "<script>window.location = 'thankyou.php';</script>";
        }
        exit();
    } else {
        echo "Failed to insert coupon code into payment records.";
    }
}
?>

<?php include("include/header.php"); ?>

<link rel="stylesheet" href="<?= $base_url; ?>assets/css/cart.css" type="text/css" />
<div class="checkout-container">
    <div class="container">
        <!-- Progress Steps -->
        <div class="checkout-progress">
            <div class="progress-step completed">
                <div class="progress-step-number">1</div>
                <span>Cart</span>
            </div>
            <div class="progress-step active">
                <div class="progress-step-number">2</div>
                <span>Checkout</span>
            </div>
            <div class="progress-step">
                <div class="progress-step-number">3</div>
                <span>Payment</span>
            </div>
        </div>

        <!-- Header -->
        <div class="checkout-header">
            <h1>Complete Your Order</h1>
            <p>Please fill in your details to complete your purchase</p>
        </div>

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8 order-2 order-md-1 mt-5 mt-lg-0"">
                <form method="post" class="checkout" action="" autocomplete="off" id="myForm" novalidate>
                <div class="row">
                    <!-- Billing Details -->

                    <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                        <div id="bAddress">
                            <div class="customer-details">
                                <h3>Billing Details</h3>
                                <div class="billing-fields-wrapper">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_full_name" name="b_full_name"
                                            value="<?= $b_full_name; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your full name.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_contact_no" name="b_contact_no"
                                            value="<?= $b_contact_no; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your Number.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Address 1 (Flat/ House No, Building, Colony) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_building_no" name="b_building_no"
                                            placeholder="House/Bulding No" value="<?= $b_building_no; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your Flat/ House No, Building, Colony.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Address 2 (Area, Sector, Street, Village) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_street_address"
                                            name="b_street_address" placeholder="Street Address"
                                            value="<?= $b_street_address; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your Area, Sector, Street, Village.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Landmark</label>
                                        <input type="text" class="form-control" name="b_landmark" placeholder="Landmark"
                                            value="<?= $b_landmark; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Town / City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_town" name="b_town"
                                            value="<?= $b_town; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your Town / City.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>District <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_district" name="b_district"
                                            placeholder="District" value="<?= $b_district; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your District.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>State <span class="text-danger">*</span></label>

                                        <?php
                                        if ($b_state == "") {
                                            $b_state_text = "Select State";
                                            $b_state_val = "";
                                        } else {
                                            $b_state_text = $b_state;
                                            $b_state_val = $b_state;
                                        }
                                        ?>
                                        <select name="b_state" id="b_state" class="form-control"
                                            style="padding:5px 15px;" required>
                                            <option value="<?= $b_state_val; ?>"><?= $b_state_text; ?></option>
                                            <?php
                                            $query_b_states = "select * from tbl_shipping_zone ORDER BY state_name ASC";
                                            $result_b_states = mysqli_query($con, $query_b_states);
                                            while ($info_b_states = mysqli_fetch_assoc($result_b_states)) {
                                                ?>
                                                <option value="<?= $info_b_states['state_name']; ?>">
                                                    <?= $info_b_states['state_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please enter your State.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Postcode / Pincode <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="b_postcode" name="b_postcode"
                                            value="<?= $b_postcode; ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your Pincode.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>GST No</label>
                                        <input type="text" class="form-control" name="b_gst_no"
                                            value="<?= $b_gst_no; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Details -->

                    <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                        <div id="sAddress">
                            <div class="shipping-fields">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="ship_to_different_address"
                                        id="shipDiff" value="1">
                                    <label class="form-check-label" for="shipDiff">
                                        Ship to a different address?
                                    </label>
                                </div>
                                <div id="shippingAddress" style="display: none;">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_full_name"
                                            value="<?= $s_full_name; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_contact_no"
                                            value="<?= $s_contact_no; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Address 1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_building_no"
                                            placeholder="House/Building No" value="<?= $s_building_no; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Address 2 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_street_address"
                                            placeholder="Street Address" value="<?= $s_street_address; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Landmark</label>
                                        <input type="text" class="form-control" name="s_landmark" placeholder="Landmark"
                                            value="<?= $s_landmark; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Town / City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_town" value="<?= $s_town; ?>"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>District <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_district"
                                            value="<?= $s_district; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>State <span class="text-danger">*</span></label>
                                        <select name="s_state" class="form-control" style="padding:5px 15px;" required>
                                            <option value="<?= $s_state; ?>"><?= $s_state ?: 'Select State'; ?></option>
                                            <?php
                                            $query_s_states = "SELECT * FROM tbl_shipping_zone ORDER BY state_name ASC";
                                            $result_s_states = mysqli_query($con, $query_s_states);
                                            while ($state = mysqli_fetch_assoc($result_s_states)) {
                                                ?>
                                                <option value="<?= $state['state_name']; ?>"><?= $state['state_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Postcode / ZIP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="s_postcode"
                                            value="<?= $s_postcode; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>GST No</label>
                                        <input type="text" class="form-control" name="s_gst_no"
                                            value="<?= $s_gst_no; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Address Button -->
                    <div class="col-12">
                        <div class="form-group mt-4">
                            <button type="submit" name="submit_address" class="btn btn-cart mt-3">Confirm
                                Address</button>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Optional: Toggle shipping address -->
                <script>
                    document.getElementById('shipDiff').addEventListener('change', function () {
                        const shippingBox = document.getElementById('shippingAddress');
                        shippingBox.style.display = this.checked ? 'block' : 'none';
                    });
                </script>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4 order-1 order-md-2">
                <div class="order-summary">
                    <form method="post" class="checkout" action="" autocomplete="off">
                        <h3><i class="fas fa-shopping-cart me-2"></i>Order Summary</h3>
                        <?php
                        $count = 0;
                        $total = 0;
                        $gst = 0;
                        $sub_total = 0;
                        $shipping_charge = 0;
                        $shipping_charge = 0;
                        $p_total_weight = 0;
                        $query_cart = "select * from tbl_cart where user_id = '$user_id' and is_ordered = '0'";
                        $result_cart = mysqli_query($con, $query_cart);
                        while ($data_cart = mysqli_fetch_assoc($result_cart)) { ?>
                            <div class="cart-item">
                                <img src="<?= $base_url; ?>assets/img/product-detail/<?= $data_cart['p_image']; ?>"
                                    alt="<?= $data_cart['p_name']; ?>" class="cart-item-img">
                                <div class="cart-item-details">
                                    <div class="cart-item-title"><?= $data_cart['p_name']; ?></div>
                                    <div class="cart-item-price">₹<?= $data_cart['p_price']; ?></div>
                                    <div class="cart-item-quantity">QTY : <?= $data_cart['no_of_item']; ?></div>
                                </div>
                            </div>
                            <?php
                            $total += $data_cart['p_price'] * $data_cart['no_of_item'];
                            $gst += $data_cart['p_gst'] * $data_cart['no_of_item'];
                            $sub_total += $data_cart['p_actual_price'] * $data_cart['no_of_item'];

                            // $p_weight = $data_cart['weight'] * $data_cart['no_of_item'];
                            $p_unit = $data_cart['unit'];
                            // echo "<script>alert('$p_unit')</script>";
                            // if (trim($p_unit) == "gm") {
                            //     $p_total_weight += $p_weight;
                            // } else {
                            //     $p_total_weight += $p_weight;
                            //     // echo "<script>alert('p_unit')</script>";
                            // }
                            $count++;
                        }

                        // if ($p_total_weight < 500) {
                        //     $shipping_charge = 0;
                        // } else if ($p_total_weight >= 500 && $p_total_weight < 1000) {
                        //     $shipping_charge = 30;
                        // } else if ($p_total_weight >= 1000 && $p_total_weight < 2000) {
                        //     $shipping_charge = 60;
                        // } else if ($p_total_weight >= 2000 && $p_total_weight < 3000) {
                        //     $shipping_charge = 90;
                        // } else if ($p_total_weight >= 3000 && $p_total_weight < 5000) {
                        //     $shipping_charge = 120;
                        // } else if ($p_total_weight >= 5000) {
                        //     $shipping_charge = 120;
                        // }
                        // $total_shipping_charge = $base_shipping_charge + $shipping_charge;
                        // if ($total > $free_shipping)
                        //     $total_shipping_charge = 0;
                        
                        $grand_total = $sub_total - $coupon_amount;
                        $grand_total = $grand_total + $total_shipping_charge;

                        $sub_total = number_format((float) $sub_total, 2, '.', '');
                        $gst = number_format((float) $gst, 2, '.', '');
                        $coupon_amount = number_format((float) $coupon_amount, 2, '.', '');
                        $total_shipping_charge = number_format((float) $total_shipping_charge, 2, '.', '');
                        $total = number_format((float) $total, 2, '.', '');
                        $grand_total = number_format((float) $grand_total, 2, '.', '');
                        ?>

                        <div class="order-summary">
                            <!-- Totals -->
                            <div class="total-section">
                                <div class="total-row">
                                    <span>Subtotal (Incl. GST):</span>
                                    <input type="hidden" name="sub_total" value="<?= $sub_total ?>">
                                    <span>₹<?= $sub_total; ?></span>
                                </div>
                                <!--<div class="total-row">-->
                                <!--    <span>Included GST:</span>-->
                                <!--    <input type="hidden" name="gst" value="<?= $gst ?>">-->
                                <!--    <span>₹<?= $gst ?></span>-->
                                <!--</div>-->

                                <div class="total-row">
                                    <span>Coupon Discount:</span>
                                    <input type="hidden" name="coupon_amount" value="<?= $coupon_amount ?>">
                                    <span>- ₹<?= $coupon_amount ?></span>
                                </div>
                                <div class="total-row">
                                    <span>Shipping:</span>
                                    <input type="hidden" name="shipping_charge" value="<?= $total_shipping_charge ?>">
                                    <span>₹<?= $total_shipping_charge; ?></span>
                                </div>
                            </div>
                            <div class="total-row final">
                                <span>Grand Total:</span>
                                <input type="hidden" name="grand_total" value="<?= $grand_total ?>">
                                <span>₹<?= $grand_total; ?></span>
                            </div>

                            <div id="payment" class="checkout-payment">
                                <ul class="payment-methods methods custom-radio"
                                    style="list-style: none; padding-left: 0;">
                                    <li class="payment-method mb-2">
                                        <input type="radio" class="input-radio" name="payment_method" value="online"
                                            id="payment_online" required checked>
                                        <label for="payment_online" class="ms-2">Online Payment (PayU)</label>
                                    </li>
                                    <li class="payment-method">
                                        <input type="radio" class="input-radio" name="payment_method" value="cod"
                                            id="payment_cod" required>
                                        <label for="payment_cod" class="ms-2">Cash On Delivery</label>
                                    </li>
                                </ul>
                                <button type="submit" name="checkout_place_order" id="placeOrderBtn"
                                    class="btn btn-cart mt-3 w-100">Proceed to Payment</button>
                            </div>

                            <!-- Shipping Info -->
                            <div class="mt-4">
                                <h6><i class="fas fa-truck me-2"></i>Shipping Information</h6>
                                <p class="text-muted mb-2">Estimated delivery: 3–5 business days</p>
                                <!--<?php if (!empty($free_shipping)): ?>-->
                                    <!--<p class="text-muted mb-0">Free shipping on orders above ₹<?= $free_shipping ?></p>-->
                                    <!--<?php else: ?>-->
                                    <!--    <p class="text-muted mb-0">Select your address to see free shipping eligibility</p>-->
                                    <!--<?php endif; ?>-->
                            </div>

                            <!-- Return Policy -->
                            <div class="mt-4">
                                <h6><i class="fas fa-undo me-2"></i>Return Policy</h6>
                                <p class="text-muted mb-0">30-day money-back guarantee</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('myForm');

        const shipDiff = document.getElementById('shipDiff'); // optional toggle


        function isAddressFilled(sectionId) {
            const fields = document.querySelectorAll(`#${sectionId} input, #${sectionId} textarea`);
            for (let field of fields) {
                if (field.value.trim() !== '') {
                    return true;
                }
            }
            return false;
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('myForm');

        // Real-time validation for all required fields
        form.querySelectorAll('input[required], select[required]').forEach(function (input) {
            input.addEventListener('input', function () {
                if (input.value.trim()) {
                    input.classList.remove('is-invalid');
                }
            });

            input.addEventListener('change', function () {
                if (input.value.trim()) {
                    input.classList.remove('is-invalid');
                }
            });
        });

        // Form submission validation
        form.addEventListener('submit', function (e) {
            let valid = true;

            // Loop through all required fields
            form.querySelectorAll('input[required], select[required]').forEach(function (input) {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    if (valid) input.focus(); // focus first invalid field
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault(); // prevent submission if any field is invalid
            }
            // else form will submit normally
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const form = document.getElementById('myForm');

        if (!placeOrderBtn || !form) return;

        placeOrderBtn.addEventListener('click', function (event) {
            const allRequired = Array.from(form.querySelectorAll('input[required], select[required]'));
            const firstEmpty = allRequired.find(input => input.value.trim() === '');

            if (firstEmpty) {
                event.preventDefault(); // Stop the order from being placed
                alert('Please fill in all required fields.');
                firstEmpty.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstEmpty.focus();
            }
        });
    });
</script>

<script>
    function isAddressFilled(prefix) {
        const fields = [
            `${prefix}_full_name`,
            `${prefix}_contact_no`,
            `${prefix}_building_no`,
            `${prefix}_town`,
            `${prefix}_district`,
            `${prefix}_state`,
            `${prefix}_postcode`
        ];
        return fields.every(id => {
            const el = document.querySelector(`[name='${id}']`);
            return el && el.value.trim() !== '';
        });
    }


    function copyBillingToShipping() {
        // Copy values only if "ship to different address" is NOT checked
        const shipDiff = document.getElementById('shipDiff');
        if (shipDiff && shipDiff.checked) return;

        const billingFields = [
            "full_name", "contact_no", "building_no",
            "street_address", "landmark", "town",
            "district", "state", "postcode", "gst_no"
        ];
        billingFields.forEach(field => {
            const bEl = document.querySelector(`[name='b_${field}']`);
            const sEl = document.querySelector(`[name='s_${field}']`);
            if (bEl && sEl) {
                sEl.value = bEl.value;
            }
        });
        validateOrderButton();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Sync billing to shipping when typing
        document.querySelectorAll("[name^='b_']").forEach(el => {
            el.addEventListener('input', copyBillingToShipping);
            el.addEventListener('change', copyBillingToShipping);
        });

        // Toggle shipping address visibility
        const shipDiff = document.getElementById('shipDiff');
        if (shipDiff) {
            shipDiff.addEventListener('change', function () {
                const shippingDiv = document.getElementById('shippingAddress');
                if (this.checked) {
                    shippingDiv.style.display = 'block';
                } else {
                    shippingDiv.style.display = 'none';
                    copyBillingToShipping(); // reset to billing when unchecked
                }
                validateOrderButton();
            });
        }

        // Run initial sync + validation
        copyBillingToShipping();
        validateOrderButton();
    });
</script>

<?php
include('include/footer.php');
?>