<?php
session_start();
include("admin/inc/config.php");

// Restore session from cookie if session is missing
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

$user_id = "";
if (isset($_SESSION['user_id'])) 
    $user_id = $_SESSION['user_id'];
elseif (isset($_SESSION['temp_user_id']))
    $user_id = $_SESSION['temp_user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<?php include("include/header.php"); ?>

<body>
<div class="container my-5">
    <div class="shadow bg-white p-4 rounded">
        <h4 class="text-center py-2 text-white" 
            style="background: linear-gradient(to right, #ffb200, #fd9800);">
            Login / Register
        </h4>
        <div class="text-center mb-4">
            <button class="btn btn-outline-warning me-2" id="showLogin">Login</button>
            <button class="btn btn-outline-warning" id="showRegister">Register</button>
        </div>

        <div class="row mt-4">
            <!-- Login Section -->
            <div class="col-md-6 mx-auto" id="loginBox">
                <div class="box-form-login">
                    <div id="error_message" class="text-danger text-center mb-3"></div>
                    <form id="login_ajax" method="post">
                        <div class="mb-3">
                            <label>Email Address<span class="required">*</span></label>
                            <input type="email" class="form-control" id="login_email" required>
                        </div>
                        <div class="mb-3">
                            <label>Password<span class="required">*</span></label>
                            <input type="password" class="form-control" id="login_password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        <div class="d-grid">
                            <button type="button" onclick="user_login();" 
                                class="btn btn-warning text-white fw-bold" 
                                style="background: linear-gradient(to right, #ffb200, #fd9800);">
                                Log In
                            </button>
                        </div>
                        <div class="text-center mt-2">
                               <a href="<?= $base_url; ?>login.php#registerBox" 
                               class="text-muted" id="regstr">New User Registration</a> |
                               <a href="<?= $base_url; ?>forgot-password.php" 
                               class="text-muted">Forgot your password?</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Register Section -->
            <div class="col-md-6 mx-auto" id="registerBox" style="display:none;">
                <div class="box-form-login">
                    <div id="error_message2" class="text-danger text-center mb-3"></div>
                    <form id="register_ajax" method="post">
                        <div class="mb-3">
                            <label>Full Name<span class="required">*</span></label>
                            <input type="text" class="form-control" id="reg_user_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Mobile Number<span class="required">*</span></label>
                            <input type="tel" class="form-control" id="reg_mobile" name="reg_mobile" pattern="[0-9]{10}" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
       placeholder="Enter 10-digit mobile number" 
       required>

                        </div>
                        <div class="mb-3">
                            <label>Email Address<span class="required">*</span></label>
                            <input type="email" class="form-control" id="reg_email" required>
                        </div>
                        <div class="mb-3">
                            <label>Password<span class="required">*</span></label>
                            <input type="password" class="form-control" id="reg_password" required>
                        </div>
                        <div class="d-grid">
                            <button type="button" onclick="user_reg();" 
                                class="btn btn-warning text-white fw-bold" 
                                style="background: linear-gradient(to right, #ffb200, #fd9800);">
                                Register Now
                            </button>
                        </div>
                        <div class="text-center mt-2">
                               <a href="<?= $base_url; ?>login.php" 
                               class="text-muted" id="regstr">Already Registered Click here to login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include("include/footer.php"); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.location.hash === "#registerBox") {
        document.getElementById("registerBox").style.display = "block";
        document.getElementById("loginBox").style.display = "none";
    }
});
</script>

<script>
var base_url = "<?php echo $base_url; ?>";

// Toggle Login/Register
$(document).ready(function () {
    $('#showLogin').on('click', function () {
        $('#loginBox').show();
        $('#registerBox').hide();
    });

    $('#showRegister').on('click', function () {
        $('#registerBox').show();
        $('#loginBox').hide();
    });
    
    $('#regstr').on('click', function () {
        $('#registerBox').show();
        $('#loginBox').hide();
    });
    
});

// Login
function user_login() {
    var email_id = $("#login_email").val().trim();
    var password = $("#login_password").val().trim();
    var remember = $("#remember").is(":checked") ? 1 : 0;  // Get the checkbox state

    if (email_id === "" || password === "") {
        $("#error_message").text("Please enter both email and password");
        return;
    }

    $.ajax({
        type: "POST",
        url: base_url + "ajax/user-login.php",
        data: {action: "login", email_id: email_id, password: password, remember: remember},
         success: function(result) {
            if (result === "success") {
                window.location.href = base_url + "cart.php";
            } else if (result === "not_registered") {
                $("#error_message").text("Email not registered. Please click on Register.");
                // Do not toggle forms
            } else if (result === "invalid_credentials") {
                $("#error_message").text("Invalid email or password. Please try again.");
            } else {
                $("#error_message").text("An error occurred. Please try again.");
            }
        }
    });
}

// Register
function user_reg() {
    var name = $("#reg_user_name").val().trim();
    var mobile = $("#reg_mobile").val().trim();
    var email = $("#reg_email").val().trim();
    var password = $("#reg_password").val().trim();

    if (name === "" || mobile === "" || email === "" || password === "") {
        $("#error_message2").text("All fields are required");
        return;
    }
    
    // ✅ Mobile number validation: only digits, exactly 10
    var mobilePattern = /^[0-9]{10}$/;
    if (!mobilePattern.test(mobile)) {
        $("#error_message2").text("Please enter a valid 10-digit mobile number");
        return;
    }

    // (Optional) Basic email validation
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        $("#error_message2").text("Please enter a valid email address");
        return;
    }
    

    $.ajax({
        type: "POST",
        url: base_url + "ajax/user-login.php",
        data: {
            action: "register",
            reg_user_name: name,
            reg_mobile: mobile,
            reg_email: email,
            reg_password: password
        },
        success: function(result) {
            if (result === "success") {
                window.location.href = base_url + "cart.php";
            } else {
                $("#error_message2").text(result);
            }
        }
    });
}
</script>
</body>
</html>
