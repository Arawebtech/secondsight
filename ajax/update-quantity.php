<?php
include("../admin/inc/config.php");
session_start();

$cart_id = $_POST['cart_id'];
$action = $_POST['action'];

if (!$cart_id || !$action) {
    exit('Invalid request');
}

// Get current cart item details
$cart_query = "SELECT c.*, p.in_stoke as stock 
               FROM tbl_cart c 
               LEFT JOIN tbl_product_price p ON c.sku = p.p_sku 
               WHERE c.id = '$cart_id'";
$cart_result = mysqli_query($con, $cart_query);
$cart_item = mysqli_fetch_assoc($cart_result);

if (!$cart_item) {
    echo json_encode(['error' => 'Cart item not found']);
    exit;
}

$current_qty = (int)$cart_item['no_of_item'];
$available_stock = (int)$cart_item['stock'];

if ($action == "add") {
    // Check if adding would exceed stock limit
    if ($current_qty >= $available_stock) {
        echo json_encode(['error' => "Only $available_stock in stock"]);
        exit;
    }
    
    $query = "UPDATE tbl_cart SET no_of_item = no_of_item + 1 WHERE id = '$cart_id'";
    mysqli_query($con, $query);
} elseif ($action == "sub") {
    if ($current_qty <= 1) {
        // Delete item if quantity goes to 0 or 1
        mysqli_query($con, "DELETE FROM tbl_cart WHERE id = '$cart_id'");
        echo json_encode(['deleted' => true]);
        exit;
    } else {
        // Otherwise subtract normally
        $query = "UPDATE tbl_cart SET no_of_item = no_of_item - 1 WHERE id = '$cart_id'";
        mysqli_query($con, $query);
    }
}

echo json_encode(['success' => true]);
