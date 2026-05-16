<?php
include("admin/inc/config.php");
session_start();
$user_id = "";
if (isset($_SESSION['user_id']))
  $user_id = $_SESSION['user_id'];
else if (isset($_SESSION['temp_user_id']))
  $user_id = $_SESSION['temp_user_id'];

include('include/header.php');

?>
<style>
     .terms-wrapper {
      max-width: 900px;
      margin: auto;
      padding: 60px 20px;
    }

    .terms-wrapper h1 {
      color: #161392;
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .terms-wrapper h2 {
      color: #161392;
      font-size: 24px;
      font-weight: 600;
      margin-top: 40px;
      margin-bottom: 15px;
    }

    .terms-wrapper p {
      font-size: 16px;
      line-height: 1.8;
      color: #555;
      margin-bottom: 18px;
    }

    .highlight {
      color: #fdc134;
      font-weight: 600;
    }

    @media (max-width: 576px) {
      .terms-wrapper {
        padding: 40px 15px;
      }

      .terms-wrapper h1 {
        font-size: 28px;
      }

      .terms-wrapper h2 {
        font-size: 20px;
      }
    }
</style>

 <div class="terms-wrapper">
    <h1>Terms & Conditions</h1>
    <p>
      Welcome to <span class="highlight">Second Sight</span>. By accessing or using our website and services, you agree to be bound by the following Terms and Conditions. These terms apply to all visitors, users, and clients who wish to access or use our services, including astrology consultations, tarot readings, spiritual guidance, and product purchases.
    </p>

    <h2>Use of Services</h2>
    <p>
      All consultations and content provided on our platform are intended for personal and spiritual guidance only. They do not replace professional advice such as medical, legal, or financial counsel. You must be 18 years or older to use our services or obtain consent from a parent or guardian. You are responsible for any interpretations, decisions, or actions you make based on the information received from our experts.
    </p>
    <p>
      We reserve the right to refuse service to anyone who behaves abusively toward our advisors or misuses the platform. Reproduction or redistribution of our content without permission is strictly prohibited.
    </p>

    <h2>Payments, Refunds & Liability</h2>
    <p>
      All payments made for sessions, remedies, or spiritual products are non-refundable once the service has been initiated or the product dispatched. We strive to ensure a high level of accuracy and professionalism in our readings and offerings, but we do not guarantee specific outcomes. Spiritual experiences vary from person to person, and results depend on individual belief and energy alignment.
    </p>
    <p>
      We are not liable for any direct or indirect loss or emotional distress arising from your use of our platform. Please consult appropriate professionals for serious mental health or medical concerns.
    </p>

    <h2>Contact Us</h2>
    <p>
      If you have any questions about these Terms & Conditions, feel free to contact us at:
      <br><strong>Email:</strong> support@secondsight.com  
      <br><strong>Phone:</strong> +91-9876543210
    </p>
  </div>

  
  <?php
include ('include/footer.php');

?>