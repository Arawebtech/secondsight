<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("admin/inc/config.php");
session_start();
$base_url = BASE_URL;
include('include/header.php');

// ----------------------
// User / Cart setup
// ----------------------
$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? '';
if (!$user_id) {
    $_SESSION['temp_user_id'] = rand(10000, 100000);
    $user_id = $_SESSION['temp_user_id'];
}


// Fetch cart items
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
function slugify($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug); // replace non-alphanumeric
    $slug = preg_replace('/-+/', '-', $slug); // collapse multiple hyphens
    return trim($slug, '-');
}


// ----------------------
// Determine category via slug
// ----------------------
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    die("Invalid Category");
}

$slug = rtrim($_GET['slug'], '/'); // remove trailing slash
$category = null;

// Fetch all top categories
$top_query = mysqli_query($con, "SELECT * FROM tbl_top_category WHERE show_on_menu = 1");
while ($top = mysqli_fetch_assoc($top_query)) {
    if (slugify($top['tcat_name']) === $slug) {
        $category = $top;
        break;
    }
}

if (!$category) {
    header("HTTP/1.0 404 Not Found");
    exit("Category not found");
}

$tcat_id = (int) $category['tcat_id'];
$title = htmlspecialchars($category['tcat_name']);

// ----------------------
// Fetch products in this category
// ----------------------
$where = "AND t.tcat_id = $tcat_id";

$query = "
    SELECT p.p_id, p.p_name, p.p_featured_photo, p.p_gst, p.p_unit,
           pr.p_current_price, pr.p_old_price, pr.p_sku, pr.color, pr.photo,
           pr.in_stoke, pr.p_qty, pr.p_weight
    FROM tbl_product p
    JOIN tbl_end_category e ON p.ecat_id = e.ecat_id
    JOIN tbl_mid_category m ON e.mcat_id = m.mcat_id
    JOIN tbl_top_category t ON m.tcat_id = t.tcat_id
    JOIN tbl_product_price pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1 $where AND pr.in_stoke > 0
    GROUP BY p.p_id
    ORDER BY p.p_id DESC
";

$result = mysqli_query($con, $query);
$base_url = BASE_URL;


?>

<div class="container my-5">
    <div class="row">
        <!-- Left: Products -->
        <div class="col-lg-9">
            <h3 class="fw-bold mb-4"><?= $title; ?></h3>
            <div class="row g-4">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $p_id = (int) $row['p_id'];
                        $p_name = htmlspecialchars($row['p_name']);
                        $p_price = (float) $row['p_current_price'];
                        $p_old = (float) $row['p_old_price'];
                        $p_photo = $row['photo'] ?: $row['p_featured_photo'];
                        $p_gst_per = (float) $row['p_gst'];
                        $p_gst_amt = round(($p_price * $p_gst_per / 100), 2);
                        $p_actual_price = $p_price;
                        $p_actual_old_price = $p_old;

                        $in_cart = $cart_items[$p_id] ?? null;
                        $cart_qty = $in_cart['qty'] ?? 0;

                        // SEO-friendly product URL
                        $product_slug = slugify($row['p_name']);
                        ?>
                        <div class="col-md-4 col-sm-6 text-center">
                            <div class="product-card position-relative h-100">
                                <a href="<?= $base_url; ?>product/<?= $product_slug; ?>" class="text-decoration-none text-dark">
                                    <img src="<?= $base_url; ?>assets/img/product-detail/<?= $p_photo; ?>" class="img-fluid"
                                        style="height:220px;object-fit:cover;" alt="<?= $p_name; ?>">
                                </a>
                                <div class="p-3 text-center">
                                    <h6 class="mb-1 fw-semibold"><?= $p_name; ?></h6>
                                    <div class="fw-bold fs-5">
                                        ₹<?= number_format($p_actual_price, 2); ?>
                                        <?php if ($p_actual_old_price > 0) { ?>
                                            <span class="text-muted text-decoration-line-through fs-6">
                                                ₹<?= number_format($p_actual_old_price, 2); ?>
                                            </span>
                                        <?php } ?>
                                    </div>

                                    <!-- Cart button container for JS -->
                                    <div class="d-flex justify-content-center align-items-center gap-2 mt-2 cart-btn-wrapper"
                                        data-id="<?= $p_id; ?>" data-name="<?= $p_name; ?>" data-price="<?= $p_price; ?>"
                                        data-gst="<?= $p_gst_amt; ?>" data-photo="<?= $p_photo; ?>"
                                        data-color="<?= htmlspecialchars($row['color']); ?>"
                                        data-weight="<?= htmlspecialchars($row['p_weight']); ?>"
                                        data-unit="<?= htmlspecialchars($row['p_unit']); ?>"
                                        data-sku="<?= htmlspecialchars($row['p_sku']); ?>"
                                        data-stock="<?= (int) $row['in_stoke']; ?>">
                                        <!-- JS will render Add to Cart / Qty buttons -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-warning">No products found in this category.</div></div>';
                }
                ?>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-3">
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
                        $slug = slugify($top['tcat_name']); ?>
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
        text-decoration: none;
    }

    .category-box:hover {
        background-color: #161394;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
        text-decoration: none;
    }
</style>

<?php include('include/footer.php'); ?>