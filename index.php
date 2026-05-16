<?php
include("admin/inc/config.php");
session_start();
$base_url = BASE_URL;

// Restore session from cookie if user_id is not set
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  $cookie_user_id = intval($_COOKIE['user_id']);
  $query = "SELECT * FROM tbl_user WHERE id = '$cookie_user_id' AND status='Active' LIMIT 1";
  $result = mysqli_query($con, $query);
  if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['email_id'] = $row['email'];
    $_SESSION['user_name'] = $row['full_name'];
    $_SESSION['phone'] = $row['phone'];
  }
}


$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? '';

// Assign guest temp_user_id if needed
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
function slugify($string)
{
  $slug = strtolower(trim($string));
  $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug); // replace non-alphanumeric
  $slug = preg_replace('/-+/', '-', $slug); // collapse multiple hyphens
  return trim($slug, '-');
}


include('include/header.php');
?>




<?php
function renderProductGrid($con, $query, $cart_items, $base_url, $ribbon = '')
{
  $result = mysqli_query($con, $query);

  if (!$result) {
    echo '<div class="col-12 text-center text-danger py-5">
            <strong>Database Error:</strong> ' . mysqli_error($con) . '<br>
            <small>If you see this on the live site, please check your database connection or SQL mode.</small>
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
    $p_old_price = (float) $row['p_old_price'];
    $p_photo    = $row['photo'] ?: $row['p_featured_photo'];
    $p_color    = htmlspecialchars($row['color']);
    $p_weight   = htmlspecialchars($row['p_weight']);
    $p_unit     = htmlspecialchars($row['p_unit']);
    $p_gst_per  = (float) $row['p_gst'];
    $p_gst_amt  = round(($p_price * $p_gst_per / 100), 2);
    $p_actual_price = $p_price + $p_gst_amt;
    $p_actual_old_price = $p_old_price + round(($p_old_price * $p_gst_per / 100), 2);

    $p_sku      = htmlspecialchars($row['p_sku']);
    $stock_qty  = (int) $row['in_stoke'];

    $in_cart    = $cart_items[$p_id] ?? null;
    $cart_qty   = $in_cart['qty'] ?? 0;
?>
    <div class="col-md-6 col-lg-3">
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

        <div class="p-3 text-center">
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




<style>
  @media (max-width:768px) {
    .hero-content {
      margin-top: -50px !important;
    }

  }

  /* Hero Section */
  .hero {
    background: url('assets/images/banner') center/cover no-repeat;
    color: #fff;
    padding: 0;
    position: relative;
    overflow: hidden;
    min-height: 400px;
    width: 100%;
    height: 95vh;
  }

  .hero-overlay-structure {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    z-index: 2;
    padding: 0 5vw;
    box-sizing: border-box;
  }

  .hero-content {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    text-align: left;
    padding: 0 40px 0 0;
  }

  .hero-slider {
    flex: 0 0 420px;
    max-width: 420px;
    min-width: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    height: 420px;
    opacity: 0;
    transform: translateX(100vw);
    transition: none;
  }

  @media (max-width:768px) {
    .hero-slider {
      margin-top: -50px !important;
    }
  }

  .hero-slider.animate-in {
    animation: heroSliderIn 1.1s cubic-bezier(0.6, 0.1, 0.3, 1) 0.2s forwards;
  }

  @keyframes heroSliderIn {
    from {
      opacity: 0;
      transform: translateX(100vw);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .hero-slider img {
    width: 400px;
    height: 400px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.18);
    transition: opacity 0.5s;
    background: #fff;
    border: 8px solid #fff;
    display: block;
    animation: rotateCircle 80s linear infinite;
  }


  @keyframes rotateCircle {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .hero-content p {
    color: #fff;
    font-size: 1.2rem;
    margin-bottom: 28px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  }

  .cta-btn {
    background: #fdc134;
    color: #222;
    padding: 18px 44px;
    border: none;
    border-radius: 40px;
    font-size: 20px;
    font-weight: bold;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(.4, 2, .3, 1);
    position: relative;
    z-index: 1;
    box-shadow: 0 6px 24px rgba(253, 193, 52, 0.18);
  }

  .cta-btn:hover {
    background: #222;
    color: #fff;
    box-shadow: 0 8px 32px rgba(34, 34, 34, 0.18);
    transform: translateY(-2px) scale(1.04);
  }

  @media (max-width: 900px) {
    .hero-overlay-structure {
      flex-direction: column-reverse;
      align-items: center;
      justify-content: center;
      padding: 0 2vw;
      height: 100%;
    }

    .hero-slider {
      height: 250px;
      max-width: 100vw;
      margin-top: 8px;
    }

    .hero-slider img {
      width: 220px;
      height: 220px;
    }

    .hero-content {
      align-items: center;
      text-align: center;
      padding: 0;
      margin-bottom: 10px;
    }

    .hero-content p {
      font-size: 1rem;
      margin-bottom: 18px;
    }

    .cta-btn {
      font-size: 1rem;
      padding: 12px 28px;
    }
  }

  /* About Us Section */
  .about-section {
    max-width: 1200px;
    margin: 60px auto 0 auto;
    padding: 40px 20px;
    background: #fff;

  }

  .about-row {
    display: flex;
    align-items: center;
    gap: 48px;
    flex-wrap: wrap;
  }

  .about-col {
    flex: 1 1 0;
  }

  .about-img-col {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .about-rotating-circle {
    width: 320px;
    height: 320px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: rotateCircle 18s linear infinite;
    box-shadow: 0 4px 24px rgba(24, 99, 171, 0.10);
    margin: 0 auto;
  }

  .about-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: none;
    border-radius: 50%;
    background: #fff;
    box-shadow: none;
  }

  .about-content-col {
    padding: 0 10px;
  }

  .about-heading {
    font-size: 2.2rem;
    color: #1863ab;
    margin-bottom: 18px;
    font-weight: bold;
  }

  .about-text {
    font-size: 1.18rem;
    color: #333;
    line-height: 1.7;
  }

  .about-bg-rotating-circle {
    position: absolute;
    left: 45%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: url('<?php echo $base_url; ?>assets/images/rotating.jpeg') center/cover no-repeat #fff;
    z-index: 1;
    opacity: 0.25;
    animation: rotateCircle 18s linear infinite;
    box-shadow: 0 4px 24px rgba(24, 99, 171, 0.10);
  }

  @media (max-width:768px) {
    .about-bg-rotating-circle {
      display: none;
    }
  }

  .about-img-static {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 400px;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(253, 193, 52, 0.13);
    border: 6px solid #fdc134;
    background: #fff;
    object-fit: cover;
    display: block;
    margin: 0 auto;
  }

  @media (max-width: 900px) {
    .about-row {
      flex-direction: column;
      gap: 28px;
    }

    .about-img {
      max-width: 90vw;
    }

    .about-rotating-circle {
      width: 140px;
      height: 140px;
    }

    .about-bg-rotating-circle {
      width: 140px;
      height: 140px;
    }

    .about-img-static {
      max-width: 90vw;
    }

    .about-heading {
      font-size: 1.5rem;
    }

    .about-text {
      font-size: 1rem;
    }
  }

  .about-readmore-btn {
    display: inline-block;
    margin-top: 24px;
    padding: 13px 38px;
    background: #fdc134;
    color: #222;
    font-size: 1.08rem;
    font-weight: bold;
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 0 4px 18px rgba(253, 193, 52, 0.13);
    border: none;
    transition: all 0.25s cubic-bezier(.4, 2, .3, 1);
  }

  .about-readmore-btn:hover {
    background: #1863ab;
    color: #fff;
    box-shadow: 0 6px 24px rgba(24, 99, 171, 0.13);
  }

  @media (max-width: 900px) {
    .about-readmore-btn {
      display: block;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }
  }

  .about-bullets {
    margin: 22px 0 0 0;
    padding-left: 22px;
  }

  .about-bullets li {
    font-size: 1.08rem;
    color: #333;
    margin-bottom: 10px;
    position: relative;
    line-height: 1.6;
  }

  .about-bullets li::marker {
    color: #fdc134;
    font-size: 1.2em;
  }

  /* Default: Desktop */
  /* Desktop styles */
  .banner-img {
    width: 100% !important;
    height: 80vh !important;
    object-fit: cover !important;
    display: block !important;
  }

  /* Mobile: reset styles */
  @media (max-width: 767px) {
    .banner-img {
      width: auto;
      height: auto;
      max-width: 100%;
      object-fit: contain;
      margin: 0 auto;
      display: block;
    }

    .hero-banner-img {
      width: 390px !important;
      height: 450px !important;
      max-width: 100vw !important;
      object-fit: contain !important;
      margin-left: auto !important;
      margin-right: auto !important;
      display: block !important;
    }
  }

  @media (max-width: 576px) {
    .product-card a {
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .product-card img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
  }
</style>
<!-- Hero Section -->
<div id="mainHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" style="margin-bottom: 0;">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">

    <!-- Slide 1 -->
    <div class="carousel-item active">
      <picture>
        <source media="(max-width: 767px)" srcset="assets/images/SSFbannerPhone.png">
        <img src="assets/images/SSF-Banner.png" class="d-block w-100 hero-banner-img" style="height: 80vh; object-fit: cover;" alt="Banner 1">
      </picture>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-item">
      <picture>
        <source media="(max-width: 767px)" srcset="assets/images/mob.png">
        <img src="assets/images/bann1.png" class="d-block w-100 hero-banner-img" style="height: 80vh; object-fit: cover;" alt="Banner 2">
      </picture>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-item">
      <picture>
        <source media="(max-width: 767px)" srcset="assets/images/mobile-banner2.png">
        <img src="assets/images/banner-home1.png" class="d-block w-100 hero-banner-img" style="height: 80vh; object-fit: cover;" alt="Banner 3">
      </picture>
    </div>

  </div>

  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- hero section end-->

<!-- Our Products -->
<div class="container py-5">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">Our
    </span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;"> Products</h4>
  </div>
  <div class="row g-4">
    <?php
    $normal_query = "
    SELECT p.p_id, p.p_name, p.p_featured_photo, p.p_gst, p.p_unit,
           pr.p_current_price, pr.p_old_price, pr.p_sku, pr.color, pr.photo,
           pr.in_stoke, pr.p_qty, pr.p_weight
    FROM tbl_product p
    JOIN tbl_product_price pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1 AND pr.in_stoke > 0
    GROUP BY p.p_id
    ORDER BY p.p_id DESC
    LIMIT 8
";
    renderProductGrid($con, $normal_query, $cart_items, $base_url);
    ?>
  </div>

</div>

<section class="container-fluid about-section " style="margin-bottom: 90px; margin-top:-40px">
  <div class="container">
    <div class="about-row">


      <div class="about-col about-img-col" style="position:relative;">
        <div class="about-bg-rotating-circle"></div>
        <img src="<?php echo $base_url; ?>assets/images/p3.jpeg"
          alt="About Us"
          class="about-img about-img-static">
      </div>
      <div class="about-col about-content-col" style="margin-top:60px !important">
        <span style="color:#e97d1f;">About Us </span>
        <h2 class="about-heading text-dark">Who We Are</h2>
        <!-- Filter Tabs -->
        <div class="mb-3 d-flex gap-3 flex-wrap">
          <button class="btn btn-sm btn-outline-warning active-tab" onclick="showTabContent('mission', this)">Our Mission</button>
          <button class="btn btn-sm btn-outline-warning" onclick="showTabContent('vision', this)">Our Vision</button>
          <button class="btn btn-sm btn-outline-warning" onclick="showTabContent('value', this)">Our Value</button>
        </div>
        <!-- Filter Content -->
        <div id="mission" class="about-text tab-content-box">
          <p style="font-size:16px;">We create modern, meaningful products that blend science and spirituality to inspire, heal, and empower your lifestyle.</p>
          <ul class="about-bullets mt-3">
            <li style="font-size:16px;">Deliver premium, meaningful, and spiritual products</li>
            <li style="font-size:16px;">Blend modern science with ancient wisdom</li>
            <li style="font-size:16px;">Inspire, heal, and empower everyday lives</li>
            <li style="font-size:16px;">Offer transformative lifestyle solutions</li>
          </ul>
        </div>
        <div id="vision" class="about-text tab-content-box d-none">
          <p style="font-size:16px;">Our vision is to be a global leader in holistic products that bridge tradition with innovation, enhancing well-being in every home we touch.</p>
          <ul class="about-bullets mt-3">
            <li style="font-size:16px;">To be a global leader in holistic living solutions</li>
            <li style="font-size:16px;">Bridge tradition with innovation for well-being</li>
            <li style="font-size:16px;">Reach every home with positive energy products</li>
            <li style="font-size:16px;">Build a community focused on mindful living</li>
          </ul>
        </div>
        <div id="value" class="about-text tab-content-box d-none">
          <p style="font-size:16px;">We value authenticity, compassion, and excellence — delivering products with purpose and creating positive energy in all we do.</p>
          <ul class="about-bullets mt-3">
            <li style="font-size:16px;">Authenticity in every product and process</li>
            <li style="font-size:16px;">Commitment to quality and transparency</li>
            <li style="font-size:16px;">Compassion and customer care at the core</li>
            <li style="font-size:16px;">Continuous innovation and ethical practices</li>
          </ul>
        </div>
        <a href="about-us.php" class="about-readmore-btn">Read More</a>
      </div>
    </div>
  </div>
</section>

<script>
  function showTabContent(tabId, el) {
    document.querySelectorAll('.tab-content-box').forEach(box => {
      box.classList.add('d-none');
    });

    document.querySelectorAll('.active-tab').forEach(btn => {
      btn.classList.remove('active-tab');
    });

    document.getElementById(tabId).classList.remove('d-none');
    el.classList.add('active-tab');
  }
</script>

<style>
  .active-tab {
    background-color: #fdbf2c !important;
    color: white !important;
    border-color: #fdbf2c !important;
  }
</style>


<!-- Banner Section Start -->
<section class="banner-section-1 py-3 container-fluid"
  style="background: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('assets/images/inner banner.png');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                height: 500px;
                position: relative;">

  <!-- ✅ Text on Banner -->
  <div class="position-absolute top-50 start-50 translate-middle text-center text-light w-100">
    <h5 class="fw-light mb-2" style="color: #fdc134; font-weight: bold !important;">Biggest Spiritual Sale of the Season!</h5>
    <h4 class="fw-bold mb-0 fs-1 fs-md-2 fs-sm-3 " style="color: #fff;  font-weight: bold;">Up to 50% OFF on Crystals, Healing Kits & More</h4>
  </div>

</section>
<!-- Banner Section End -->


<!--Trending products -->
<div class="container py-5">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">Our</span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;">Trending Products</h4>
  </div>
  <div class="row g-4">
    <?php
    $trend_query = "
    SELECT p.p_id, p.p_name, p.p_featured_photo, p.p_gst, p.p_unit,
           pr.p_current_price, pr.p_old_price, pr.p_sku, pr.color, pr.photo,
           pr.in_stoke, pr.p_qty, pr.p_weight
    FROM tbl_product p
    JOIN tbl_product_price pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1 AND p.p_is_trending = 1 AND pr.in_stoke > 0
    GROUP BY p.p_id
    ORDER BY p.p_id DESC
    LIMIT 8
";
    renderProductGrid($con, $trend_query, $cart_items, $base_url, "Trending");
    ?>
  </div>
</div>




<!-- Banner Section Start -->
<section class="banner-section">
  <div class="container-fluid-lg mt-5">
    <div class="row gy-lg-0 gy-3">
      <!-- Card 1 -->
      <div class="col-lg-6">
        <div class="banner-contain-3 hover-effect position-relative" style="height: 300px; overflow: hidden;">

          <!-- Image as background -->
          <img src="assets\images\offer-banner1.png"
            class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; z-index: 1;" alt="">
          <div class="banner-detail banner-detail-2 text-dark p-center-left w-75 banner-p-sm position-relative mend-auto"
            style="z-index: 2;">
            <div class="p-3">
              <h2 class="text-great fw-normal text-danger"></h2>
              <h3 class="mb-1 fw-bold"></h3>
              <h4 class="text-content"></h4>
              <a href="product-circle.php"> <button class="btn btn-md theme-bg-color text-white mt-sm-3 mt-1 fw-bold mend-auto"></button> </a>
            </div>
          </div>
        </div>
      </div>
      <!-- Card 2 -->
      <div class="col-lg-6">
        <div class="banner-contain-3 hover-effect position-relative" style="height: 300px; overflow: hidden;">

          <!-- Image -->
          <img src="assets\images\offer-banner2.png"
            class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; z-index: 1;" alt="">

          <!-- Overlay Content -->
          <div class="banner-detail banner-detail-2 text-dark p-center-left w-75 banner-p-sm position-relative mend-auto"
            style="z-index: 2;">
            <div class="p-3">
              <h2 class="text-great fw-normal text-danger"></h2>
              <h3 class="mb-1 fw-bold"></h3>
              <h4 class="text-content"></h4>
              <button class="btn btn-md theme-bg-color text-white mt-sm-3 mt-1 fw-bold mend-auto"
                onclick="location.href = 'shop-left-sidebar.html';"></button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- Banner Section Start -->
<section class="banner-section-1 py-3 container-fluid"
  style="background: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('assets/images/inner-banner1.png');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                height: 700px;
                position: relative; margin-top: 100px !important;">

  <!-- ✅ Text on Banner -->
  <div class="position-absolute top-50 start-50 translate-middle text-center text-light w-100">
    <h5 class="fw-light mb-2" style="color: #fdc134; font-weight: bold !important;">Biggest Healing Sale of the Season!</h5>
    <h4 class="text-banner fw-bold mb-0  fs-1 fs-md-2 fs-sm-3 " style="color: #fff; font-weight: bold;">Up to 50% OFF on Spiritual Bracelets, <br> Vision Healing Kits & More</h4>
  </div>

</section>
<!-- Banner Section End -->


<!-- Why choose our products -->
<section class="container my-5">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">why</span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;">Choose Our Products</h4>
    <p class="text-muted" style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">We offer premium spiritual and healing products that transform your life with authentic materials and positive energy.</p>
  </div>

  <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">

    <!-- CARD 1 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-gem"></i>
        </div>
        <h5 class="fw-bold mb-2">Premium Quality</h5>
        <p>All our products are crafted with authentic materials and genuine stones, ensuring the highest quality and effectiveness.</p>
      </div>
    </div>

    <!-- CARD 2 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-shield-check"></i>
        </div>
        <h5 class="fw-bold mb-2">Authentic & Certified</h5>
        <p>Every product comes with certification and is sourced from trusted suppliers to guarantee authenticity and purity.</p>
      </div>
    </div>

    <!-- CARD 3 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-truck"></i>
        </div>
        <h5 class="fw-bold mb-2">Fast & Secure Delivery</h5>
        <p>We ensure quick and safe delivery across India with proper packaging to protect your precious spiritual items.</p>
      </div>
    </div>

    <!-- CARD 4 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-heart"></i>
        </div>
        <h5 class="fw-bold mb-2">Positive Energy</h5>
        <p>Our products are designed to bring positive energy, healing, and transformation to your life and surroundings.</p>
      </div>
    </div>

    <!-- CARD 5 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-headset"></i>
        </div>
        <h5 class="fw-bold mb-2">Expert Support</h5>
        <p>Get guidance from our spiritual experts on choosing the right products for your specific needs and goals.</p>
      </div>
    </div>

    <!-- CARD 6 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-award"></i>
        </div>
        <h5 class="fw-bold mb-2">Trusted by Thousands</h5>
        <p>Join thousands of satisfied customers who have experienced positive changes through our spiritual products.</p>
      </div>
    </div>

  </div>
</section>

<!-- STYLES -->
<style>
  .benefit-card {
    background-color: #fff;
    border: 2px solid #fff4b3;
    border-radius: 15px;
    min-height: 420px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem !important;
  }

  .benefit-card:hover {
    border-color: #fdc134;
    background-color: #fdc134;
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
  }

  .benefit-card:hover h5,
  .benefit-card:hover p {
    color: #fff !important;
  }

  .benefit-card:hover .icon-wrap {
    background-color: #161392;
  }

  .benefit-card:hover .icon-wrap i {
    color: #fff;
  }

  .icon-wrap {
    width: 80px;
    height: 80px;
    background-color: #fda600;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 12px rgba(253, 166, 0, 0.4);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem !important;
  }

  .icon-wrap i {
    font-size: 36px;
    color: #161392;
    transition: all 0.3s ease;
  }

  .benefit-card h5 {
    color: #000;
    transition: all 0.3s ease;
    font-size: 1.3rem;
    margin-bottom: 1rem;
  }

  .benefit-card p {
    font-size: 16px;
    color: #555;
    padding: 0 5px;
    transition: all 0.3s ease;
    line-height: 1.6;
  }
</style>


<!-- ✅ Testimonial Slider Section -->
<div class="testimonial-section py-5" style="
  background: url('assets/images/banner4.png') center center / cover no-repeat fixed;
  min-height: 400px;
">

  <div class="container position-relative">
    <div class="text-center mb-5">
      <h4 class="fw-bold" style="font-size:40px; color: #fff;">Testimonials</h4>
    </div>

    <div class="swiper testimonialSwiper">
      <div class="swiper-wrapper">
        <!-- ✅ Slide 2 -->
        <div class="swiper-slide mt-4">
          <div class="text-center">
            <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
              <img src="assets/images/ankita.jpg"
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
              <img src="assets/images/suchita.jpg"
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

        <!-- ✅ Slide 2 -->
        <div class="swiper-slide mt-4">
          <div class="text-center">
            <div class="bg-white p-3" style="border-radius: 30px; max-width: 280px; margin: auto;">
              <img src="assets/images/ankita.jpg"
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
              <img src="assets/images/suchita.jpg"
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
              <img src="assets/images/ankit.jpg" class="img-fluid rounded mb-3"
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

<!-- our products -->
<div class="container py-5">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">Our</span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;"> Best Sellers</h4>

  </div>
  <div class="row g-4">
    <?php
    $best_query = "
    SELECT p.p_id, p.p_name, p.p_featured_photo, p.p_gst, p.p_unit,
           pr.p_current_price, pr.p_old_price, pr.p_sku, pr.color, pr.photo,
           pr.in_stoke, pr.p_qty, pr.p_weight,
           SUM(o.no_of_item) AS sold_qty
    FROM tbl_order o
    JOIN tbl_product p ON o.p_id = p.p_id
    JOIN (SELECT * FROM tbl_product_price GROUP BY p_id) pr ON p.p_id = pr.p_id
    WHERE p.p_is_active = 1 AND pr.in_stoke > 0
    GROUP BY p.p_id
    ORDER BY sold_qty DESC
    LIMIT 8
";
    renderProductGrid($con, $best_query, $cart_items, $base_url, "Best Seller");
    ?>
  </div>


</div>




<!-- contact section start -->
<section class="contact-box-section">
  <div class="container-fluid-lg">
    <div class="row g-lg-5 g-3">
      <div class="col-lg-6">
        <div class="left-sidebar-box">
          <div class="row">
            <div class="col-xl-12">
              <div class="contact-image">
                <img src="assets/images/contact.png"
                  class="img-fluid blur-up lazyloaded" alt="">
              </div>
            </div>
            <div class="col-xl-12">
              <div class="contact-title">
                <h3>Get In Touch</h3>
              </div>

              <div class="contact-detail">
                <div class="row g-4">
                  <div class="col-xxl-6 col-lg-12 col-sm-6">
                    <div class="contact-detail-box">
                      <div class="contact-icon" style="background-color: #fdc134;">
                        <i class="fa-solid fa-phone"></i>
                      </div>
                      <div class="contact-detail-title">
                        <h4>Phone</h4>
                      </div>

                      <div class="contact-detail-contain">
                        <p>+91-9716517463</p>
                      </div>
                    </div>
                  </div>

                  <div class="col-xxl-6 col-lg-12 col-sm-6">
                    <div class="contact-detail-box">
                      <div class="contact-icon" style="background-color: #fdc134;">
                        <i class="fa-solid fa-envelope"></i>
                      </div>
                      <div class="contact-detail-title">
                        <h4>Email</h4>
                      </div>

                      <div class="contact-detail-contain">
                        <p>secondsightfoundation@gmail.com</p>
                      </div>
                    </div>
                  </div>



                  <div class="col-xxl-6 col-lg-12 col-sm-6">
                    <div class="contact-detail-box">
                      <div class="contact-icon" style="background-color: #fdc134;">
                        <i class="fa-solid fa-building"></i>
                      </div>
                      <div class="contact-detail-title">
                        <h4>Our Location</h4>
                      </div>

                      <div class="contact-detail-contain">
                        <p>AE-10, Ground Floor, Tagore Garden, Near Tagore Garden Metro Station Gate Number 1 Exit, New Delhi, Delhi 110027</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="title d-xxl-none d-block">
          <h2>Contact Us</h2>
        </div>
        <form action="submit.php" method="post">
          <div class="right-sidebar-box">
            <div class="row">
              <div class="col-xxl-6 col-lg-12 col-sm-6">
                <div class="mb-md-4 mb-3 custom-form">
                  <label class="form-label">First Name</label>
                  <div class="custom-input">
                    <input type="text" class="form-control" name="first_name"
                      placeholder="Enter First Name" required>
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>
              </div>

              <div class="col-xxl-6 col-lg-12 col-sm-6">
                <div class="mb-md-4 mb-3 custom-form">
                  <label class="form-label">Last Name</label>
                  <div class="custom-input">
                    <input type="text" class="form-control" name="last_name"
                      placeholder="Enter Last Name" required>
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>
              </div>

              <div class="col-xxl-6 col-lg-12 col-sm-6">
                <div class="mb-md-4 mb-3 custom-form">
                  <label class="form-label">Email Address</label>
                  <div class="custom-input">
                    <input type="email" class="form-control" name="email"
                      placeholder="Enter Email Address" required>
                    <i class="fa-solid fa-envelope"></i>
                  </div>
                </div>
              </div>

              <div class="col-xxl-6 col-lg-12 col-sm-6">
                <div class="mb-md-4 mb-3 custom-form">
                  <label class="form-label">Phone Number</label>
                  <div class="custom-input">
                    <input type="tel" class="form-control" name="phone"
                      placeholder="Enter Your Phone Number" maxlength="10"
                      oninput="if (this.value.length > this.maxLength) this.value=this.value.slice(0,this.maxLength);" required>
                    <i class="fa-solid fa-mobile-screen-button"></i>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="mb-md-4 mb-3 custom-form">
                  <label class="form-label">Message</label>
                  <div class="custom-textarea">
                    <textarea class="form-control" name="message"
                      placeholder="Enter Your Message" rows="6" required></textarea>
                    <i class="fa-solid fa-message"></i>
                  </div>
                </div>
              </div>
            </div>
            <button class="btn bg-dark text-light btn-md fw-bold ms-auto" name="send_mail">Send Message</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<!-- Contact Box Section End -->

<?php
include('include/footer.php');

?>