<?php
session_start();
include("admin/inc/config.php");
$user_id = "";
if(isset($_SESSION['user_id']))
    $user_id = $_SESSION['user_id'];
else if(isset($_SESSION['temp_user_id']))
    $user_id = $_SESSION['temp_user_id'];

// Handle form submission before any HTML output
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["register"])) {
    $UserName   = mysqli_real_escape_string($con, $_POST["UserName"]);
    $email      = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"];
    $Mobile     = mysqli_real_escape_string($con, $_POST["Mobile"]);
    $b_name     = mysqli_real_escape_string($con, $_POST["b_name"]);
    $b_mobile   = mysqli_real_escape_string($con, $_POST["b_mobile"]);
    $b_address  = mysqli_real_escape_string($con, $_POST["b_address"]);
    $b_town     = mysqli_real_escape_string($con, $_POST["b_town"]);
    $b_city     = mysqli_real_escape_string($con, $_POST["b_city"]);
    $b_state    = mysqli_real_escape_string($con, $_POST["b_state"]);
    $b_pincode  = mysqli_real_escape_string($con, $_POST["b_pincode"]);
    $b_landmark = mysqli_real_escape_string($con, $_POST["b_landmark"]);
    $s_name     = mysqli_real_escape_string($con, $_POST["s_name"]);
    $s_mobile   = mysqli_real_escape_string($con, $_POST["s_mobile"]);
    $s_address  = mysqli_real_escape_string($con, $_POST["s_address"]);
    $s_town     = mysqli_real_escape_string($con, $_POST["s_town"]);
    $s_city     = mysqli_real_escape_string($con, $_POST["s_city"]);
    $s_state    = mysqli_real_escape_string($con, $_POST["s_state"]);
    $s_pincode  = mysqli_real_escape_string($con, $_POST["s_pincode"]);
    $s_landmark = mysqli_real_escape_string($con, $_POST["s_landmark"]);

    $query = "INSERT INTO tbl_register (
        UserName, email, password, Mobile,
        b_name, b_mobile, b_address, b_town, b_city, b_state, b_pincode, b_landmark,
        s_name, s_mobile, s_address, s_town, s_city, s_state, s_pincode, s_landmark
    ) VALUES (
        '$UserName', '$email', '$password', '$Mobile',
        '$b_name', '$b_mobile', '$b_address', '$b_town', '$b_city', '$b_state', '$b_pincode', '$b_landmark',
        '$s_name', '$s_mobile', '$s_address', '$s_town', '$s_city', '$s_state', '$s_pincode', '$s_landmark'
    )";

    if (mysqli_query($con, $query)) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Failed to save data. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .register-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; }
        .box-form-login { border: 1px solid #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        label.required:after { content:" *"; color: red; }
    </style>
</head>
<body>
<?php include("include/header.php"); ?>

<div class="container my-5">
    <div class="shadow bg-white p-4 rounded">
        <h4 class="text-center py-2 text-white" style="background: linear-gradient(to right, #ffb200, #fd9800);">Registration</h4>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="row">
                <!-- Personal Details -->
                <div class="col-md-4">
                    <div class="box-form-login">
                        <div class="register-title">Personal Details</div>
                        <div class="mb-2">
                            <label class="required">User Name</label>
                            <input type="text" name="UserName" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="required">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="required">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="required">Mobile</label>
                            <input type="text" name="Mobile" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="col-md-4">
                    <div class="box-form-login">
                        <div class="register-title">Billing Address</div>
                        <div class="mb-2"><label class="required">Name</label><input type="text" name="b_name" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Mobile</label><input type="text" name="b_mobile" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Address</label><input type="text" name="b_address" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Town</label><input type="text" name="b_town" class="form-control" required></div>
                        <div class="mb-2"><label class="required">City</label><input type="text" name="b_city" class="form-control" required></div>
                        <div class="mb-2"><label class="required">State</label><input type="text" name="b_state" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Pincode</label><input type="text" name="b_pincode" class="form-control" required></div>
                        <div class="mb-2"><label>Landmark</label><input type="text" name="b_landmark" class="form-control"></div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="col-md-4">
                    <div class="box-form-login">
                        <div class="register-title">Shipping Address</div>
                        <div class="mb-2"><label class="required">Name</label><input type="text" name="s_name" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Mobile</label><input type="text" name="s_mobile" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Address</label><input type="text" name="s_address" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Town</label><input type="text" name="s_town" class="form-control" required></div>
                        <div class="mb-2"><label class="required">City</label><input type="text" name="s_city" class="form-control" required></div>
                        <div class="mb-2"><label class="required">State</label><input type="text" name="s_state" class="form-control" required></div>
                        <div class="mb-2"><label class="required">Pincode</label><input type="text" name="s_pincode" class="form-control" required></div>
                        <div class="mb-2"><label>Landmark</label><input type="text" name="s_landmark" class="form-control"></div>
                    </div>
                </div>

                <!-- Register Button -->
                <div class="col-12 text-center">
                    <button type="submit" name="register" class="btn btn-warning mt-3 px-5 py-2 text-white fw-bold" style="background: linear-gradient(to right, #ffb200, #fd9800); border: none;">Register</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('include/footer.php'); ?>
<script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

