<?php
session_start();
include("../admin/inc/config.php");

// Set guest temp_user_id if not logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['temp_user_id'])) {
    $_SESSION['temp_user_id'] = rand(10000, 100000);
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['temp_user_id'];

// Fetch only unordered items from cart + stock info
$query = "SELECT 
  tbl_cart.id AS cart_id, 
  tbl_cart.p_id AS id, 
  tbl_cart.p_name AS name, 
  tbl_cart.p_price AS price, 
  tbl_cart.p_gst AS gst,
  tbl_cart.p_actual_price AS actual_price,
  tbl_cart.no_of_item AS qty, 
  tbl_cart.p_image AS image,
  tbl_product_price.in_stoke AS stock   -- ✅ Added stock
FROM tbl_cart
LEFT JOIN tbl_product_price 
  ON tbl_cart.sku = tbl_product_price.p_sku
WHERE tbl_cart.user_id = '$user_id' AND tbl_cart.is_ordered = '0'";

$result = mysqli_query($con, $query);

$cartItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['price'] = floatval($row['price']);
    $row['qty']   = intval($row['qty']);
    $row['stock'] = intval($row['stock']);
    $cartItems[]  = $row;
}

header('Content-Type: application/json');
echo json_encode($cartItems);
?>
