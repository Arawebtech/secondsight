<?php
include("admin/inc/config.php");
session_start();

$message = "";

// When user clicks the email link
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $query = "SELECT * FROM tbl_password_resets WHERE token = '$token' AND expires_at > NOW() LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $reset_data = mysqli_fetch_assoc($result);
        $user_id = $reset_data['user_id'];
    } else {
        $message = "<div class='alert alert-danger'>Invalid or expired reset link.</div>";
        $token = null;
    }
} else {
    $message = "<div class='alert alert-danger'>No reset token provided.</div>";
    $token = null;
}

// When user submits new password
if (isset($_POST['reset_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    $token = mysqli_real_escape_string($con, $_POST['token']);

    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } elseif (strlen($new_password) < 6) {
        $message = "<div class='alert alert-warning'>Password must be at least 6 characters long.</div>";
    } else {
        $query = "SELECT * FROM tbl_password_resets WHERE token = '$token' AND expires_at > NOW() LIMIT 1";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 1) {
            $reset_data = mysqli_fetch_assoc($result);
            $user_id = $reset_data['user_id'];

            // Store password in plain text
            $update = "UPDATE tbl_user SET password = '$new_password' WHERE id = '$user_id'";
            mysqli_query($con, $update);

            // Delete token after successful reset
            mysqli_query($con, "DELETE FROM tbl_password_resets WHERE user_id = '$user_id'");

            $message = "<div class='alert alert-success'>Your password has been reset successfully! <a href='login.php'>Login here</a>.</div>";
            $token = null;
        } else {
            $message = "<div class='alert alert-danger'>Invalid or expired reset request.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("include/header.php"); ?>
    <style>
        .reset-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        .reset-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .reset-container input[type=password] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        .reset-container input[type=submit] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.3s;
            background: linear-gradient(to right, #ffb200, #fd9800);
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .reset-container input[type=submit]:hover {
            background: #0056b3;
        }
        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .alert-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body class="page">
    <div class="container my-5">
            <div class="shadow bg-white p-4 rounded">
                <h4 class="text-center py-2 text-white" style="background: linear-gradient(to right, #ffb200, #fd9800);">Reset Your Password</h4>
                <div id="content" class="site-content" role="main">
                    <div class="reset-container">
                        <h2>Create a New Password</h2>
                        <?php if(!empty($message)) echo $message; ?>

                        <?php if(!empty($token)): ?>
                        <form method="post" action="">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <input type="password" name="new_password" placeholder="New Password" required>
                            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                            <input type="submit" name="reset_password" value="Reset Password">
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php include("include/footer.php"); ?>
</body>
</html>
