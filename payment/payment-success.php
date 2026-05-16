<?php
include("../admin/inc/config.php");
include("payu_config.php"); // $MERCHANT_KEY, $SALT

$status       = $_POST["status"] ?? '';
$firstname    = $_POST["firstname"] ?? '';
$amount       = $_POST["amount"] ?? '';
$txnid        = $_POST["txnid"] ?? '';
$posted_hash  = $_POST["hash"] ?? '';
$key          = $_POST["key"] ?? '';
$productinfo  = $_POST["productinfo"] ?? '';
$email        = $_POST["email"] ?? '';
$transaction_id = $_POST['mihpayid'] ?? '';

/* ---------- Step 1: Verify PayU Hash ---------- */
$salt = $SALT;

if (isset($_POST["additionalCharges"])) {
    $additionalCharges = $_POST["additionalCharges"];
    $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status
                .'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
} else {
    $retHashSeq = $salt.'|'.$status
                .'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$calculated_hash = strtolower(hash("sha512", $retHashSeq));
if ($calculated_hash !== $posted_hash) {
    die("<h2>Payment Verification Failed. Hash mismatch.</h2>");
}

/* ---------- Step 2: Get payment record ---------- */
$query = "SELECT * FROM tbl_payment WHERE order_id = '$txnid' LIMIT 1";
$result = mysqli_query($con, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Payment record not found for txnid $txnid");
}
$data_pay = mysqli_fetch_assoc($result);

$order_id = $data_pay['order_id'];
$user_id  = $data_pay['user_id'];

/* ---------- Step 3: Update cart, payment & order status ---------- */
mysqli_query($con, "UPDATE tbl_cart SET is_ordered = '1' WHERE user_id = '$user_id' AND is_ordered = '0'");
mysqli_query($con, "UPDATE tbl_payment SET transaction_id = '$transaction_id', payment_status='Success' WHERE order_id = '$order_id'");
mysqli_query($con, "UPDATE tbl_order SET order_status = 'Success' WHERE order_id = '$order_id'");

/* ---------- Step 4: Build order details ---------- */
$body = "Dear {$firstname},\r\n\r\nThank you for your purchase! Here are your order details:\r\n\r\n";
$query = "SELECT p_name, p_actual_price, no_of_item FROM tbl_order WHERE order_id = '$order_id'";
$result = mysqli_query($con, $query);
while ($info = mysqli_fetch_assoc($result)) {
    $body .= "Product: {$info['p_name']}\r\n";
    $body .= "Quantity: {$info['no_of_item']}\r\n\r\n";
}
$body .= "Order ID: #{$order_id}\r\n";
$body .= "Transaction ID: {$transaction_id}\r\n";
$body .= "Total Amount: ₹{$amount}\r\n\r\n";
$body .= "We will notify you once your order is shipped.\r\n\r\nRegards,\r\nSecond Sight Team";

/* ---------- Step 5: Send emails ---------- */
$to_mail = $data_pay['payer_email'] ?? $email;
$subject = "Order Confirmation - #{$order_id}";
$headers  = "From: no-reply@arawebtechnologies.com\r\n";
$headers .= "Reply-To: smo@arawebtechnologies.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send to user
mail($to_mail, $subject, $body, $headers);
// Send copy to admin
mail("gurujimanishsharma@gmail.com", "New Order Received - #{$order_id}", $body, $headers);

$base_url = "https://secondsightfoundation.in/";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Second Sight</title>
    <!-- <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/main.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', Arial, sans-serif;
            overflow-x: hidden;
        }
        .glass-box {
            background: rgba(255,255,255,0.25);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 24px;
            border: 1.5px solid rgba(255,255,255,0.25);
            padding: 56px 36px 40px 36px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            position: relative;
        }
        .thankyou-icon {
            font-size: 4.5rem;
            color: #fdc134; /* Website theme color */
            margin-bottom: 18px;
            animation: pop 0.7s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); }
        }
        .thankyou-title {
            font-size: 2.3rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .thankyou-message {
            color: #4a4e69;
            font-size: 1.13rem;
            margin-bottom: 30px;
        }
        .thankyou-btn {
            background: linear-gradient(135deg, #fdc134, #fcb813);
            color: #000 !important;
            border: none;
            padding: 13px 36px;
            font-size: 1.08rem;
            font-weight: 600;
            border-radius: 10px;
            transition: 0.2s;
            text-decoration: none;
            margin-bottom: 0;
            box-shadow: 0 2px 12px rgba(253,193,52,0.13);
            display: inline-block;
        }
        .thankyou-btn:hover {
            opacity: 0.93;
            box-shadow: 0 4px 18px rgba(253,193,52,0.18);
        }
        .thankyou-btn.secondary {
            background: #fff;
            color: #fdc134 !important;
            border: 2px solid #fdc134;
            margin-top: 16px;
        }
        .order-id {
            font-size: 1.02rem;
            color: #fdc134;
            background: rgba(253,193,52,0.08);
            border-radius: 6px;
            padding: 6px 0 4px 0;
            margin-bottom: 18px;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        /* Confetti */
        .confetti {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <canvas class="confetti"></canvas>

    <div class="glass-box">
    <div class="thankyou-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="thankyou-title">Thank You!</div>

    <?php if (!empty($order_id)): ?>
        <div class="order-id">Order ID: <b>#<?= htmlspecialchars($order_id) ?></b></div>
    <?php endif; ?>

    <div class="thankyou-message">
        Your payment was successful.<br>
        <strong>Transaction ID:</strong> <?= htmlspecialchars($transaction_id) ?><br>
        <strong>Amount Paid:</strong> ₹ <?= htmlspecialchars($amount) ?><br><br>
        A confirmation email has been sent to <?= htmlspecialchars($to_mail) ?>.
    </div>

    <a href="<?= $base_url; ?>" class="thankyou-btn"><i class="fas fa-home me-2"></i>Back to Home</a>
    <a href="<?= $base_url; ?>products.php" class="thankyou-btn secondary"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
</div>

    <script>
    // Simple Confetti Animation
    const canvas = document.querySelector('.confetti');
    const ctx = canvas.getContext('2d');
    let W = window.innerWidth, H = window.innerHeight;
    canvas.width = W; canvas.height = H;
    let confetti = [];
    for(let i=0;i<120;i++){
        confetti.push({
            x: Math.random()*W,
            y: Math.random()*H - H,
            r: Math.random()*7+4,
            d: Math.random()*80+40,
            color: `hsl(${Math.random()*360},80%,60%)`,
            tilt: Math.random()*10-10
        });
    }
    function drawConfetti(){
        ctx.clearRect(0,0,W,H);
        confetti.forEach(c=>{
            ctx.beginPath();
            ctx.ellipse(c.x,c.y,c.r,c.r/2, c.tilt,0,2*Math.PI);
            ctx.fillStyle = c.color;
            ctx.fill();
        });
        updateConfetti();
    }
    function updateConfetti(){
        confetti.forEach(c=>{
            c.y += Math.cos(c.d)+2+c.r/2;
            c.x += Math.sin(c.d);
            c.tilt += Math.random()*0.1-0.05;
            if(c.y>H){c.y = -10; c.x = Math.random()*W;}
        });
    }
    setInterval(drawConfetti, 18);
    window.addEventListener('resize',()=>{
        W = window.innerWidth; H = window.innerHeight;
        canvas.width = W; canvas.height = H;
    });
    </script>
</body>
</html>

