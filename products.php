<?php
include("admin/inc/config.php");
session_start();
$base_url = BASE_URL;

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


// ✅ Pagination
$products_per_page = 18;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// ✅ Handle Filter
$orderby = $_GET['orderby'] ?? 'menu_order';
$where_conditions = ["p.p_is_active = 1", "pr.in_stoke > 0"];

switch ($orderby) {
    case 'trending':
        $where_conditions[] = "p.p_is_trending = 1";
        $order_sql = "p.p_id DESC";
        $extra_select = "";
        $extra_join   = "";
        $extra_group  = "";
        break;

    case 'bestseller':
        $order_sql = "sold_qty DESC";
        $extra_select = ", SUM(o.no_of_item) AS sold_qty";
        $extra_join   = "JOIN tbl_order o ON o.p_id = p.p_id";
        $extra_group  = "GROUP BY p.p_id";
        break;

    case 'price':
        $order_sql = "pr.p_current_price ASC";
        $extra_select = "";
        $extra_join   = "";
        $extra_group  = "";
        break;

    case 'price-desc':
        $order_sql = "pr.p_current_price DESC";
        $extra_select = "";
        $extra_join   = "";
        $extra_group  = "";
        break;

    case 'date':
        $order_sql = "p.p_id DESC";
        $extra_select = "";
        $extra_join   = "";
        $extra_group  = "";
        break;

    default:
        $order_sql = "p.p_id DESC";
        $extra_select = "";
        $extra_join   = "";
        $extra_group  = "";
}

// ✅ Count Total Products
$total_query = "
    SELECT COUNT(DISTINCT p.p_id) as total
    FROM tbl_product p
    LEFT JOIN tbl_product_price pr ON p.p_id = pr.p_id
    " . ($orderby == 'bestseller' ? "JOIN tbl_order o ON o.p_id = p.p_id" : "") . "
    WHERE " . implode(" AND ", $where_conditions);

$total_result = mysqli_query($con, $total_query);
if (!$total_result) {
    die("Error in counting products: " . mysqli_error($con));
}
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $products_per_page);

include('include/header.php');
?>

<?php
function renderProductGrid($con, $query, $cart_items, $base_url, $ribbon = '') {
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo '<div class="col-12 text-center text-danger py-5">
                <strong>Database Error:</strong> ' . mysqli_error($con) . '<br>
                <small>Please report this to the administrator.</small>
              </div>';
        return;
    }

    if (mysqli_num_rows($result) === 0) {
        echo '<div class="col-12 text-center text-muted py-5">No products found.</div>';
        return;
    }

    while ($row = mysqli_fetch_assoc($result)):
        $p_id       = (int) $row['p_id'];
        $p_name     = htmlspecialchars($row['p_name']);
        $p_price    = (float) $row['p_current_price'];
        $p_old_price= (float) $row['p_old_price'];
        $p_photo    = $row['photo'] ?: $row['p_featured_photo'];
        $p_color    = htmlspecialchars($row['color']);
        $p_weight   = htmlspecialchars($row['p_weight']);
        $p_unit     = htmlspecialchars($row['p_unit']);
        $p_sku      = htmlspecialchars($row['p_sku']);
        $p_gst_per  = (float) $row['p_gst'];
        $p_gst_amt  = round(($p_price * $p_gst_per / 100), 2);
        $p_actual_price = $p_price;
        $p_actual_old_price = $p_old_price;

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


  <!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0 custom-breadcrumb-bg">
  <style>
    .custom-breadcrumb-bg {
      background-image: url('assets/images/product-ban1.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 70vh;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    @media (max-width: 768px) {
      .custom-breadcrumb-bg {
        height: 300px;
      }
    }

    .breadcrumb-contain h2 {
      font-size: 50px !important;
      color: #fff;
      margin: 0;
    }
  </style>

  <div class="breadcrumb-contain">
    <h2>Products</h2>
  </div>
</section>
<!-- Breadcrumb Section End -->


<!-- Our Products -->
<div class="container py-5">
  <div class="text-center mb-5">
               <span style="color:#e97d1f;">Our
         </span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;"> Products</h4>
  </div>

  <div class="row">
    <!-- ✅ Left Column (Products) -->
    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Filter -->
        <form method="GET" id="productFilterForm" class="mb-0">
          <select name="orderby" class="orderby form-control" style="color: #555;appearance: auto; border-radius: 0;
    box-shadow: none;
    border-color: #d2d6de;width:auto;display:inline-block;"
            onchange="document.getElementById('productFilterForm').submit()">
            <option value="menu_order" <?= (!isset($_GET['orderby']) || $_GET['orderby'] == 'menu_order') ? 'selected' : ''; ?>>Default sorting</option>
            <option value="trending" <?= (isset($_GET['orderby']) && $_GET['orderby'] == 'trending') ? 'selected' : ''; ?>>Trending</option>
            <option value="bestseller" <?= (isset($_GET['orderby']) && $_GET['orderby'] == 'bestseller') ? 'selected' : ''; ?>>Best Sellers</option>
            <option value="date" <?= (isset($_GET['orderby']) && $_GET['orderby'] == 'date') ? 'selected' : ''; ?>>Sort by latest</option>
            <option value="price" <?= (isset($_GET['orderby']) && $_GET['orderby'] == 'price') ? 'selected' : ''; ?>>Price: low to high</option>
            <option value="price-desc" <?= (isset($_GET['orderby']) && $_GET['orderby'] == 'price-desc') ? 'selected' : ''; ?>>Price: high to low</option>
          </select>
        </form>
        
<style>
.pagination .page-link {
  background: transparent;
  border: 1px solid #ddd;
  color: #000;
  font-weight: 500;
  margin: 0 3px;
  transition: all 0.3s ease;
  padding: 8px 14px;
  border-radius: 6px;
}
.pagination .page-link:hover {
  background: #000;
  color: #fff !important;
}
.pagination .active .page-link,
.pagination .active-page {
  background: #000 !important;
  color: #fff !important;
  border-color: #000 !important;
}
.pagination .prev-next {
  background: #000;
  color: #fff !important;
  border: 1px solid #000;
  font-weight: 600;
}
.pagination .prev-next:hover {
  background: #333;
  border-color: #333;
}
</style>

      </div>

      <div class="row g-4">
        <?php
        // ✅ Main Query
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
        $extra_select
    FROM tbl_product p
    JOIN tbl_product_price pr ON p.p_id = pr.p_id
    $extra_join
    WHERE " . implode(" AND ", $where_conditions) . "
    GROUP BY p.p_id
    ORDER BY $order_sql
    LIMIT $products_per_page OFFSET $offset
";
        renderProductGrid($con, $query, $cart_items, $base_url);
         ?>
      </div>

      <!-- Pagination -->
<?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation" class="mt-5">
  <ul class="pagination justify-content-center">
    <!-- Prev Button -->
    <?php if($page > 1): ?>
      <li class="page-item">
        <a class="page-link prev-next" 
           href="?orderby=<?= $orderby ?>&page=<?= $page-1; ?>">&laquo; Prev</a>
      </li>
    <?php endif; ?>

    <!-- First Page -->
    <?php if($page > 3): ?>
      <li class="page-item">
        <a class="page-link text-dark" href="?orderby=<?= $orderby ?>&page=1">1</a>
      </li>
      <li class="page-item disabled"><span class="page-link">...</span></li>
    <?php endif; ?>

    <!-- Page Numbers -->
    <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
      <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
        <a class="page-link <?= ($i == $page) ? 'active-page' : ''; ?>" 
           href="?orderby=<?= $orderby ?>&page=<?= $i; ?>">
          <?= $i; ?>
        </a>
      </li>
    <?php endfor; ?>

    <!-- Last Page -->
    <?php if($page < $total_pages - 2): ?>
      <li class="page-item disabled"><span class="page-link">...</span></li>
      <li class="page-item">
        <a class="page-link text-dark" href="?orderby=<?= $orderby ?>&page=<?= $total_pages; ?>"><?= $total_pages; ?></a>
      </li>
    <?php endif; ?>

    <!-- Next Button -->
    <?php if($page < $total_pages): ?>
      <li class="page-item">
        <a class="page-link prev-next" 
           href="?orderby=<?= $orderby ?>&page=<?= $page+1; ?>">Next &raquo;</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>
<?php endif; ?>

    </div>

    <!-- ✅ Right Sidebar -->
    <div class="col-lg-3">
      <!-- 6 Products Section -->
      <div class="mb-5"> 
        <h5 class="fw-bold mb-4">Other Products</h5>
        <?php
          $side_query = mysqli_query($con, "
            SELECT p.p_id, p.p_name, pr.p_current_price, pr.p_old_price, pr.photo, p.p_featured_photo
            FROM tbl_product p
            JOIN (SELECT * FROM tbl_product_price GROUP BY p_id) pr ON p.p_id = pr.p_id
            WHERE p.p_is_active = 1 AND pr.in_stoke > 0
            ORDER BY RAND()
            LIMIT 6
        ");
          while ($side = mysqli_fetch_assoc($side_query)) {
              $slug = slugify($side['p_name']);
        ?>
          <div class="d-flex mb-3 border-bottom pb-2 ">
            <img src="<?= $base_url; ?>assets/img/product-detail/<?= $side['photo'] ?: $side['p_featured_photo']; ?>" 
                 class="me-3" style="width:70px; height:70px; object-fit:cover;" alt="">
            <div>
              
              <a href="<?= $base_url; ?>product/<?= $slug; ?>" class="text-decoration-none text-dark">
                <?= $side['p_name']; ?>
              </a>
              <div class="d-flex align-items-center gap-2">
                  <div class="small text-danger text-decoration-line-through">₹<?= $side['p_old_price']; ?></div>
                  <div class="fw-bold text-dark">₹<?= $side['p_current_price']; ?></div>
                </div>
            </div>
          </div>
        <?php } ?>
      </div>

      <!-- Contact Form -->
      <div class="p-4 border rounded">
        <h5 class="fw-bold mb-3">Any questions? Contact us!</h5>
        <form method="POST" action="contact-submit.php">
          <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Name *" required>
          </div>
          <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="E-mail *" required>
          </div>
          <div class="mb-3">
            <textarea name="message" class="form-control" placeholder="Message *" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-dark w-100" style="background-color:#161394;">Send</button>
        </form>
      </div>
      
      <!-- Categories Section in Sidebar -->
        <!-- Top Categories -->
<div class="p-4 border rounded mb-5 mt-5">
    <h5 class="fw-bold mb-4">Shop by Category</h5>
    <div class="row row-cols-2 g-2">
        <?php
        $top_query = mysqli_query($con, "
            SELECT tcat_id, tcat_name 
            FROM tbl_top_category 
            WHERE show_on_menu = 1 
            ORDER BY tcat_name ASC
        ");
        while ($top = mysqli_fetch_assoc($top_query)) {
        $slug = slugify($top['tcat_name']);
        ?>
            <div class="col">
                <a href="<?= $base_url; ?>category/<?= $slug; ?>" 
                   class="category-box d-block fw-semibold small text-capitalize text-center h-100">
                    <?= htmlspecialchars($top['tcat_name']); ?>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

    </div>
  </div>
</div>
<style>
.category-box {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
    color: #333;
    background-color: #fff;
    transition: all 0.3s ease;
    text-decoration:none;
}
.category-box:hover {
    background-color: #161394;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transform: translateY(-2px);
    text-decoration: none;
}
</style>

<!-- Our Products ends -->

<?php
include ('include/footer.php');

?>