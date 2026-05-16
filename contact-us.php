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





<style>
    .custom-breadcrumb-bg {
      background-image: url('assets/images/contact');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 70vh; /* Fixed height for all devices */
      display: flex;
      align-items: center ;
    }

    @media (max-width: 768px) {
      .custom-breadcrumb-bg {
        height: 300px; /* Same height on mobile */
      }
    }

    .breadcrumb-contain h2{
font-size: 30px !important;
    }
    .breadcrumb-contain {
      color: #fff; /* Optional: Make text white for better visibility */
    }
</style>

  <!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0 custom-breadcrumb-bg">
  <div class="container-fluid-lg">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb-contain">
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Breadcrumb Section End -->


    <!-- Contact Box Section Start -->
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
                                                <div class="contact-detail-title" >
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
                                                <div class="contact-icon"  style="background-color: #fdc134;" >
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

    <!-- Map Section Start -->
    <section class="map-section">
        <div class="container-fluid p-0">
            <div class="map-box">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14005.565031084938!2d77.10352774101322!3d28.648001341494783!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce36daf8d5329%3A0x292538a1be5c43b2!2sSECOND%20SIGHT%20Foundation!5e0!3m2!1sen!2sin!4v1748428300329!5m2!1sen!2sin"
                    style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
    <!-- Map Section End -->

    
  <?php
include ('include/footer.php');

?>