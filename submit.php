<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['send_mail'])) {
    $firstName = $_POST['first_name'];
    $lastName  = $_POST['last_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $message   = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gurujimanishsharma@gmail.com'; 
        $mail->Password   = 'jnwo hxpp bphv rjkm';  
        $mail->SMTPSecure = 'tls'; 
        $mail->Port       = 587;   

        // Recipients
        $mail->setFrom($email, $firstName.' '.$lastName);
        $mail->addAddress('gurujimanishsharma@gmail.com', 'Secondsight Foundation Contact-Form');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body    = "
            <h2>Contact Form Submission</h2>
            <p><strong>First Name:</strong> {$firstName}</p>
            <p><strong>Last Name:</strong> {$lastName}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Message:</strong><br>{$message}</p>
        ";

        $mail->send();
        echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Error: {$mail->ErrorInfo}'); window.location.href='index.php';</script>";
    }
}
?>