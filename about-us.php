<?php
include("admin/inc/config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? '';

if (!$user_id) {
    $_SESSION['temp_user_id'] = rand(10000, 100000);
    $user_id = $_SESSION['temp_user_id'];
}

include('include/header.php');
?>


<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0 custom-breadcrumb-bg">
  <style>
    .custom-breadcrumb-bg {
      background-image: url('assets/images/about-banner');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 70vh;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
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

  <div class="container-fluid-lg">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb-contain">
          <h2>About Us</h2>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Breadcrumb Section End -->


<!-- Fresh Vegetable Section Start -->
<section class="fresh-vegetable-section section-lg-space">
  <div class="container-fluid-lg">
    <div class="row gx-xl-5 gy-xl-0 g-3 ratio_148_1">
      <div class="col-xl-6 col-12">
        <div class="row g-sm-4 g-2">
          <div class="col-6">
            <div class="fresh-image-2">
              <div>
                <img src=" assets/images/about1.png " class="bg-img  lazyload" alt="">
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="fresh-image">
              <div>
                <img src="assets/images/about2.png" class="bg-img  lazyload" alt="">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-6 col-12">
        <div class="fresh-contain p-center-left">
          <div>
            <div class="review-title" ">
                                <h4 style=" color:#e97d1f;">About Us</h4>
              <h2>Bringing Back the Light of Vision</h2>
            </div>

            <div class="delivery-list">
              <p class="text-content">A charitable group termed the Second Sight Foundation aims to better lives and
                give sight. We strive to eliminate avoidable blindness and visual impairment, especially in poor areas,
                as we were founded on the idea that vision is an inherent human right.
                For millions people in India and around the world, losing their sight means not just not able to see; it
                also means losing freedom, dignity, job, and education. That is why we’re trying to change.
                We at the Second Sight Foundation give second chances—a second opportunity to see, to live life to the
                fullest, to dream anew. Compassion, professionalism, and the impact that we make in the lives of those
                most in need are the basis of our work.
              </p>

              <ul class="delivery-box">
                <li>
                  <div class="delivery-box">
                    <div class="delivery-icon">
                      <img src="https://themes.pixelstrap.com/fastkart/assets/svg/3/delivery.svg" class=" lazyload"
                        alt="">
                    </div>

                    <div class="delivery-detail">
                      <h5 class="text">Free delivery for all orders</h5>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="delivery-box">
                    <div class="delivery-icon">
                      <img src="https://themes.pixelstrap.com/fastkart/assets/svg/3/leaf.svg" class=" lazyload" alt="">
                    </div>

                    <div class="delivery-detail">
                      <h5 class="text">100 % Gurantee</h5>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- about section end -->


<!-- work section start-->
<div class="container how-we-work">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">How</span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;"> We Make a Difference</h4>

  </div>
  <div class="row text-center g-0 mt-4">
    <div class="col-md-3 ">
      <div class="work-item">
        <div class="work-number" style="font-size: 5rem;">01</div>
        <div class="fs-4 fw-bold">Rudraksha Malas </div>
        <div class="work-title"> Spiritual prayer beads for daily use.</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="work-item">
        <div class="work-number" style="font-size: 5rem;">02</div>
        <div class="fs-4 fw-bold">Crystal Products</div>
        <div class="work-title">Items like spheres, pyramids, hearts, angels, and more.</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="work-item">
        <div class="work-number" style="font-size: 5rem;">03</div>
        <div class="fs-4 fw-bold">Coins & Yantras</div>
        <div class="work-title">Evil eye, turtles, Buddha, protect.</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="work-item">
        <div class="work-number" style="font-size: 5rem;">04</div>
        <div class="fs-4 fw-bold">Gemstone Rings</div>
        <div class="work-title">Citrine, emerald rings for astrology.</div>
      </div>
    </div>
  </div>
</div>
<style>
  .how-we-work {
    text-align: center;
    padding: 50px 0;
  }

  .work-item:hover {
    border-bottom: 4px solid #002a92;
  }

  .work-item {
    text-align: center;
    padding: 20px;
    border-bottom: 4px solid #fdc1345b;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .work-item:hover {
    transform: translateY(-20px);
  }

  .work-item:hover .work-number {
    color: #002a92 !important;
    color: #002a92;
  }

  .work-number:hover {
    -webkit-text-stroke: 1px #002a92 !important;
  }

  .work-number {
    font-size: 50px;
    font-weight: bold;
    color: transparent;
    -webkit-text-stroke: 1px #e97d1f;
  }

  .work-item.work-number:hover {
    color: #002a92;
    border: 1solid white;
  }

  .work-title {
    font-size: 18px;
    margin-top: 10px;
  }
</style>
<!-- work section end -->

<!-- Professional Mission & Values Section Start -->
<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
  <div class="container">
    <!-- Mission Block -->
    <div class="row align-items-center mb-5 flex-wrap">
      <div class="col-md-5 mb-4 mb-md-0">
        <div class="position-relative">
          <img src="assets/images/ourmission.png" alt="Our Mission" class="img-fluid shadow-lg"
            style="min-height:280px; object-fit:cover; border-radius: 20px; border: 4px solid #fda600;">
          <div class="position-absolute top-0 start-0 px-3 py-2 rounded-bottom-end"
            style="font-weight: bold; font-size: 14px; background-color: #fda600; color: #fff;">
            <i class="fas fa-bullseye me-2"></i>Our Mission
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="mission-content">
          <div class="d-flex align-items-center mb-4">
            <div class="mission-icon me-3">
              <i class="fas fa-eye "></i>
            </div>
            <h4 class="fw-bold " style=" font-size: 25px; color: #222;">Our Mission</h4>
          </div>
          <div class="mission-text" style="font-size: 1.1rem; color: #444; line-height: 1.8;">
            <div class="mission-highlight mb-4 p-4 rounded-start border-start border-4"
              style="background-color: rgba(253, 166, 0, 0.08); border-color: #fda600;">
              <strong style="color: #000; font-size: 1.2rem;">🌟 Vision for All:</strong>
              <p class="mb-0 mt-2">We believe that <span style="color: #fda600; font-weight: bold;">every person
                  deserves the gift of sight</span>, regardless of their financial circumstances. Vision is not a
                luxury—it's a fundamental human right.</p>
            </div>

            <div class="mission-points">
              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-heart"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Compassionate Care</h5>
                  <p class="mb-0" style="font-size: 1rem;">Through affordable and free eye care services, we reach out
                    to the most vulnerable and underserved communities, bringing hope where there was despair.</p>
                </div>
              </div>

              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Prevention First</h5>
                  <p class="mb-0" style="font-size: 1rem;">We focus on early detection and timely treatment to eliminate
                    avoidable blindness. Our regular screenings and awareness programs save lives before it's too late.
                  </p>
                </div>
              </div>

              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-hands-helping"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Community Partnership</h5>
                  <p class="mb-0" style="font-size: 1rem;">We work hand-in-hand with local communities, healthcare
                    providers, and volunteers to identify and treat those in need.</p>
                </div>
              </div>

              <div class="d-flex align-items-start">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-star"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Life Transformation</h5>
                  <p class="mb-0" style="font-size: 1rem;">Every intervention—from simple eye exams to complex
                    surgeries—restores independence, dignity, and the ability to dream again.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Values Block -->
    <div class="row align-items-center flex-wrap flex-md-row-reverse">
      <div class="col-md-5 mb-4 mb-md-0">
        <div class="position-relative">
          <img src="assets/images/ourvalue.png" alt="Our Values" class="img-fluid shadow-lg"
            style="min-height:280px; object-fit:cover; border-radius: 20px; border: 4px solid #fda600;">
          <div class="position-absolute top-0 start-0 px-3 py-2 rounded-bottom-end"
            style="font-weight: bold; font-size: 14px; background-color: #fda600; color: #fff;">
            <i class="fas fa-gem me-2"></i>Our Value
          </div>
        </div>
      </div>

      <div class="col-md-7">
        <div class="values-content">
          <div class="d-flex align-items-center mb-4">
            <div class="values-icon me-3">
              <i class="fas fa-diamond"></i>
            </div>
            <h4 class="fw-bold" style="font-size: 25px; color: #222;">Our Value</h4>
          </div>
          <div class="values-text" style="font-size: 1.1rem; color: #444; line-height: 1.8;">
            <div class="vision-highlight mb-4 p-4 rounded-start border-start border-4"
              style="background-color: rgba(22, 19, 146, 0.08); border-color: #161392;">
              <strong style="color: #000; font-size: 1.2rem;">🌍 World Without Blindness:</strong>
              <p class="mb-0 mt-2">We envision a world where <span style="color: #fda600; font-weight: bold;">no one
                  suffers from preventable blindness</span> simply because of poverty or geography.</p>
            </div>

            <div class="values-points">
              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-globe"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Universal Access</h5>
                  <p class="mb-0" style="font-size: 1rem;">Every individual, regardless of background, will have access
                    to quality eye care—from diagnosis to treatment and recovery.</p>
                </div>
              </div>

              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-users"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Dignified Living</h5>
                  <p class="mb-0" style="font-size: 1rem;">People will walk freely with confidence, see their loved ones
                    clearly, and participate in society with full dignity.</p>
                </div>
              </div>

              <div class="d-flex align-items-start mb-4">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-child"></i>
                </div>
                <div>
                  <h5 style="color:#484849ff; margin-bottom: 8px;">Empowered Future</h5>
                  <p class="mb-0" style="font-size: 1rem;">Children can learn, adults can work, and elders can live
                    fully—all because they can see the world around them.</p>
                </div>
              </div>

              <div class="d-flex align-items-start">
                <div class="point-icon me-3 mt-1">
                  <i class="fas fa-rocket"></i>
                </div>
                <div>
                  <h5 style="color: #484849ff; margin-bottom: 8px;">Innovation & Impact</h5>
                  <p class="mb-0" style="font-size: 1rem;">Through compassion, innovation, and relentless outreach, we
                    build this vision—one restored sight at a time.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .mission-icon,
  .values-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #fda600, #ffb84d);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    box-shadow: 0 4px 15px rgba(253, 166, 0, 0.3);
  }

  .point-icon {
    width: 30px;
    height: 30px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #fda600;
  }

  .value-card {
    transition: all 0.3s ease;
  }

  .value-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
  }

  .mission-highlight,
  .vision-highlight {
    position: relative;
    overflow: hidden;
  }

  .mission-highlight::before,
  .vision-highlight::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #fda600, #ffb84d);
  }

  @media (max-width: 767px) {

    .row.align-items-center>.col-md-5,
    .row.align-items-center>.col-md-7 {
      text-align: left;
    }

    .row.align-items-center img {
      margin-bottom: 1.5rem;
    }

    .mission-icon,
    .values-icon {
      margin: 0 1rem 0 0;
    }

    .d-flex.align-items-center {
      justify-content: flex-start;
    }
  }
</style>



<!-- Why choose us -->
<section class="container my-5">
  <div class="text-center mb-5">
    <span style="color:#e97d1f;">why</span>
    <h4 class="fw-bold" style="font-size: 32px; color: #222;">Choose Us</h4>
    <p class="text-muted" style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">We are committed to making vision
      care accessible to everyone, regardless of their financial circumstances.</p>
  </div>


  <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">

    <!-- CARD 1 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-heart-pulse"></i>
        </div>
        <h5 class="fw-bold mb-2">Compassionate Care</h5>
        <p>We treat every patient with dignity and respect, understanding that vision loss affects not just sight, but
          quality of life.</p>
      </div>
    </div>

    <!-- CARD 2 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-shield-check"></i>
        </div>
        <h5 class="fw-bold mb-2">Expert Medical Team</h5>
        <p>Our experienced ophthalmologists and healthcare professionals provide world-class eye care with proven
          results.</p>
      </div>
    </div>

    <!-- CARD 3 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-people"></i>
        </div>
        <h5 class="fw-bold mb-2">Community Outreach</h5>
        <p>We reach remote and underserved areas through mobile clinics and community partnerships to serve those most
          in need.</p>
      </div>
    </div>

    <!-- CARD 4 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <h5 class="fw-bold mb-2">Proven Impact</h5>
        <p>Over the years, we've restored vision to thousands of people, transforming lives and communities across
          India.</p>
      </div>
    </div>

    <!-- CARD 5 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-lightbulb"></i>
        </div>
        <h5 class="fw-bold mb-2">Innovation & Technology</h5>
        <p>We use the latest medical technology and innovative approaches to provide the best possible eye care
          outcomes.</p>
      </div>
    </div>

    <!-- CARD 6 -->
    <div class="col">
      <div class="benefit-card d-flex flex-column justify-content-center align-items-center text-center p-4 h-100">
        <div class="icon-wrap mb-3">
          <i class="bi bi-award"></i>
        </div>
        <h5 class="fw-bold mb-2">Transparency & Trust</h5>
        <p>We maintain complete transparency in our operations and build lasting trust with our patients and donors.</p>
      </div>
    </div>

  </div>
</section>



<style>
  .stat-card {
    padding: 2rem 1rem;
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    background-color: #002a92;
    color: #fff;
  }

  .stat-card:hover .stat-value {
    color: #fff !important;
  }

  .stat-card:hover .stat-description {
    color: #fff !important;
  }

  .stat-number-outline {
    font-size: 4rem;
    font-weight: bold;
    color: #f0f0f0;
    line-height: 1;
    margin-bottom: 1rem;
    font-family: 'Arial', sans-serif;
  }

  .stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #fda600;
    margin-bottom: 0.5rem;
    font-family: 'Arial', sans-serif;
  }

  .stat-description {
    font-size: 1rem;
    color: #666;
    font-weight: 500;
  }

  @media (max-width: 768px) {
    .stat-number-outline {
      font-size: 3rem;
    }

    .stat-value {
      font-size: 2rem;
    }

    .stat-description {
      font-size: 0.9rem;
    }
  }
</style>


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
<!-- Blog Section Start -->
<section class="blog-section py-5">
  <div class="container-fluid-lg">
    <div class="title title-4 mb-4">
      <h2>Blog</h2>
    </div>

    <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-6">
        <div class="blog-box ratio_45 shadow p-3 rounded bg-white">
          <div class="blog-box-image mb-3">
            <a href="blog-detail.html">
              <img
                src="https://astrotalk.store/cdn/shop/articles/Your_paragraph_text_8_9e0c01bb-46cb-4891-8620-daf70119377a_720x480.jpg?v=1751031255"
                class="img-fluid rounded h-50 w-100" alt="">
            </a>
          </div>
          <div class="blog-detail">
            <label class="badge bg-warning text-dark mb-2">Conversion rate optimization</label>
            <a href="blog-detail.php" class="text-decoration-none">
              <h4 class="fw-bold text-dark">Karungali Mala Uses: Benefits, Powers & the Deity It Is Associated With</h4>
            </a>
            <h5 class="text-danger mt-2">Anil Viradiya - MARCH 9, 2022</h5>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-6">
        <div class="blog-box ratio_45 shadow p-3 rounded bg-white">
          <div class="blog-box-image mb-3">
            <a href="blog-detail.php">
              <img
                src="https://astrotalk.store/cdn/shop/articles/11_Mukhi_Rudraksha_Benefits_and_Wearing_Rules_for_Maximum_Impact.jpg?v=1750940869&width=450"
                class="img-fluid rounded h-50 w-100" alt="">
            </a>
          </div>
          <div class="blog-detail">
            <label class="badge bg-warning text-dark mb-2">Email Marketing</label>
            <a href="blog-detail.php" class="text-decoration-none">
              <h4 class="fw-bold text-dark">11 Mukhi Rudraksha Benefits and Wearing Rules for Maximum Impact</h4>
            </a>
            <h5 class="text-danger mt-2">Anil Viradiya - MARCH 9, 2022</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Blog Section End -->


<?php
include 
('include/footer.php');
?>