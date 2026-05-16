<?php
include("admin/inc/config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? '';

if (!$user_id) {
  $_SESSION['temp_user_id'] = rand(10000, 100000);
  $user_id = $_SESSION['temp_user_id'];
}

// ✅ Fetch cart items in one query
$cart_items = [];
$cart_query = mysqli_query($con, "SELECT id as cart_id, p_id, no_of_item FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = 0");
while ($row = mysqli_fetch_assoc($cart_query)) {
  $cart_items[$row['p_id']] = [
    'cart_id' => $row['cart_id'],
    'qty' => $row['no_of_item']
  ];
}

// ----------------------------
// Slugify function
// ----------------------------
function slugify($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug); // replace non-alphanumeric
    $slug = preg_replace('/-+/', '-', $slug); // collapse multiple hyphens
    return trim($slug, '-');
}

include('include/header.php');


// Get search term from form (GET method from popup)
$search_query = trim($_GET['q'] ?? '');


function renderProductGrid($con, $query, $cart_items, $base_url, $ribbon = '')
{
  $result = mysqli_query($con, $query);

  if (!$result || mysqli_num_rows($result) === 0) {
    echo '<div class="col-12 text-center text-muted py-5">No products found.</div>';
    return;
  }

  while ($row = mysqli_fetch_assoc($result)):
    $p_id       = (int) $row['p_id'];
    $p_name     = ($row['p_name']);
    $p_price    = (float) $row['p_current_price'];
    $p_old_price = (float) $row['p_old_price'];
    $p_photo    = $row['photo'] ?: $row['p_featured_photo'];
    $p_color    = htmlspecialchars($row['color']);
    $p_weight   = htmlspecialchars($row['p_weight']);
    $p_unit     = htmlspecialchars($row['p_unit']);
    $p_sku      = htmlspecialchars($row['p_sku']);
    $p_gst_per  = (float) $row['p_gst'];
    $p_gst_amt  = round(($p_price * $p_gst_per / 100), 2);
    $p_actual_price = $p_price + $p_gst_amt;
    $p_actual_old_price = $p_old_price + round(($p_old_price * $p_gst_per / 100), 2);

    $stock_qty  = (int) $row['in_stoke'];

    $in_cart    = $cart_items[$p_id] ?? null;
    $cart_qty   = $in_cart['qty'] ?? 0;
?>
    <div class="col-md-6 col-lg-4 text-center">
      <div class="product-card position-relative">
        <?php if ($ribbon): ?>
          <span class="ribbon" style="background-color: rgb(214,242,253);">
            <?= htmlspecialchars($ribbon); ?>
          </span>
        <?php endif; ?>

        <?php $slug = slugify($p_name); ?>
        <a href="<?= $base_url; ?>product/<?= $slug; ?>" class="text-decoration-none text-dark">
          <img src="<?= $base_url; ?>assets/img/product-detail/<?= $p_photo; ?>"
            class="img-fluid" alt="<?= $p_name; ?>">
        </a>

        <div class="p-3">
          <h6 class="mb-1 fw-semibold"><?= $p_name; ?></h6>
          <div class="text-warning small">★★★★★
            <span class="text-muted">1311 reviews</span>
          </div>
          <div class="fw-bold fs-5">
            ₹<?= number_format($p_actual_price, 2); ?>
            <span class="text-muted text-decoration-line-through fs-6">
              ₹<?= number_format($p_actual_old_price, 2); ?>
            </span>
          </div>

          <div class="d-flex justify-content-center align-items-center gap-2 mt-2 cart-btn-wrapper"
            data-id="<?= $p_id; ?>"
            data-name="<?= $p_name; ?>"
            data-price="<?= $p_price; ?>"
            data-gst="<?= $p_gst_amt; ?>"
            data-photo="<?= $p_photo; ?>"
            data-color="<?= $p_color; ?>"
            data-weight="<?= $p_weight; ?>"
            data-unit="<?= $p_unit; ?>"
            data-sku="<?= $p_sku; ?>"
            data-stock="<?= $stock_qty; ?>">
            <!-- JS syncHomepageButtons() will render buttons -->
          </div>
        </div>
      </div>
    </div>
<?php endwhile;
}
?>

<div class="container my-5">
    <h4 class="mb-4">
        Search results for: 
        <span class="text-primary"><?php echo htmlspecialchars($search_query); ?></span>
    </h4>

    <div class="row g-4">
<?php
$searchTerm = mysqli_real_escape_string($con, $_GET['q'] ?? '');

// Build your search query
$query = "
    SELECT 
        p.p_id, 
        p.p_name,
        p.p_featured_photo,
        p.p_gst,
        p.p_unit,
        pr.p_current_price,
        pr.p_old_price,
        pr.p_sku,
        pr.color,
        pr.photo,
        pr.in_stoke,
        pr.p_qty,
        pr.p_weight
    FROM tbl_product p
    JOIN (
        SELECT * 
        FROM tbl_product_price 
        GROUP BY p_id
    ) pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1
      AND (
            p.p_name LIKE '%$searchTerm%'
         OR pr.color LIKE '%$searchTerm%'
         OR pr.p_sku LIKE '%$searchTerm%'
      )
    ORDER BY p.p_name ASC
";

renderProductGrid($con, $query, $cart_items, $base_url);
?>

</div>

</div>

<?php include("include/footer.php"); ?>

</body>
</html>
