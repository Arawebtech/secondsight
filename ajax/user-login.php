<?php
session_start();
include("../admin/inc/config.php");

header('Content-Type: text/plain'); // ensure plain text response

$action = isset($_POST['action']) ? $_POST['action'] : "";

if ($action === "login") {
    $email_id = mysqli_real_escape_string($con, $_POST['email_id']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $remember = isset($_POST['remember']) ? intval($_POST['remember']) : 0;

    // Check if user exists
    $query_user = "SELECT * FROM tbl_user WHERE email='$email_id' LIMIT 1";
    $result_user = mysqli_query($con, $query_user);
    
    if (mysqli_num_rows($result_user) == 0) {
        echo "not_registered"; // Email not found → prompt to register
    } else {
        $row = mysqli_fetch_assoc($result_user);

        if ($row['password'] !== $password || $row['status'] !== 'Active') {
            echo "invalid_credentials"; // Password mismatch or inactive account
        } else {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['email_id']  = $row['email'];
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['phone']     = $row['phone'];
            $_SESSION['flash_message'] = "Welcome back, " . $row['full_name'] . "!";

            if ($remember === 1) {
                setcookie("user_id", $row['id'], time() + (86400 * 30), "/");
            }

            // Update cart if temp user exists
            if (!empty($_SESSION['temp_user_id'])) {
                $temp_user_id = $_SESSION['temp_user_id'];
                $_SESSION['temp_user_id'] = "";
                $user_id = $_SESSION['user_id'];
                $query_update = "UPDATE tbl_cart SET user_id = '$user_id' WHERE user_id = '$temp_user_id'";
                mysqli_query($con, $query_update);
            }

            echo "success";
        }
    }
}

if ($action === "register") {
    $user_name = mysqli_real_escape_string($con, $_POST['reg_user_name']);
    $email_id  = mysqli_real_escape_string($con, $_POST['reg_email']);
    $mobile    = mysqli_real_escape_string($con, $_POST['reg_mobile']);
    $password  = mysqli_real_escape_string($con, $_POST['reg_password']);

    // Check duplicate email
    $query_check = "SELECT id FROM tbl_user WHERE email='$email_id'";
    $result_check = mysqli_query($con, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "Email already exists ! Please login to continue.";
    } else {
        // $query_reg = "INSERT INTO tbl_user(full_name, email, phone, password, status) 
        //               VALUES('$user_name', '$email_id', '$mobile', '$password', 'Active')";
        // $result = mysqli_query($con, $query_reg);
        
        // Register user
$query_reg = "INSERT INTO tbl_user(full_name, email, phone, password, status, photo, role) 
VALUES('$user_name', '$email_id', '$mobile', '$password', 'Active', 'default.png', 'user')";

$result = mysqli_query($con, $query_reg);

        if ($result) {
            $user_id = mysqli_insert_id($con);
            $_SESSION['user_id']   = $user_id;
            $_SESSION['email_id']  = $email_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['phone']     = $mobile;
            $_SESSION['flash_message'] = "Welcome, $user_name!";

            // Update cart if temp user exists
            if (!empty($_SESSION['temp_user_id'])) {
                $temp_user_id = $_SESSION['temp_user_id'];
                $_SESSION['temp_user_id'] = "";
                $query_update = "UPDATE tbl_cart 
                                 SET user_id = '$user_id' 
                                 WHERE user_id = '$temp_user_id'";
                mysqli_query($con, $query_update);
            }

            echo "success";
        } else {
            echo "Error while registering user";
        }
    }
}
?>
