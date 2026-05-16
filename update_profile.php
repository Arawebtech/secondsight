<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/inc/config.php");

// Get User ID from session
$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
if (!$user_id) {
    die("Session error: User ID not found. Please login again.");
}

$affected = 0;

// 1. Handle Personal Info Update
if (isset($_POST['update_personal_info'])) {
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');

    // Check if user already exists
    $check_u = mysqli_query($con, "SELECT id FROM tbl_user WHERE id = '$user_id'");
    if (mysqli_num_rows($check_u) > 0) {
        // Update existing user
        $sql = "UPDATE tbl_user SET full_name = '$full_name', phone = '$phone', password = '$password' WHERE id = '$user_id'";
        if (!empty($email)) {
            $sql = "UPDATE tbl_user SET full_name = '$full_name', phone = '$phone', password = '$password', email = '$email' WHERE id = '$user_id'";
        }
        mysqli_query($con, $sql);
        $affected += 1;
    } else {
        // Create new user record if they don't exist
        
        // Check if email already exists for another user
        $check_e = mysqli_query($con, "SELECT id FROM tbl_user WHERE email = '$email'");
        if (mysqli_num_rows($check_e) > 0) {
            echo '<script>alert("Error: This email is already registered with another account."); window.location.href = "profile.php";</script>';
            exit();
        }

        if (is_numeric($user_id)) {
            $sql = "INSERT INTO tbl_user (id, full_name, phone, password, email, status) VALUES ('$user_id', '$full_name', '$phone', '$password', '$email', 'Active')";
        } else {
            $sql = "INSERT INTO tbl_user (full_name, phone, password, email, status) VALUES ('$full_name', '$phone', '$password', '$email', 'Active')";
        }

        if (mysqli_query($con, $sql)) {
            if (!is_numeric($user_id)) {
                $_SESSION['user_id'] = mysqli_insert_id($con); // Upgrade to real user
            }
            $affected += 1;
        }
    }
}


// 2. Handle Consolidated Address Update (Merged Billing/Shipping)
if (isset($_POST['update_user_address'])) {
    // Fetch main user info since Name/Mobile were removed from address form
    $res_u = mysqli_query($con, "SELECT full_name, phone FROM tbl_user WHERE id = '$user_id'");
    $u_info = mysqli_fetch_assoc($res_u);
    
    $u_name = mysqli_real_escape_string($con, $u_info['full_name'] ?? '');
    $u_mobile = mysqli_real_escape_string($con, $_POST['u_mobile'] ?? $u_info['phone'] ?? ''); // Fallback to info if POST missing

    $u_address = mysqli_real_escape_string($con, $_POST['u_address']);
    $u_town = mysqli_real_escape_string($con, $_POST['u_town']);
    $u_state = mysqli_real_escape_string($con, $_POST['u_state']);
    $u_pincode = mysqli_real_escape_string($con, $_POST['u_pincode']);
    $u_landmark = mysqli_real_escape_string($con, $_POST['u_landmark'] ?? '');
    $u_district = mysqli_real_escape_string($con, $_POST['u_district'] ?? '');
    $u_building = mysqli_real_escape_string($con, $_POST['u_building'] ?? '');
    $u_gst = mysqli_real_escape_string($con, $_POST['u_gst'] ?? '');

    // Synchronize Billing Table
    $check_b = mysqli_query($con, "SELECT id FROM tbl_billing_address WHERE user_id = '$user_id'");
    if (mysqli_num_rows($check_b) > 0) {
        $sql_b = "UPDATE tbl_billing_address SET name = '$u_name', phone_no = '$u_mobile', building_no = '$u_building', street_address = '$u_address', town = '$u_town', state = '$u_state', pincode = '$u_pincode', landmark = '$u_landmark', district = '$u_district', gst_no = '$u_gst' WHERE user_id = '$user_id'";
    } else {
        $sql_b = "INSERT INTO tbl_billing_address (user_id, name, phone_no, building_no, street_address, town, state, pincode, landmark, district, gst_no) VALUES ('$user_id', '$u_name', '$u_mobile', '$u_building', '$u_address', '$u_town', '$u_state', '$u_pincode', '$u_landmark', '$u_district', '$u_gst')";
    }
    mysqli_query($con, $sql_b);

    // Synchronize Shipping Table
    $check_s = mysqli_query($con, "SELECT id FROM tbl_shipping_address WHERE user_id = '$user_id'");
    if (mysqli_num_rows($check_s) > 0) {
        $sql_s = "UPDATE tbl_shipping_address SET name = '$u_name', phone_no = '$u_mobile', building_no = '$u_building', street_address = '$u_address', town = '$u_town', state = '$u_state', pincode = '$u_pincode', landmark = '$u_landmark', district = '$u_district', gst_no = '$u_gst' WHERE user_id = '$user_id'";
    } else {
        $sql_s = "INSERT INTO tbl_shipping_address (user_id, name, phone_no, building_no, street_address, town, state, pincode, landmark, district, gst_no) VALUES ('$user_id', '$u_name', '$u_mobile', '$u_building', '$u_address', '$u_town', '$u_state', '$u_pincode', '$u_landmark', '$u_district', '$u_gst')";
    }
    mysqli_query($con, $sql_s);

    
    $affected = 1; // Mark as updated for alert
}

if ($affected > 0 || isset($_POST['update_personal_info']) || isset($_POST['update_user_address'])) {
    echo '<script>alert("Profile details updated successfully!"); window.location.href = "profile.php";</script>';
} else {
    header("Location: profile.php");
}
exit();
?>
