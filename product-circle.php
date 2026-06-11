<?php
include("admin/inc/config.php");

session_start();

// Ensure a user_id is always set
$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? '';

if (!$user_id) {
    $_SESSION['temp_user_id'] = rand(10000, 100000);
    $user_id = $_SESSION['temp_user_id'];
}

// Fetch all cart items to check if product already added
$cart_items = [];
$cart_query = mysqli_query($con, "SELECT id as cart_id, p_id as product_id, no_of_item FROM tbl_cart WHERE user_id = '$user_id'");
while ($cart_row = mysqli_fetch_assoc($cart_query)) {
  $cart_items[$cart_row['product_id']] = [
    'cart_id' => $cart_row['cart_id'],
    'qty' => $cart_row['no_of_item']
  ];
}

// ----------------------------
// Get slug from URL
// ----------------------------
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("HTTP/1.0 404 Not Found");
    exit("Product not found");
}
$slug = $_GET['slug'];

// ----------------------------
// Slugify function
// ----------------------------
function slugify($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug); // replace non-alphanumeric
    $slug = preg_replace('/-+/', '-', $slug); // collapse multiple hyphens
    return trim($slug, '-');
}

// Fetch product details for given ID
$query = "
    SELECT 
        p.p_id, 
        p.p_name,
        p.p_featured_photo,
        p.p_gst,
        p.p_unit,
        p.p_short_description,
        p.p_description,
        pr.p_current_price,
        pr.p_old_price,
        pr.p_sku,
        pr.color,
        pr.photo,
        pr.in_stoke,
        pr.p_qty,
        pr.p_weight
    FROM tbl_product p
    LEFT JOIN tbl_product_price pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1
";

$result = mysqli_query($con, $query);
$product = null;

// ----------------------------
// Compare slug dynamically
// ----------------------------
while ($row = mysqli_fetch_assoc($result)) {
    $generated_slug = slugify($row['p_name']);

    if ($generated_slug === $slug) {
        $product = $row;
        break;
    }
}

// ----------------------------
// Handle not found
// ----------------------------
if (!$product) {
    header("HTTP/1.0 404 Not Found");
    exit("Product not found");
}

$p_id = $product['p_id'];

// ----------------------------
// Fetch reviews for this product
// ----------------------------
$reviews = [];
$avg_rating = 0;
$total_reviews = 0;

$reviews_query = mysqli_query($con, "
    SELECT r.*, c.full_name 
    FROM tbl_reviews r
    JOIN tbl_user c ON r.customer_id = c.id
    WHERE r.product_id = '{$product['p_id']}'
    ORDER BY r.created_at DESC
");

if ($reviews_query && mysqli_num_rows($reviews_query) > 0) {
    while ($row = mysqli_fetch_assoc($reviews_query)) {
        $reviews[] = $row;
    }
    
    // Calculate average rating
    $avg_query = mysqli_query($con, "
        SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
        FROM tbl_reviews WHERE product_id = '{$product['p_id']}'
    ");
    if ($avg_query) {
        $avg_data = mysqli_fetch_assoc($avg_query);
        $avg_rating = round($avg_data['avg_rating'], 1);
        $total_reviews = $avg_data['total_reviews'];
    }
}

// Product-specific variables
$page_title       = $product['p_name'] . " | Second Sight Foundation";
$page_description = "Check out this product: " . $product['p_name'];
$page_keywords    = "product, secondsight, " . $product['p_name'];

$product_name   = $product['p_name'];
$product_img    = BASE_URL . "assets/img/product-detail/" . ($product['photo'] ?: $product['p_featured_photo']);

$product_url    = BASE_URL . "product/" . $slug;
$share_text     = "Check out this product: " . $product['p_name'];

// Then include the common header
include('include/header.php');
?>


<link rel="stylesheet" href="<?= $base_url; ?>assets/css/product-detail.css">
<!-- Product Left Sidebar Start -->
<section class="product-section">
  <div class="container-fluid-lg">
    <div class="row">
      <div class="col-xxl-9 col-xl-8 col-lg-7 wow fadeInUp">
        <div class="row g-4">
          <?php
          // Assume $product contains the product details (e.g., from tbl_product)
          // Assume $p_id is set
          // Fetch gallery images
          $gallery_images = [];
          $photo_query = mysqli_query($con, "SELECT photo FROM tbl_product_photo WHERE p_id = '$p_id'");
          while ($photo_row = mysqli_fetch_assoc($photo_query)) {
            $gallery_images[] = $photo_row['photo'];
          }
          ?>

          <div class="col-xl-6 wow fadeInUp">
            <div class="product-left-box">
              <div class="row g-sm-4 g-2">
                <!-- Main Image Gallery -->
                <div class="col-12">
                  <div class="product-main no-arrow">
                    <?php
                    $img_index = 0;

                    // Featured image
                    echo '<div>
                                          <div class="slider-image">
                                            <img 
                                              src="' . $base_url . 'assets/img/product/' . $product['p_featured_photo'] . '" 
                                              data-zoom-image="' . $base_url . 'assets/img/product/' . $product['p_featured_photo'] . '" 
                                              class="img-fluid image_zoom_cls-' . $img_index++ . ' blur-up lazyload" 
                                              alt="' . htmlspecialchars($product['p_name']) . '">
                                          </div>
                                        </div>';

                    // Additional gallery images
                    foreach ($gallery_images as $img) {
                      echo '<div>
                                              <div class="slider-image">
                                                <img 
                                                  src="' . $base_url . 'assets/img/product/' . $img . '" 
                                                  data-zoom-image="' . $base_url . 'assets/img/product/' . $img . '" 
                                                  class="img-fluid image_zoom_cls-' . $img_index++ . ' blur-up lazyload" 
                                                  alt="">
                                              </div>
                                            </div>';
                    }
                    ?>
                  </div>
                </div>

                <!-- Thumbnails -->
                <div class="col-12">
                  <div class="left-slider-image left-slider no-arrow slick-top">
                    <?php
                    // Featured image thumbnail
                    echo '<div>
                                          <div class="sidebar-image">
                                            <img 
                                              src="' . $base_url . 'assets/img/product/' . $product['p_featured_photo'] . '" 
                                              class="img-fluid blur-up lazyload" 
                                              alt="">
                                          </div>
                                        </div>';

                    // Gallery thumbnails
                    foreach ($gallery_images as $img) {
                      echo '<div>
                                              <div class="sidebar-image">
                                                <img 
                                                  src="' . $base_url . 'assets/img/product/' . $img . '" 
                                                  class="img-fluid blur-up lazyload" 
                                                  alt="">
                                              </div>
                                            </div>';
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $cart_qty = isset($cart_items[$product['p_id']]) ? $cart_items[$product['p_id']]['qty'] : 0;
          $in_cart = $cart_qty > 0;
          $available_stock = $product['in_stoke']; // from your query

          // Discount Calculation
          $p_gst_per = (float)$product['p_gst'];
          $p_gst_amt = round(($product['p_current_price'] * $p_gst_per / 100), 2);
          $p_actual_price = $product['p_current_price'];
          $p_actual_old_price = (float)$product['p_old_price'];

          $discount = 0;
          if ($product['p_old_price'] > 0) {
            $discount = round((($product['p_old_price'] - $product['p_current_price']) / $product['p_old_price']) * 100);
          }
          ?>
          <div class="col-xl-6 wow fadeInUp">
            <div class="right-box-contain">
              <h6 class="offer-top " style="text-align: start;"><?= $discount; ?>% OFF</h6>
              <h2 class="name" style="text-align:start;"><?= $product['p_name']; ?></h2>

              <div class="price-rating">
                <div class="product-rating custom-rate">
                  <ul class="rating">
                    <li><i data-feather="star" class="fill"></i></li>
                    <li><i data-feather="star" class="fill"></i></li>
                    <li><i data-feather="star" class="fill"></i></li>
                    <li><i data-feather="star" class="fill"></i></li>
                    <li><i data-feather="star"></i></li>
                    <span class="review">23 Customer Review</span>
                  </ul>
                  <div class="d-flex mb-2 mt-3 ">
                    <button style=" background-color: #ffd269;border: none; border-radius: 20px; padding: 7px;">Removes Negativity</button> <br>
                    <button style="background-color: #a4d8ff; margin-left: 10px; border: none; border-radius: 20px;">Boost Confidence</button>
                  </div>
                </div>
                <h3 class="theme-color price text-dark">
                  ₹<?= number_format($p_actual_price, 2); ?> <del class="text-content">₹<?= number_format($p_actual_old_price, 2); ?></del>
                  <span class="offer theme-color">(<?= $discount; ?>% off)</span>
                </h3>
              </div>
              <div class="time deal-timer product-deal-timer mx-md-0 mx-auto" id="clockdiv-1"
                data-hours="1" data-minutes="2" data-seconds="3">
                <div class="product-title" style="color: red;">
                  <h4>Hurry up! Sales Ends In</h4>
                </div>
                <ul style="color: red;">
                  <li>
                    <div class="counter d-block ">
                      <div class="days d-block">
                        <h5></h5>
                      </div>
                      <h6>Days</h6>
                    </div>
                  </li>
                  <li>
                    <div class="counter d-block">
                      <div class="hours d-block">
                        <h5></h5>
                      </div>
                      <h6>Hours</h6>
                    </div>
                  </li>
                  <li>
                    <div class="counter d-block">
                      <div class="minutes d-block">
                        <h5></h5>
                      </div>
                      <h6>Min</h6>
                    </div>
                  </li>
                  <li>
                    <div class="counter d-block">
                      <div class="seconds d-block">
                        <h5></h5>
                      </div>
                      <h6>Sec</h6>
                    </div>
                  </li>
                </ul>
              </div>
              
              <?php if (!empty($product['p_short_description'])): ?>
                    <div class="mb-3">
                      <strong>Quick Overview:</strong>
                      <p><?= $product['p_short_description']; ?></p>
                    </div>
                  <?php endif; ?>
              <div class="note-box product-package">
                  <div class="cart-btn-wrapper"
                       data-id="<?= $product['p_id']; ?>"
                       data-name="<?= htmlspecialchars($product['p_name']); ?>"
                       data-price="<?= $product['p_current_price']; ?>"
                       data-gst="<?= $p_gst_amt; ?>"
                       data-photo="<?= $product['photo'] ?: $product['p_featured_photo']; ?>"
                       data-color="<?= $product['color'] ?? ''; ?>"
                       data-weight="<?= $product['p_weight'] ?? ''; ?>"
                       data-unit="<?= $product['p_unit'] ?? ''; ?>"
                       data-sku="<?= $product['p_sku'] ?? ''; ?>"
                       data-stock="<?= $product['in_stoke'] ?? 0; ?>">
                  </div>
                </div>


              <div class="payment-option">
                <div class="product-title">
                  <h4>Share this product</h4>
                </div>
                  <button 
                    type="button" 
                    onclick="shareProduct()" 
                    class="btn flex-fill" style="background-color: #161394;color:white;
                  border-radius: 25px;
                  padding: 8px 40px;">
                    <i class="fa fa-share-alt"></i> Share
                  </button>
                  <script>
                    function shareProduct() {
                      const shareData = {
                        title: <?= json_encode($product_name) ?>,
                        text: <?= json_encode($share_text) ?>,
                        url: <?= json_encode($product_url) ?>
                      };
                      // Try to add image if supported (not all browsers support images in Web Share API)
                      if (navigator.canShare && navigator.canShare({ files: [] })) {
                        fetch(<?= json_encode($product_img) ?>)
                          .then(res => res.blob())
                          .then(blob => {
                            const file = new File([blob], "product.jpg", { type: blob.type });
                            shareData.files = [file];
                            return navigator.share(shareData);
                          })
                          .catch(() => fallbackCopy());
                      } else if (navigator.share) {
                        navigator.share(shareData).catch(() => {});
                      } else {
                        fallbackCopy();
                      }
                      function fallbackCopy() {
                        navigator.clipboard.writeText(shareData.url).then(() => {
                          alert("Product link copied to clipboard!");
                        });
                      }
                    }
                  </script>
              </div>
            </div>
          </div>
        </div>
                          <!-- specification -->
        <section class="specifications py-4 text-start">
        <div class="container">
          <!-- <h2 class="faq-title mb-3" style="font-weight: 500;">Specifications</h2>    -->

          <?php if (trim(strip_tags($product['p_description'])) !== ''): ?>
            <div class="mt-4">
              <h4 class="mb-2">Detailed Description</h4>
              <div class="product-description">
                <?= $product['p_description']; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        </section>
      </div>

      <div class="col-xxl-3 col-xl-4 col-lg-5 d-none d-lg-block wow fadeInUp">
        <div class="right-sidebar-box">
          <div class="vendor-box">
            <div class="vendor-contain">
              <p class="vendor-detail"><b>Natural Stone Jewelry – Stylish & Soulful </b>
                Crafted from nature’s gems, our rings, malas, and bracelets not only enhance your style but also bring positive energy and healing vibes. Each stone tells a story—of peace, love, or confidence. Wear beauty with purpose.</p>

              <div class="vendor-list">
                <ul>
                  <li>
                    <div class="address-contact">
                      <i data-feather="map-pin"></i>
                      <h5>Address: <span class="text-content">AE-10, Ground Floor, Tagore Garden, Near Tagore Garden Metro Station Gate Number 1 Exit, New Delhi, Delhi 110027</span></h5>
                    </div>
                  </li>

                  <li>
                    <div class="address-contact">
                      <i data-feather="headphones"></i>
                      <h5>Contact Seller: <span class="text-content">9716517463</span></h5>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <div class="pt-25">
              <div class="hot-line-number">
                <h5>Hotline Order:</h5>
                <h6>Mon - Fri: 07:00 am - 08:30PM</h6>
                <h3>+91-9716517463</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- Product Left Sidebar End -->


<!-- Our Products -->
<div class="container py-5">
  <div class="title title-4">
    <h2 style="text-align: center !important;">Related Products</h2>
  </div>
  <div class="row g-4">
    <!-- Product Card Example -->
    <?php
// ✅ Fetch related products (excluding the main product)
$related_sql = "
    SELECT 
        p.p_id,
        pr.id as price_id,
        p.p_name,
        p.p_featured_photo,
        pr.p_current_price,
        pr.p_old_price,
        p.p_gst,
        pr.p_sku,
        pr.color,
        pr.photo,
        pr.in_stoke,
        pr.p_weight,
        p.p_unit
    FROM tbl_product p
    JOIN tbl_product_price pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1 
      AND pr.in_stoke > 0
      AND p.p_id != '$p_id'
      ORDER BY RAND()
    LIMIT 4
";
$related_result = mysqli_query($con, $related_sql);

while ($row = mysqli_fetch_assoc($related_result)) {
    $price_id = $row['price_id']; 
    $rel_p_id = $row['p_id'];

    $rel_p_gst_per = (float)$row['p_gst'];
    $rel_p_gst_amt = round(((float)$row['p_current_price'] * $rel_p_gst_per / 100), 2);
    $rel_p_actual_price = (float)$row['p_current_price'];
    $rel_p_actual_old_price = (float)$row['p_old_price'];

    // ✅ check by p_id since cart is stored with p_id
    $in_cart = isset($cart_items[$rel_p_id]);
    $qty = $in_cart ? $cart_items[$rel_p_id]['qty'] : 0;
?>
  <div class="col-md-6 col-lg-3">
    <!-- <span class="ribbon" style="background-color: rgb(214, 242, 253);">
        Attracts Success
      </span> -->
    <div class="product-card position-relative text-center">
      

            <a href="<?= $base_url; ?>product/<?= $slug; ?>" class="text-decoration-none text-dark">
        <img src="<?= $base_url; ?>assets/img/product/<?= $row['p_featured_photo']; ?>" 
             class="img-fluid" 
             alt="<?= htmlspecialchars($row['p_name']); ?>">
        <div class="p-3">
          <h6 class="mb-1 fw-semibold text-center"><?= $row['p_name']; ?></h6>
        </div>
      </a>

      <div class="text-warning small text-center">
        ★★★★★ <span class="text-muted">1311 reviews</span>
      </div>
      
      <div class="fw-bold fs-5 text-center">
        ₹<?= number_format($rel_p_actual_price, 2); ?> 
        <span class="text-muted text-decoration-line-through fs-6">
          ₹<?= number_format($rel_p_actual_old_price, 2); ?>
        </span>
      </div>

          <?php 
            $rel_p_id = $row['p_id']; // ✅ must match tbl_cart's p_id
            $in_cart = isset($cart_items[$rel_p_id]);
            $qty = $in_cart ? $cart_items[$rel_p_id]['qty'] : 0;
            ?>
            <div class="d-flex justify-content-center cart-btn-wrapper"
                 data-id="<?= $rel_p_id; ?>" 
                 data-name="<?= htmlspecialchars($row['p_name']); ?>"
                 data-price="<?= $row['p_current_price']; ?>"
                 data-gst="<?= $rel_p_gst_amt; ?>"
                 data-photo="<?= $row['photo'] ?: $row['p_featured_photo']; ?>"
                 data-color="<?= $row['color'] ?? ''; ?>"
                 data-weight="<?= $row['p_weight'] ?? ''; ?>"
                 data-unit="<?= $row['p_unit'] ?? ''; ?>"
                 data-sku="<?= $row['p_sku'] ?? ''; ?>"
                 data-stock="<?= $row['in_stoke'] ?? 0; ?>">
            </div>

    </div>
  </div>
<?php } ?>

</div>
</div>


<!-- ✅ Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

<!-- ✅ Font Awesome for Stars & Arrows -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />

  <!-- ✅ Testimonial Slider Section -->
<div class="testimonial-section py-5" style="
  background: url('<?= $base_url; ?>assets/images/banner4.png') center center / cover no-repeat fixed;
  min-height: 400px;
">

  <div class="container position-relative">
     <div class="text-center mb-5">
    <h4 class="fw-bold" style="font-size:40px; color: #fff;">Testimonials</h4>
  </div>
  
    <div class="swiper testimonialSwiper">
      <div class="swiper-wrapper">
        <!-- ✅ Slide 1 -->
          <!--<div class="swiper-slide mt-4">-->
          <!--  <div class="text-center">-->
          <!--    <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">-->
          <!--      <img src="<?= $base_url; ?>assets/images/4-1.jpg"-->
          <!--           class="img-fluid rounded mb-3"-->
          <!--           style="height: 200px; object-fit: cover; border-radius: 20px !important;">-->
          <!--      <div class="text-warning fs-6 mb-2">-->
          <!--        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>-->
          <!--        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>-->
          <!--        <i class="fa-solid fa-star"></i>-->
          <!--      </div>-->
          <!--      <p class="mb-1 small">"Second Sight Foundation has truly transformed my life. I ordered a healing bracelet and took a tarot reading — both experiences were deeply calming and insightful. I felt a shift in my energy within days. Highly recommended for anyone seeking real spiritual guidance."</p>-->
          <!--      <strong class="small fw-bold">- Ankit Mishra -</strong>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div>-->
        
          <!-- ✅ Slide 2 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
                <img src="<?= $base_url; ?>assets/images/ankita.jpg"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"I was new to crystal healing and wasn’t sure what to expect, but the team at Second Sight was so patient and knowledgeable. Their energy kit helped me focus better and brought so much peace into my space. Their products are authentic and beautifully packaged."</p>
                <strong class="small fw-bold">- Ankita Shukla -</strong>
              </div>
            </div>
          </div>
        
          <!-- ✅ Slide 3 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
                <img src="<?= $base_url; ?>assets/images/suchita.jpg"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"The aura cleansing session I had was powerful! I’ve been feeling lighter, more positive, and balanced ever since. Thank you Second Sight Foundation for guiding me with compassion and care. I’m now a regular customer!"</p>
              </br>
                <strong class="small fw-bold">- Shuchita Panday -</strong>
              </div>
            </div>
          </div>
        
          <!-- ✅ Slide 4 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px !important; max-width: 280px; margin: auto;">
                <img src="data:image/jpeg;base64,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"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"The aura cleansing session was truly impactful! I feel emotionally lighter, more grounded, and filled with positivity. Thank you Second Sight Foundation for the gentle guidance and care. I’ll definitely keep coming back!."</p>
               </br>
                <strong class="small fw-bold"> — Amit R., Delhi -</strong>
              </div>
            </div>
          </div>
          <!-- ✅ Slide 1 -->
          <!--<div class="swiper-slide mt-4">-->
          <!--  <div class="text-center">-->
          <!--    <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">-->
          <!--      <img src="<?= $base_url; ?>assets/images/4-1.jpg"-->
          <!--           class="img-fluid rounded mb-3"-->
          <!--           style="height: 200px; object-fit: cover; border-radius: 20px !important;">-->
          <!--      <div class="text-warning fs-6 mb-2">-->
          <!--        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>-->
          <!--        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>-->
          <!--        <i class="fa-solid fa-star"></i>-->
          <!--      </div>-->
          <!--      <p class="mb-1 small">"Second Sight Foundation has truly transformed my life. I ordered a healing bracelet and took a tarot reading — both experiences were deeply calming and insightful. I felt a shift in my energy within days. Highly recommended for anyone seeking real spiritual guidance."</p>-->
          <!--      <strong class="small fw-bold">- Ankit Mishra -</strong>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div>-->
        
          <!-- ✅ Slide 2 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
                <img src="<?= $base_url; ?>assets/images/ankita.jpg"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"I was new to crystal healing and wasn’t sure what to expect, but the team at Second Sight was so patient and knowledgeable. Their energy kit helped me focus better and brought so much peace into my space. Their products are authentic and beautifully packaged."</p>
                <strong class="small fw-bold">- Ankita Shukla -</strong>
              </div>
            </div>
          </div>
        
          <!-- ✅ Slide 3 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
                <img src="<?= $base_url; ?>assets/images/suchita.jpg"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"The aura cleansing session I had was powerful! I’ve been feeling lighter, more positive, and balanced ever since. Thank you Second Sight Foundation for guiding me with compassion and care. I’m now a regular customer!"</p>
              </br>
                <strong class="small fw-bold">- Shuchita Panday -</strong>
              </div>
            </div>
          </div>
        
          <!-- ✅ Slide 4 -->
          <div class="swiper-slide mt-4">
            <div class="text-center">
              <div class="bg-white p-3" style="border-radius: 30px !important; max-width: 280px; margin: auto;">
                <img src="data:image/jpeg;base64,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"
                     class="img-fluid rounded mb-3"
                     style="height: 200px; object-fit: cover; border-radius: 20px !important;">
                <div class="text-warning fs-6 mb-2">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
                <p class="mb-1 small">"The aura cleansing session was truly impactful! I feel emotionally lighter, more grounded, and filled with positivity. Thank you Second Sight Foundation for the gentle guidance and care. I’ll definitely keep coming back!."</p>
               </br>
                <strong class="small fw-bold"> — Amit R., Delhi -</strong>
              </div>
            </div>
          </div>
        </div>
      <!-- ✅ Navigation Arrows -->
      <div class="swiper-button-next"><i class="fa-solid fa-arrow-right"></i></div>
      <div class="swiper-button-prev"><i class="fa-solid fa-arrow-left"></i></div>
    </div>
  </div>
</div>
 <!-- testimonals section end -->

<!-- ✅ Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<!-- ✅ Swiper Init Script -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    new Swiper(".testimonialSwiper", {
      loop: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      spaceBetween: 30,
      breakpoints: {
        0: {
          slidesPerView: 1
        },
        768: {
          slidesPerView: 2
        },
        992: {
          slidesPerView: 3
        }
      }
    });
  });
</script>

<!-- ✅ Swiper Arrow Styles -->
<style>
  .swiper-button-next,
  .swiper-button-prev {
    background-color: white;
    color: #a06ee2;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
  }

  .swiper-button-next::after,
  .swiper-button-prev::after {
    display: none;
  }

  .swiper-button-next i,
  .swiper-button-prev i {
    font-size: 16px;
  }
  
  .reviews-section {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .single-review {
        border-bottom: 1px solid #ddd;
        padding: 20px 0;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .customer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .profile-icon {
        font-size: 40px;
        color: #888;
    }
    
    .customer-name {
        font-weight: bold;
    }
    
    .review-rating {
        color: #f5c518;
        font-size: 14px;
    }
    
    .review-meta {
        font-size: 12px;
        color: #555;
        margin: 10px 0;
    }
    
    .review-text {
        margin: 10px 0;
        font-size: 14px;
        color: #333;
    }
    
    .review-images img {
        width: 60px;
        height: 60px;
        margin-right: 5px;
        border-radius: 5px;
    }
    
    .review-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 12px;
        margin-top: 10px;
    }
    
    .helpful-btn,
    .report-btn {
        background: none;
        border: none;
        color: #0073e6;
        cursor: pointer;
    }
    
    .helpful-btn:hover,
    .report-btn:hover {
        text-decoration: underline;
    }
    
    .review-form {
        margin-top: 40px;
    }
    
    .review-form h4 {
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .review-form textarea {
        width: 100%;
        min-height: 100px;
        margin-bottom: 10px;
        padding: 10px;
        resize: vertical;
    }
    
    .review-form select {
        width: 100%;
        margin-bottom: 10px;
        padding: 10px;
    }
    
    .review-form button {
        padding: 10px 20px;
        background-color: #161394;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    
    .review-form button:hover {
        background-color: #005bb5;
    }
    
    .review-form a {
        color: #0073e6;
        text-decoration: none;
    }
    
    .review-form a:hover {
        text-decoration: underline;
    }
</style>


<div class="reviews-section">
    <h2>Top reviews from India</h2>

    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="single-review">
                <div class="review-header">
                    <div class="customer-info">
                        <i class="fas fa-user-circle profile-icon"></i>
                        <div class="customer-name"><?= htmlspecialchars($review['full_name']) ?></div>
                    </div>
                    <div class="review-rating">
                        <?= str_repeat('★', $review['rating']) ?>
                        <?= str_repeat('☆', 5 - $review['rating']) ?>
                        <!-- <span><?= $review['rating'] ?> out of 5 stars</span> -->
                    </div>
                </div>
                <div class="review-meta">
                    <span>Reviewed in India on <?= date('d F Y', strtotime($review['created_at'])) ?></span>
                    <div>Verified Buyer</div>
                </div>
                <div class="review-text">
                    <?= nl2br(htmlspecialchars($review['review'])) ?>
                </div>
                <div class="review-images">
                    <!-- <img src="https://via.placeholder.com/60" alt="Customer image">
                    <img src="https://via.placeholder.com/60" alt="Customer image"> -->
                </div>
                <div class="review-actions">
                    <!-- <button class="helpful-btn">Helpful</button>
                    <button class="report-btn">Report</button> -->
                    <span>One person found this helpful</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews yet. Be the first to review this product!</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="review-form">
        <h4>Write a Review</h4>
        <form method="post" id="reviewForm" action="submit_review.php">
            <input type="hidden" name="product_id" value="<?= $product['p_id'] ?>">
            <textarea name="review" placeholder="Write your review here..." required></textarea>
            <select name="rating" required>
                <option value="">Select rating</option>
                <option value="5">★★★★★</option>
                <option value="4">★★★★</option>
                <option value="3">★★★</option>
                <option value="2">★★</option>
                <option value="1">★</option>
            </select>
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>
    <?php else: ?>
        <p><a href="<?= $base_url; ?>login.php">Login</a> to write a review.</p>
    <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
      const reviewForm = document.getElementById('reviewForm');
      if (!reviewForm) return;

      reviewForm.addEventListener('submit', function(e) {
          e.preventDefault(); // prevent normal submission

          const formData = new FormData(reviewForm);

          fetch('/submit_review.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Review submitted successfully!');
                  reviewForm.reset();

                  // Optionally, reload or fetch the updated reviews list here
                  window.location.reload(); // or call a function to refresh reviews dynamically
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while submitting your review.');
          });
      });
  });
</script>


<!-- faQ start -->
<section class="container faq-wrapper">
  <h2 class=" container faq-title">FAQs</h2>

  <div class="faq">
    <input type="checkbox" id="faq1">
    <label for="faq1">What are its benefits?</label>
    <div class="faq-content">
      <p>This gemstone helps with mental peace, energy balance, and attracts positive vibes.</p>
    </div>
  </div>

  <div class="faq">
    <input type="checkbox" id="faq2">
    <label for="faq2">How natural and authentic is this gemstone?</label>
    <div class="faq-content">
      <p>All gemstones are certified by reputed labs and 100% natural.</p>
    </div>
  </div>

  <div class="faq">
    <input type="checkbox" id="faq3">
    <label for="faq3">What is the return policy and delivery duration?</label>
    <div class="faq-content">
      <p>You can return within 7 days. Delivery takes 3–5 business days.</p>
    </div>
  </div>

  <div class="faq">
    <input type="checkbox" id="faq4">
    <label for="faq4">How is this energized and how can I wear it?</label>
    <div class="faq-content">
      <p>It’s energized with Vedic rituals and can be worn on wrist or neck.</p>
    </div>
  </div>
</section>
<!-- faQ end -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    syncHomepageButtons();
});
</script>


<?php
include('include/footer.php');

?>