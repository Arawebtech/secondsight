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

    
<div class="container py-5">
  <div class="mx-auto p-5 border rounded shadow" style="max-width: 700px;">
    <h3 class="text-center mb-4 fw-bold">Track Your Order</h3>
    <form>
      <div class="mb-3">
        <h5 class="fw-bold">
           Awb number/Order ID :
        </h5>
        <label for="orderId" class="form-label">Please enter your Order ID </label>
        <input type="text" class="form-control" id="orderId" placeholder="Please Enter orde number or AWB" required>
      </div>
      <div class="mb-3">
        <h5 class="fw-bold">
           Enter Mobile Number or Email :
        </h5>
        <input type="text" class="form-control" id="emailOrPhone" placeholder="Enter Email or Phone" required>
      </div>
     <a href="order-tracking.html" class="btn btn-dark bg-dark text-light w-100" style="border-radius: 20px;">Track Order</a>

    </form>
  </div>
</div>


 <?php
include ('include/footer.php');

?>