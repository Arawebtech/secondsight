<?php
require_once("admin/inc/config.php");
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


if(isset($_SESSION['user_id']))
    $user_id = $_SESSION['user_id'];
else if(isset($_SESSION['temp_user_id']))
    $user_id = $_SESSION['temp_user_id'];

// --- Referral Tracking ---
if (!isset($_SESSION['product_ref']) || !is_array($_SESSION['product_ref'])) {
    $_SESSION['product_ref'] = [];
}

if (isset($p_id)) {
    // If we are on a product page, check for 'ref'
    if (isset($_GET['ref'])) {
        $ref_val = $_GET['ref'];
        $partner_id = 0;

        // Check if ref is numeric (ID) or string (ref_code)
        if (is_numeric($ref_val)) {
            $stmt_ref = $pdo->prepare("SELECT id FROM tbl_user WHERE id = ? AND status = 'Active' LIMIT 1");
            $stmt_ref->execute([intval($ref_val)]);
        } else {
            $stmt_ref = $pdo->prepare("SELECT id FROM tbl_user WHERE ref_code = ? AND status = 'Active' LIMIT 1");
            $stmt_ref->execute([$ref_val]);
        }

        if ($stmt_ref->rowCount() > 0) {
            $partner_id = $stmt_ref->fetchColumn();
            $_SESSION['product_ref'][$p_id] = $partner_id;
            // No cookie for strict product-based URL referral
        }
    } else {
        // STRICTOR: If on product page WITHOUT ref, clear any existing referral for this product
        // This handles the case where they previously clicked a link but now came back directly.
        unset($_SESSION['product_ref'][$p_id]);
    }
} else {
    // General referral (homepage/other pages)
    if (isset($_GET['ref'])) {
        $ref_val = $_GET['ref'];
        if (is_numeric($ref_val)) {
            $stmt_ref = $pdo->prepare("SELECT id FROM tbl_user WHERE id = ? AND status = 'Active' LIMIT 1");
            $stmt_ref->execute([intval($ref_val)]);
        } else {
            $stmt_ref = $pdo->prepare("SELECT id FROM tbl_user WHERE ref_code = ? AND status = 'Active' LIMIT 1");
            $stmt_ref->execute([$ref_val]);
        }
        if ($stmt_ref->rowCount() > 0) {
            $_SESSION['ref_user_id'] = $stmt_ref->fetchColumn();
        }
    }
}
    
    $taglines = [];

try {
    $statement = $pdo->prepare("SELECT tagline FROM tbl_tagline ORDER BY id ASC");
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $taglines[] = $row['tagline'];
    }
} catch (Exception $e) {
    $taglines = ["Welcome to our website!"]; // fallback
}

// ✅ Calculate total cart items in PHP (to avoid '0' flicker)
$total_cart_items = 0;
if ($user_id) {
    if ($con) {
        $q_count = "SELECT SUM(no_of_item) as total FROM tbl_cart WHERE user_id = '$user_id' AND is_ordered = '0'";
        $res_count = mysqli_query($con, $q_count);
        if ($res_count) {
            $row_count = mysqli_fetch_assoc($res_count);
            $total_cart_items = (int)($row_count['total'] ?? 0);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
// ===== Set defaults =====
$head_title       = $page_title ?? "Second Sight Foundation";
$head_description = $page_description ?? "Second Sight Foundation";
$head_keywords    = $page_keywords ?? "Second Sight Foundation";
$head_author      = "Second Sight Foundation";

// Optional: product-specific variables for OG/Twitter
$product_og_title = $product_name ?? null;
$product_og_desc  = $share_text ?? $product_name ?? null;
$product_og_url   = $product_url ?? null;
$product_og_img = !empty($product_img) ? $product_img . '?v=' . time() : null;

?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($head_description) ?>">
<meta name="keywords" content="<?= htmlspecialchars($head_keywords) ?>">
<meta name="author" content="<?= htmlspecialchars($head_author) ?>">
<link rel="icon" href="<?=$base_url;?>assets/images/logo-fav.png" type="image/png">
<title><?= htmlspecialchars($head_title) ?></title>

<?php if($product_og_title && $product_og_url && $product_og_img): ?>
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="<?= htmlspecialchars($product_og_title) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($product_og_desc) ?>" />
    <meta property="og:image" content="<?= htmlspecialchars($product_og_img) ?>" />
    <meta property="og:url" content="<?= htmlspecialchars($product_og_url) ?>" />
    <meta property="og:type" content="product" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($product_og_title) ?>" />
    <meta name="twitter:description" content="<?= htmlspecialchars($product_og_desc) ?>" />
    <meta name="twitter:image" content="<?= htmlspecialchars($product_og_img) ?>" />
<?php endif; ?>



  <!-- Dependency Styles -->
  <link rel="stylesheet" href="<?= $base_url; ?>libs/bootstrap/css/bootstrap.min.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/feather-font/css/iconfont.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/icomoon-font/css/icomoon.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/font-awesome/css/font-awesome.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/wpbingofont/css/wpbingofont.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/elegant-icons/css/elegant.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/slick/css/slick.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/slick/css/slick-theme.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>libs/mmenu/css/mmenu.min.css" type="text/css">

  <!-- Site Stylesheet -->
  <!-- <link rel="stylesheet" href="<?= $base_url; ?>assets/css/app.css" type="text/css">
  <link rel="stylesheet" href="<?= $base_url; ?>assets/css/responsive.css" type="text/css"> -->

  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
    rel="stylesheet" />

  <!-- Google font -->
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&amp;display=swap" rel="stylesheet">
  <!-- ✅ Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

  <!-- Font Awesome for Stars & Arrows -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
  <!-- bootstrap css -->
  <link id="rtl-link" rel="stylesheet" type="text/css" href="<?= $base_url; ?>assets/css/vendors/bootstrap.css">

  <!-- wow css -->
  <link rel="stylesheet" href="<?= $base_url; ?>assets/css/animate.min.css">

  <!-- Plugin CSS file with desired skin css -->
  <link rel="stylesheet" href="<?= $base_url; ?>assets/css/vendors/ion.rangeSlider.min.css">

  <!-- animation css -->
  <link rel="stylesheet" type="text/css" href="<?= $base_url; ?>assets/css/font-style.css">

  <!-- Template css -->
  <link id="color-link" rel="stylesheet" type="text/css" href="<?= $base_url; ?>assets/css/style.css">
  <!-- <link id="color-link" rel="stylesheet" type="text/css" href="/assets/css/header.css"> -->
  <link rel="stylesheet" href="<?= $base_url; ?>assets/css/custom.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= $base_url; ?>assets/css/main.css">
</head>
<style>
  /* === Desktop Specific Styles === */
  @media (min-width: 992px) {

    /* Logo size */
    .navbar-brand img {
      height: 75px !important;
    }

    /* Navbar link font size */
    .navbar-nav .nav-link {
      font-size: 18px !important;
    }

    /* Cart and other icon sizes */
    .navbar .fa-shopping-cart,
    .navbar .fa-user,
    .navbar .fa-heart,
    .navbar .fa-phone {
      font-size: 22px !important;
    }

    /* Login and Signup button font size */
    .navbar .btn {
      font-size: 16px !important;
      padding: 8px 20px !important;
    }
  }

  @media (max-width:768px) {
    .login {
      color: white;
      background: black;
    }

    .signup {
      color: white;
      background: black;
    }
  }
</style>

<body>

  <!--  Top Black Header -->
  <div class="top-header" style="background-color: black !important;">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
      <div class="rotating-text" style="color: white;"></div>
      <div class="right-icons d-flex align-items-center">
        <div class="dropdown">
          <button class="btn text-secondary btn-sm bg-transparent d-lg-block d-none border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://flagcdn.com/in.svg" alt="INR" width="20" class="mx-1"> INR
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">INR</a></li>
            <li><a class="dropdown-item" href="#">USD</a></li>
            <li><a class="dropdown-item" href="#">EUR</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>


    <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top" style="background-color: white; box-shadow: 0 2px 15px rgba(0,0,0,0.1); padding: 15px 0;">
    <div class="container d-flex align-items-center justify-content-between">

      <a href="<?= $base_url; ?>index.php" class="navbar-brand">
        <img src="<?= $base_url; ?>assets/images/header2.png" alt="Logo" style="height: 60px;">
      </a>
      
      <!-- Mobile Search Trigger -->
<div class="d-flex d-lg-none align-items-center ms-auto">
  <a href="#" id="searchToggleMobile" class="position-relative me-2" title="Search">
    <i class="fa fa-search fs-5 text-dark"></i>
  </a>
</div>

<!-- Mobile Search Popup -->
<div id="searchBoxMobile" class="search-popup-mobile shadow rounded">
  <button class="search-close" aria-label="Close">&times;</button>
  <form action="<?= $base_url; ?>search.php" method="get" class="d-flex">
    <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Search...">
    <button type="submit" class="btn btn-sm btn-gradient">
      <i class="fas fa-search"></i>
    </button>
  </form>
</div>

<style>
  /* Mobile search popup (hidden by default) */
  .search-popup-mobile {
    display: none;
  }

  @media (max-width: 991px) {
    .search-popup-mobile {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      padding: 20px;
      background: #fff;
      z-index: 1050;
      display: flex;
      flex-direction: column;
      justify-content: center;
      opacity: 0;
      transform: translateY(-100%);
      pointer-events: none;
      transition: all 0.25s ease-in-out;
    }

    .search-popup-mobile.show {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    /* Show close button only on mobile */
    .search-popup-mobile .search-close {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 24px;
      background: none;
      border: none;
      cursor: pointer;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchToggleMobile = document.getElementById('searchToggleMobile');
    const searchBoxMobile = document.getElementById('searchBoxMobile');
    const closeBtn = searchBoxMobile.querySelector('.search-close');

    if (searchToggleMobile && searchBoxMobile) {
      // Open/close only on mobile
      searchToggleMobile.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.innerWidth <= 991) {
          searchBoxMobile.classList.toggle('show');
          if (searchBoxMobile.classList.contains('show')) {
            setTimeout(() => {
              const input = searchBoxMobile.querySelector('input');
              if (input) input.focus();
            }, 150);
          }
        }
      });

      // Close button
      closeBtn.addEventListener('click', function() {
        searchBoxMobile.classList.remove('show');
      });

      // Close on ESC
      document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
          searchBoxMobile.classList.remove('show');
        }
      });
    }
  });
</script>

      <div class="d-lg-none d-flex align-items-center">
          <a href="#" onclick="openCartPopup(); return false;" class="position-relative me-2">
              <i class="fa fa-shopping-cart fs-5 text-dark"></i>
              <span id="cart-badge-count-mobile" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                  <?= $total_cart_items; ?>
              </span>
          </a>
          <button class="navbar-toggler border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
      </div>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <a href="<?= $base_url; ?>index.php" class="navbar-brand d-flex d-lg-none" style="margin-left:-20px;">
          <img src="<?= $base_url; ?>assets/images/header2.png" alt="Logo" style="height: 60px;">
        </a>
        <ul class="navbar-nav gap-4 mx-auto">
          <li class="nav-item"><a class="nav-link fw-semibold text-secondary " href="<?= $base_url; ?>index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-secondary " href="<?= $base_url; ?>about-us.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-secondary " href="<?= $base_url; ?>products.php">Shop</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-secondary " href="<?= $base_url; ?>contact-us.php">Contact</a></li>
        </ul>

        <!-- Search input -->
        <form class="d-flex position-relative me-2" role="search" action="<?= $base_url; ?>search.php" method="get" style="width:200px;">
          <input 
            type="search" 
            class="form-control ps-4" 
            name="q" 
            placeholder="Search..." 
            aria-label="Search"
          />
          <button 
            type="submit" 
            class="btn position-absolute top-0 end-0 h-100 px-2" 
            style="border:none; background:transparent;">
            <i class="fa fa-search text-dark"></i>
          </button>
        </form>


        <div class="d-none d-lg-flex align-items-center">
          <a href="#" onclick="openCartPopup(); return false;" class="position-relative me-3">
            <i class="fa fa-shopping-cart fs-4 text-dark"></i>
            <span id="cart-badge-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                <?= $total_cart_items; ?>
            </span>
          </a>
          <!-- Custom Gradient Button Style -->
          <script>
            // Highlight current link
            const currentPage = window.location.pathname.split("/").pop();
            const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

            navLinks.forEach(link => {
              const href = link.getAttribute("href");
              if (href === currentPage || (href === "index.php" && currentPage === "")) {
                link.classList.add("active");
              }
            });
          </script>

          <style>
            .nav-link.active {
              color: #fdc134 !important;
              border-bottom: 2px solid #fdc134;
            }

            @media (max-width: 768px) {
              .nav-link.active {
                color: #9FA6B2 !important;
                border-bottom: none;
              }
            }

            .btn-gradient {
              background: linear-gradient(135deg, #fdc134, #fcb813);
              color: #000 !important;
              border: none;
              padding: 14px 36px;
              font-size: 18px;
              border-radius: 8px;
              transition: 0.3s ease;
            }

            .btn-gradient:hover {
              opacity: 0.9;
            }

            .btn-gradient-outline {
              background: transparent;
              border: 2px solid #fdc134;
              color: #fdc134;
              padding: 14px 36px;
              font-size: 18px;
              border-radius: 8px;
              transition: 0.3s ease;
            }

            .btn-gradient-outline:hover {
              background: #fdc134;
              color: #000;
            }
          </style>


          <!-- 🔸 Buttons -->
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link me-3" href="<?= $base_url; ?>profile.php">Hi, <?php
$fullName  = $_SESSION['user_name'] ?? 'User';
$firstName = explode(' ', trim($fullName))[0];
?>
<?= htmlspecialchars($firstName); ?></a>
            </li>
            <li class="nav-item">
              <a class="btn btn-gradient-outline fw-bold" href="<?= $base_url; ?>logout.php">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="btn btn-gradient-outline fw-bold me-3" href="<?= $base_url; ?>login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-gradient-outline fw-bold" href="<?= $base_url; ?>login.php#registerBox">Signup</a>
            </li>
          <?php endif; ?>
        </div>
        <div class="d-lg-none text-center mt-3 d-flex">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a class="nav-link" href="profile.php">Hi, <?php
$fullName  = $_SESSION['user_name'] ?? 'User';
$firstName = explode(' ', trim($fullName))[0];
?>
<?= htmlspecialchars($firstName); ?></a> <br>
            <a href="<?= $base_url; ?>logout.php"> <button onclick="openLoginPopup()" class="login btn btn-outline-warning mb-2 px-4 ">Logout</button> </a>
          <?php else: ?>
            <a href="<?= $base_url; ?>login.php"> <button onclick="openLoginPopup()" class="login btn btn-outline-warning mb-2 px-4 ">LogIn</button></a> <br>
            <a href="<?= $base_url; ?>signup.php"> <button onclick="openLoginPopup()" class="login btn btn-outline-warning mb-2 px-4 ">Signup</button> </a><br>
          <?php endif; ?>
        </div>
      </div>
    
    </div>

  </nav>
