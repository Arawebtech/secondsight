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
 

    .policy-wrapper {
      max-width: 900px;
      margin: auto;
      padding: 60px 20px;
    }

    .policy-wrapper h1 {
      color: #161392;
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .policy-wrapper h2 {
      color: #161392;
      font-size: 24px;
      font-weight: 600;
      margin-top: 40px;
      margin-bottom: 15px;
    }

    .policy-wrapper p {
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
      .policy-wrapper {
        padding: 40px 15px;
      }

      .policy-wrapper h1 {
        font-size: 28px;
      }

      .policy-wrapper h2 {
        font-size: 20px;
      }
    }
  </style>
 

  <div class="policy-wrapper container">
    <h1>Privacy Policy</h1>
    <p>
      At <span class="highlight">Second Sight</span>, we value your trust and are committed to protecting your personal and spiritual information. Whether you're consulting an astrologer, tarot reader, or seeking healing services, we understand the sacredness of what you share with us. This Privacy Policy is designed to help you understand how we collect, use, and protect the information you provide.
    </p>

    <h2>Your Information & Its Use</h2>
    <p>
      When you interact with our platform, we may collect personal details like your name, contact number, email address, and birth details — specifically to offer accurate spiritual guidance and personalized experiences. This information is used only for your consultations, horoscope generation, or any services you intentionally seek. We do not use your details for anything unrelated to your experience with us, and we certainly do not sell or misuse your data.
    </p>
    <p>
      Your conversations with our advisors, including chat or call sessions, are treated with complete confidentiality. If we record or store any of your session data (for better continuity), we do so only with your consent and ensure it's protected using industry-standard encryption and security protocols.
    </p>

    <h2>Confidentiality & Security</h2>
    <p>
      Everything you share — from your spiritual concerns to your personal life — stays between you and us. We may use basic analytics tools to improve our services, but these never include personal or sensitive data. We use modern firewalls, encrypted databases, and restricted access systems to ensure your information is safe from any unauthorized access.
    </p>
    <p>
      If you ever feel uncomfortable or want to have your data removed from our system, you can reach out to us anytime. We honor such requests and ensure your information is deleted securely and permanently from our servers.
    </p>

    <h2>Contact Us</h2>
    <p>
      If you have any questions about how your privacy is protected at Second Sight, feel free to contact us via:
      <br><strong>Email:</strong> support@secondsight.com  
      <br><strong>Phone:</strong> +91-9876543210
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  
  <?php
include ('include/footer.php');

?>