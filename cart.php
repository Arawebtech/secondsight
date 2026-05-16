<?php
include("admin/inc/config.php");
session_start();

$user_id = "";
// If session exists, use it
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    // Restore session from cookie if available
    $cookie_user_id = intval($_COOKIE['user_id']);
    $query = "SELECT * FROM tbl_user WHERE id = '$cookie_user_id' AND status='Active' LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email_id'] = $row['email'];
        $_SESSION['user_name'] = $row['full_name'];
        $_SESSION['phone'] = $row['phone'];

        $user_id = $row['id'];
    }
} elseif (isset($_SESSION['temp_user_id'])) {
    // Fallback to temp session if nothing else
    $user_id = $_SESSION['temp_user_id'];
}

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
include('include/header.php');
?>
<link rel="stylesheet" href="<?= $base_url; ?>assets/css/cart.css" type="text/css">


<?php
if (isset($_SESSION['flash_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show m-3" role="alert">'
        . $_SESSION['flash_message'] .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['flash_message']);
}
?>


<div class="cart-container" id="cart-page-container">
    <div class="container">
        <!-- Header -->
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart me-3"></i>Your Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
            <div class="cart-table">
                <div id="cart-items-container">
                    <?php
                    $query_cart = "SELECT * FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = '0'";
                    $result_cart = mysqli_query($con, $query_cart);
        
                    $count = 0;
                    $total = 0;
                    $gst = 0;
                    $sub_total = 0;
                    $shipping_charge = 0;
                    $p_total_weight = 0;
                    $btn_disable = "";
                    $has_items = false;
        
                    while ($data_cart = mysqli_fetch_assoc($result_cart)) {
                        $has_items = true;
                        $p_id = $data_cart['p_id'];
                        $items_alert_msg = "";
                        $sku = $data_cart['sku'];
                        $query_stock = "SELECT in_stoke FROM tbl_product_price WHERE p_sku = '$sku'";
                        $result_stock = mysqli_query($con, $query_stock);
                        $stock_data = mysqli_fetch_assoc($result_stock);
                        $available_qty = $stock_data['in_stoke'];
        
                        $cart_qty = (int)$data_cart['no_of_item'];
        
                        // If cart quantity > stock, adjust it
                        if ($cart_qty > $available_qty) {
                            if ($available_qty > 0) {
                                $items_alert_msg = "Only $available_qty items left";
                                $cart_qty = $available_qty;
                                // Sync with DB so sidebar matches
                                mysqli_query($con, "UPDATE tbl_cart SET no_of_item = '$available_qty' WHERE id = '{$data_cart['id']}'");
                            } else {
                                $items_alert_msg = "Out of stock";
                                $btn_disable = "yes";
                                // Optionally remove from cart if out of stock
                                mysqli_query($con, "DELETE FROM tbl_cart WHERE id = '{$data_cart['id']}'");
                                continue; // skip rendering this item
                            }
                        }
                    
                        $p_base_total = $data_cart['p_price'] * $cart_qty;
                        $p_gst_total = $data_cart['p_gst'] * $cart_qty;
                        $p_actual_total = $p_base_total + $p_gst_total;

                        $sub_total += $p_base_total; // BASE total
                        $gst += $p_gst_total;        
                        $total += $p_actual_total;   
                        ?>
                        <div class="cart-item" data-id="<?= $p_id; ?>" data-stock="<?= $available_qty; ?>" data-price="<?= $data_cart['p_price']; ?>" 
                            data-gst="<?= $data_cart['p_gst']; ?>" >
                          <img src="<?= $base_url; ?>assets/img/product-detail/<?= $data_cart['p_image']; ?>" class="cart-item-img"
                                style="width: 70px; height: 70px;">
                          <div class="cart-item-details">
                            <a href="<?= $base_url; ?>product-circle.php?p_id=<?= $data_cart['p_id']; ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($data_cart['p_name']); ?>
                                    </a>
                        
                            <div class="d-flex align-items-center gap-2 my-1 justify-content-center justify-content-md-start">
                              <button class="btn btn-sm btn-outline-secondary qty-sub-cart" data-cartid="<?= $data_cart['id']; ?>">–</button>
                              <span id="qty-<?= $p_id; ?>"><?= $data_cart['no_of_item']; ?></span>
                              <button class="btn btn-sm btn-outline-secondary qty-add-cart" data-cartid="<?= $data_cart['id']; ?>">+</button>
                            </div>
                        
                            <div class="fw-bold text-dark" id="line-total-<?= $p_id; ?>">
                              ₹<?= $data_cart['p_price'] * $data_cart['no_of_item']; ?>
                            </div>
                            <div class="text-danger" id="stock-msg-<?= $p_id; ?>"><?= $items_alert_msg; ?></div>
                        
                            <a href="#" class="text-danger small remove-link-page" data-cartid="<?= $data_cart['id']; ?>">Remove</a>
                          </div>
                        </div>
        
                    <?php } ?>
        
                    <?php if (!$has_items): ?>
                        <div class="empty-cart text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                            <h3 class="mt-3">Your cart is empty</h3>
                            <p>Looks like you haven't added anything yet.</p>
                            <a href="<?= $base_url; ?>index.php" class="btn btn-cart secondary mt-3">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

<!-- Order Summary -->
<div class="col-lg-4">
    <div class="cart-summary">
        <h3><i class="fas fa-calculator me-2"></i>Order Summary</h3>

        <!-- Coupon Section -->
        <div class="coupon-section mt-4">
            <h6><i class="fas fa-tag me-2"></i>Have a coupon?</h6>
            <div class="coupon-input d-flex gap-2">
                <input type="text" id="coupon-input" name="coupon_code" placeholder="Enter coupon code"
                    class="form-control" value="<?= htmlspecialchars($coupon_code); ?>">
                <button type="button" class="btn btn-primary" onclick="applyCoupon()">Apply</button>
            </div>
            <small id="coupon-message" class="text-success mt-2 d-block">
                <?php if (isset($_SESSION['coupon']) && is_array($_SESSION['coupon'])): ?>
                    Coupon applied: <?= htmlspecialchars($_SESSION['coupon']['code']) ?>
                <?php endif; ?>
            </small>
            <?php if (isset($_SESSION['coupon'])): ?>
                <button class="btn btn-link text-danger p-0 mt-2" onclick="removeCoupon()">Remove Coupon</button>
            <?php endif; ?>
        </div>

        <!-- Totals -->
        <div class="summary-row mt-4">
            <span>Subtotal:</span>
            <span id="subtotal">₹<?= number_format($sub_total, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>GST:</span>
            <span id="gst">₹<?= number_format($gst, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Discount<?= $coupon_code ? " ($coupon_code)" : ''; ?>:</span>
            <span id="discount" class="text-success">-₹<?= number_format($coupon_amount, 2); ?></span>
        </div>
        <div class="summary-row final">
            <span>Total:</span>
            <span id="grand-total">₹<?= number_format($total - $coupon_amount, 2); ?></span>
        </div>
        
        <!-- Hidden fields to pass values to JS -->
        <input type="hidden" id="coupon-amount" value="<?= $coupon_amount; ?>">


        <?php if ($btn_disable === "" && $user_id && $has_items): ?>
            <a href="<?= $base_url; ?>checkout.php" class="btn btn-cart mt-3">Proceed to checkout</a>
        <?php else: ?>
            <a href="<?= $base_url; ?>index.php" class="btn btn-cart secondary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        <?php endif; ?>

        <div class="security-badge mt-3 text-muted">
            <i class="fas fa-shield-alt me-2"></i>Secure Checkout - SSL Encrypted
        </div>
    </div>
</div>

        </div>
    </div>


    <script>
    // Apply Coupon
  function applyCoupon() {
    const couponCode = $("#coupon-input").val().trim();
    const messageEl = $("#coupon-message");

    if (!couponCode) {
      messageEl.text("Please enter a coupon code.")
               .removeClass("text-success").addClass("text-danger");
      return;
    }

    $.post("ajax/apply-coupon.php", { coupon_code: couponCode }, function (res) {
      if (res.success) {
        messageEl.text("Coupon applied: " + res.totals.coupon_code + 
                       " (-₹" + res.totals.coupon_amount + ")")
                 .removeClass("text-danger").addClass("text-success");
        location.reload();
      } else {
        messageEl.text(res.message)
                 .removeClass("text-success").addClass("text-danger");
      }
    }, "json");
  }

  // Remove Coupon
  function removeCoupon() {
    $.post("ajax/remove-coupon.php", {}, function (res) {
      if (res.success) {
        location.reload();
      } else {
        alert("Failed to remove coupon.");
      }
    }, "json");
  }
  
        setTimeout(function() {
            let alertEl = document.querySelector('.alert');
            if (alertEl) {
                alertEl.classList.remove('show');
                alertEl.classList.add('fade');
            }
        }, 3000);
    </script>

    <?php
    include('include/footer.php');
    ?>
    </div>