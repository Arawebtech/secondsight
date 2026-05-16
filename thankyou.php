<?php
session_start();
include("admin/inc/config.php");


if (!isset($_SESSION['order_success']) || $_SESSION['order_success'] !== true) {
    // If user refreshes or directly visits without placing order
    header("Location: index.php");
    exit();
}

// Destroy the flag so thankyou.php can't be revisited
unset($_SESSION['order_success']);

// Optional: prevent browser caching of thankyou page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");



// Get order ID from session
$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;

// Fetch order details if needed (e.g., customer name or total)
$order_info = null;
if ($order_id) {
    $result = mysqli_query($con, "SELECT * FROM tbl_order WHERE order_id = '$order_id'");
    $order_info = mysqli_fetch_assoc($result);
}
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
        <?php if ($order_id): ?>
            <div class="order-id mb-3">Order ID: <b>#<?= htmlspecialchars($order_id) ?></b></div>
        <?php endif; ?>
        <div class="thankyou-message">
            Your order has been placed successfully.<br>
            We appreciate your trust in us.<br>
            You will receive a confirmation email shortly.
        </div>
        <a href="index.php" class="thankyou-btn"><i class="fas fa-home me-2"></i>Back to Home</a>
        <br>
        <a href="products.php" class="thankyou-btn secondary"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
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
