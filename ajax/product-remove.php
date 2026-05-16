<?php 
include("../admin/inc/config.php");
session_start(); // REQUIRED for accessing $_SESSION

$action = $_POST['action'];

if ($action == "cart") {
    $cart_id = $_POST['cart_id'];

    // First, get user ID (either logged in or temp)
    $user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? 0;

    // Delete item
    $query_del = "DELETE FROM tbl_cart WHERE id = '$cart_id' AND user_id = '$user_id'";
    mysqli_query($con, $query_del);

    // Check if cart is now empty
    $check = mysqli_query($con, "SELECT COUNT(*) as count FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = 0");
    $row = mysqli_fetch_assoc($check);
    if ((int)$row['count'] === 0) {
        unset($_SESSION['coupon']); // Unset coupon if cart is empty
    }

    echo json_encode([
        'success' => true,
        'coupon_removed' => (int)$row['count'] === 0,
        'message' => (int)$row['count'] === 0 ? "Coupon removed because cart is empty." : "Item removed."
    ]);
    exit;
}

if ($action == "wish") {
    $wish_id = $_POST['wish_id'];
    $query_del = "DELETE FROM tbl_wishlist WHERE id = '$wish_id'";
    mysqli_query($con, $query_del);

    echo json_encode([
        'success' => true,
        'message' => "Wishlist item removed."
    ]);
    exit;
}
?>
