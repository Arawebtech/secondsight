<?php
include("../admin/inc/config.php");
session_start();

$user_id = "";
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif(isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
} else {
    $_SESSION['temp_user_id'] = rand(10000, 100000);
    $user_id = $_SESSION['temp_user_id'];
}

$wishlist_id = $_POST['wishlist_id'];

// Get wishlist item details
$wishlist_query = "SELECT * FROM tbl_wishlist WHERE id = '$wishlist_id' AND user_id = '$user_id'";
$wishlist_result = mysqli_query($con, $wishlist_query);
$wishlist_item = mysqli_fetch_assoc($wishlist_result);

if (!$wishlist_item) {
    echo json_encode(['error' => 'Wishlist item not found']);
    exit;
}

// Check stock availability
$sku = $wishlist_item['sku'];
$stock_query = "SELECT in_stoke FROM tbl_product_price WHERE p_sku = '$sku'";
$stock_result = mysqli_query($con, $stock_query);
$stock_data = mysqli_fetch_assoc($stock_result);
$available_stock = (int)$stock_data['in_stoke'];

if ($available_stock <= 0) {
    echo json_encode(['error' => 'Out of stock']);
    exit;
}

$requested_qty = (int)$wishlist_item['no_of_item'];

// Check if item already exists in cart
$existing_query = "SELECT no_of_item FROM tbl_cart WHERE user_id = '$user_id' AND sku = '$sku' AND is_ordered = '0'";
$existing_result = mysqli_query($con, $existing_query);
$existing_item = mysqli_fetch_assoc($existing_result);

$current_cart_qty = $existing_item ? (int)$existing_item['no_of_item'] : 0;

if ($current_cart_qty + $requested_qty > $available_stock) {
    echo json_encode(['error' => "Only $available_stock in stock"]);
    exit;
}

if ($existing_item) {
    // Update existing cart item
    $update_query = "UPDATE tbl_cart SET no_of_item = no_of_item + $requested_qty WHERE user_id = '$user_id' AND sku = '$sku' AND is_ordered = '0'";
    $result_update = mysqli_query($con, $update_query);
    
    if ($result_update) {
        // Remove from wishlist
        $delete_query = "DELETE FROM tbl_wishlist WHERE id = '$wishlist_id'";
        mysqli_query($con, $delete_query);
        echo json_encode(['success' => 'Product quantity updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update cart']);
    }
} else {
    // Insert new cart item
    $query = "INSERT INTO tbl_cart (user_id, p_id, p_name, p_color, p_size, p_price, p_actual_price, p_gst, p_image, p_quantity, no_of_item, weight, unit, sku) SELECT user_id, p_id, p_name, p_color, p_size, p_price, p_actual_price, p_gst, p_image, p_quantity, no_of_item, weight, unit, sku FROM tbl_wishlist WHERE id = '$wishlist_id'";
    $result = mysqli_query($con, $query);

    if($result) {
        // Remove from wishlist
        $delete_query = "DELETE FROM tbl_wishlist WHERE id = '$wishlist_id'";
        mysqli_query($con, $delete_query);
        echo json_encode(['success' => 'Product Added Successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add product to cart']);
    }
}
?>