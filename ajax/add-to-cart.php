<?php
session_start();
include("../admin/inc/config.php");

$user_id = "";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    if (isset($_SESSION['temp_user_id'])) {
        $user_id = $_SESSION['temp_user_id'];
    } else {
        $_SESSION['temp_user_id'] = rand(10000, 100000);
        $user_id = $_SESSION['temp_user_id'];
    }

}

$p_id = $_POST['p_id'];
$p_name = trim($_POST['p_name']);
$p_color = trim($_POST['p_color']);
$p_size = trim($_POST['p_size']);
$p_price = $_POST['p_price'];
$p_image = $_POST['p_image'];
$p_qty = $_POST['p_qty'];
$p_total_item = $_POST['p_total_item'];
$p_weight = $_POST['p_weight'];
$p_unit = trim($_POST['p_unit']);
$p_full_sku = trim($_POST['p_full_sku']);

// Check stock availability
// Prefer SKU if available, otherwise use p_id (if product has only one price entry)
if (!empty($p_full_sku)) {
    $stock_query = "SELECT in_stoke FROM tbl_product_price WHERE p_sku = '$p_full_sku'";
} else {
    $stock_query = "SELECT in_stoke FROM tbl_product_price WHERE p_id = '$p_id' LIMIT 1";
}

$stock_result = mysqli_query($con, $stock_query);
if (!$stock_result) {
    echo json_encode(['error' => 'Database error checking stock: ' . mysqli_error($con)]);
    exit;
}

$stock_data = mysqli_fetch_assoc($stock_result);
if (!$stock_data) {
    echo json_encode(['error' => 'Product details not found for ID: ' . $p_id . (empty($p_full_sku) ? '' : ' and SKU: ' . $p_full_sku)]);
    exit;
}

$available_stock = (int) $stock_data['in_stoke'];

if ($available_stock <= 0) {
    echo json_encode(['error' => 'Out of stock']);
    exit;
}

// Check if item already exists in cart
if (!empty($p_full_sku)) {
    $existing_query = "SELECT no_of_item FROM tbl_cart WHERE user_id = '$user_id' AND sku = '$p_full_sku' AND is_ordered = '0'";
} else {
    $existing_query = "SELECT no_of_item FROM tbl_cart WHERE user_id = '$user_id' AND p_id = '$p_id' AND is_ordered = '0'";
}
$existing_result = mysqli_query($con, $existing_query);
$existing_item = mysqli_fetch_assoc($existing_result);

$current_cart_qty = $existing_item ? (int) $existing_item['no_of_item'] : 0;
$requested_qty = (int) $p_total_item;

if ($current_cart_qty + $requested_qty > $available_stock) {
    echo json_encode(['error' => "Only $available_stock in stock"]);
    exit;
}

$p_gst_per = 0;
$query_gst = "SELECT p_gst FROM tbl_product WHERE p_id = '$p_id'";
$result_gst = mysqli_query($con, $query_gst);
$info_gst = mysqli_fetch_assoc($result_gst);
$p_gst_per = $info_gst['p_gst'];

$p_gst = round(($p_price * $p_gst_per / 100), 2);
$p_actual_price = $p_price + $p_gst;

if ($existing_item) {
    // Update existing cart item
    if (!empty($p_full_sku)) {
        $update_query = "UPDATE tbl_cart SET no_of_item = no_of_item + $requested_qty WHERE user_id = '$user_id' AND sku = '$p_full_sku' AND is_ordered = '0'";
    } else {
        $update_query = "UPDATE tbl_cart SET no_of_item = no_of_item + $requested_qty WHERE user_id = '$user_id' AND p_id = '$p_id' AND is_ordered = '0'";
    }
    $result_update = mysqli_query($con, $update_query);

    if ($result_update) {
        echo json_encode(['success' => 'Product quantity updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update cart']);
    }
} else {
    // Insert new cart item
    $query_ins = "insert into tbl_cart(user_id, p_id, p_name, p_color, p_size, p_price, p_actual_price, p_gst, p_image, p_quantity, no_of_item, weight, unit, sku, order_id) values('$user_id', '$p_id', '$p_name', '$p_color', '$p_size', '$p_price', '$p_actual_price', '$p_gst', '$p_image', '$p_qty', '$p_total_item', " . (int)$p_weight . ", '$p_unit', '$p_full_sku', 0)";
    $result_ins = mysqli_query($con, $query_ins);

    if ($result_ins) {
        echo json_encode(['success' => 'Product Added Successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add product to cart']);
    }
}

error_reporting();
?>