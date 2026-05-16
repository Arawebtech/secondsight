<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vendor Landing Page</title>

<!-- FONT AWESOME ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" 
href="https://secondsightfoundation.com/assets/img/logo-fav.png">
<style>

/* ================= GLOBAL ================= */

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
}

a {
    text-decoration: none;
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: auto;
}

/* ================= HEADER ================= */

.header {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #000;
    padding: 15px 20px;
    text-align: center;
}


.header1 {
    background: ;
    color: #000;
    padding: 40px 20px;
    text-align: center;
}


        .site-logo {
            max-width: 220px;
            height: auto;
            margin-bottom: 10px;
        }

        .profile-img {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 5px solid white;
    object-fit: cover;
    margin-bottom: 15px;
}

.vendor-name {
    font-size: 28px;
    font-weight: bold;
}

.vendor-url {
    font-size: 16px;
    margin-top: 5px;
}

/* ================= SECTION ================= */

.section {
    background: white;
    margin-top: 20px;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.section h2 {
    margin-bottom: 15px;
    color: #333;
}

.description {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
}

/* ================= PRODUCT SLIDER ================= */

.slider {
    position: relative;
    max-width: 100%;
    overflow: hidden;
    border-radius: 10px;
}

.slides {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slides img {
    width: 100%;
    height: 750px;
    object-fit: cover;
}

/* SLIDER BUTTONS */

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    z-index: 10;
}

.slider-btn:hover {
    background: #ffc107;
    color: #000;
}

.prev {
    left: 10px;
}

.next {
    right: 10px;
}

/* ================= VIDEO ================= */

.video-wrapper {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
}

.video-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 10px;
}

/* ================= COUPON ================= */

.coupon-box {
    text-align: center;
}

.coupon-code {
    font-size: 32px;
    font-weight: bold;
    color: #ffc107;
    margin: 15px 0;
    letter-spacing: 2px;
}

.buy-btn {
    display: inline-block;
    background: #ffc107;
    color: black;
    padding: 14px 30px;
    border-radius: 6px;
    font-size: 16px;
    margin-top: 10px;
    transition: 0.3s;
    font-weight: bold;
}

.buy-btn:hover {
    background: #e0a800;
}

/* ================= SOCIAL ================= */

.social-links {
    text-align: center;
}

.social-links a {
    margin: 0 10px;
    font-size: 22px;
    color: #ffc107;
    display: inline-block;
    width: 45px;
    height: 45px;
    line-height: 45px;
    border-radius: 50%;
    border: 2px solid #ffc107;
    transition: 0.3s;
}

.social-links a:hover {
    background: #ffc107;
    color: black;
}

/* ================= MOBILE ================= */

@media (max-width: 600px) {

    .vendor-name {
        font-size: 22px;
    }

    .coupon-code {
        font-size: 26px;
    }

    .slides img {
        height: 220px;
    }

}



        /* ================= FOOTER ================= */

        .footer {
            background: #000;
            color: #fff;
            margin-top: 30px;
            padding: 30px 20px;
            text-align: center;
        }

        .footer h3 {
            margin-bottom: 10px;
            color: #ffc107;
        }

        .footer p {
            font-size: 14px;
            line-height: 1.6;
            margin: 5px 0;
        }

        .copyright {
            margin-top: 15px;
            font-size: 13px;
            opacity: 0.8;
        }
        
        .slider {
    position: relative;
    max-width: 100%;
    overflow: hidden;
}

.slides {
    display: flex;
    width: 100%;
    transition: transform 0.5s ease-in-out;
}

.slides img {
    width: 100%;
    height: 750px;
    object-fit: cover;

    /* KEY LINE — ek hi image dikhayegi */
    flex: 0 0 100%;
}


</style>

</head>

<body>

<!-- HEADER / PROFILE -->

<div class="header">

        <!-- Logo -->
        <div class="logo-wrapper">
            <img src="https://secondsightfoundation.in/assets/images/header2.png" alt="Company Logo" class="site-logo">
        </div>


</div>


<div class="header1">

     
    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="profile-img">

    <div class="vendor-name">
        Rahul Sharma
    </div>

    <div class="vendor-url">
        www.rahulstore.com
    </div>

</div>

<div class="container">

    <!-- PRODUCT SLIDER -->

    <div class="section">

        <h2>Our Products</h2>

        <div class="slider">

            <button class="slider-btn prev" onclick="prevSlide()">
                <i class="fas fa-chevron-left"></i>
            </button>

            <button class="slider-btn next" onclick="nextSlide()">
                <i class="fas fa-chevron-right"></i>
            </button>

            <div class="slides" id="slides">

                <img src="images/slider1.jpg" alt="Product 1">

                <img src="images/slider2.jpg" alt="Product 2">

                <!--<img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad" alt="Product 3">-->

                <!--<img src="https://images.unsplash.com/photo-1606813907291-d86efa9b94db" alt="Product 4">-->

            </div>

        </div>

    </div>

    <!-- DESCRIPTION -->

    <div class="section">

        <h2>About Vendor</h2>

        <div class="description">
            This vendor provides high-quality products with the best discounts and customer service. Use the coupon below to get your special offer today.
        </div>

    </div>


   <div class="section coupon-box">

        <h2>Special Offer Coupon</h2>

        <div class="coupon-code">
            RAHUL10
        </div>

        <a href="buy-product.html" class="buy-btn">
            Buy Product Now
        </a>

    </div>
    <!-- VIDEO SECTION -->

    <div class="section">

        <h2>Watch Introduction Video</h2>

        <div class="video-wrapper">

            <!--<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>-->
            <iframe width="560" height="315" src="https://www.youtube.com/embed/ZucOiqzRznA?si=jWRKfBp5jPy2yl-2" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

        </div>

    </div>

    <!-- COUPON SECTION -->

 

    <!-- SOCIAL LINKS -->

    <div class="section social-links">

        <h2>Connect With Me</h2>

        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
        <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>

    </div>

</div>

<script>

let index = 0;

const slides = document.getElementById("slides");
const totalSlides = slides.children.length;

function showSlide() {
    slides.style.transform = 
        "translateX(-" + (index * 100) + "%)";
}

function nextSlide() {
    index++;

    if (index >= totalSlides) {
        index = 0;
    }

    showSlide();
}

function prevSlide() {
    index--;

    if (index < 0) {
        index = totalSlides - 1;
    }

    showSlide();
}

/* Auto slide */
setInterval(nextSlide, 3000);


</script>



    <!-- FOOTER -->

    <footer class="footer">
        <div class="container">

            <div class="footer-content">

                <h3>About Our Company</h3>

                <p>
                    We provide trusted products and exclusive offers through our verified vendors. 
                    Use the coupon above to get the best deals directly from the vendor.
                </p>

                <p class="copyright">
                    © 2026 All Rights Reserved | Your Company Name
                </p>

            </div>

        </div>
    </footer>

</body>

</html>
