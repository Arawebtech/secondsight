<?php 
include("admin/inc/config.php");
session_start();

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";

if (isset($_POST['send_mail'])) {
    $to_mail = mysqli_real_escape_string($con, trim($_POST['email']));
    
    if (!filter_var($to_mail, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Invalid email address.</div>";
    } else {
        // Check if email exists
        $query = "SELECT id FROM tbl_user WHERE email = '$to_mail'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['id'];

            // Generate secure token
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Store or update token
            $insert = "INSERT INTO tbl_password_resets (user_id, token, expires_at) 
                       VALUES ('$user_id', '$token', '$expiry')
                       ON DUPLICATE KEY UPDATE token='$token', expires_at='$expiry'";
            mysqli_query($con, $insert);

            // Reset link
            $reset_link = BASE_URL . "reset-password.php?token=" . $token;

            // Build Email Body
            $subject = "Reset Your Password - SSF";
            $message_body = "
                <p>Hello,</p>
                <p>We received a request to reset your password.</p>
                <p>Click the link below to create a new one:</p>
                <p><a href='$reset_link'>$reset_link</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this, you can ignore this email.</p>
            ";

            // Send mail via Gmail SMTP
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'gurujimanishsharma@gmail.com'; // your Gmail
                $mail->Password   = 'jnwo hxpp bphv rjkm';   // Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('gurujimanishsharma@gmail.com', 'SSF Support');
                $mail->addAddress($to_mail);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message_body;

                $mail->send();
                $message = "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>No account found with that email address.</div>";   
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("include/header.php");?>
    <style>
        .forgot-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        .forgot-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .forgot-container input[type=email],
        .forgot-container input[type=submit] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-size: 16px;
        }
        .forgot-container input[type=email] {
            border: 1px solid #ccc;
        }
        .forgot-container input[type=submit] {
            background: linear-gradient(to right, #ffb200, #fd9800);
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .forgot-container input[type=submit]:hover {
            background: linear-gradient(to right, #fd9800, #ffb200);
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
                <h4 class="text-center py-2 text-white" style="background: linear-gradient(to right, #ffb200, #fd9800);">Forgot Your Password</h4>
                <div id="content" class="site-content" role="main">
                    <div class="forgot-container">
                        <h2>Reset Your Password</h2>
                        <?php if(!empty($message)) echo $message; ?>
                        <form method="post" action="">
                            <input type="email" name="email" placeholder="Enter your registered email" required>
                            <input type="submit" name="send_mail" value="Send Reset Link">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php include("include/footer.php");?>
</body>
</html>
