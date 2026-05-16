<?php
include("admin/inc/config.php");
session_start();

$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch user info for header help
$query_user = "SELECT full_name FROM tbl_user WHERE id = '$user_id'";
$result_user = mysqli_query($con, $query_user);
$user_info = mysqli_fetch_assoc($result_user);
$user_name = $user_info['full_name'] ?? "Customer";

?>
<?php include("include/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders | Dashboard</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #ffb200 0%, #fd9800 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-header {
            background: var(--primary-gradient);
            padding: 60px 0 100px 0;
            color: white;
            margin-bottom: -60px;
        }

        .order-card {
            background: var(--glass-bg);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .order-header {
            border-bottom: 2px solid #f1f3f5;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .status-success { background: rgba(46, 125, 50, 0.1); color: #2e7d32; }
        .status-pending { background: rgba(253, 152, 0, 0.1); color: #fd9800; }
        .status-failed { background: rgba(211, 47, 47, 0.1); color: #d32f2f; }

        .item-row {
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .order-footer {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 40px 15px 80px 15px;
            }
            .order-card {
                padding: 20px 15px;
                border-radius: 15px;
            }
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .item-row {
                flex-direction: column;
                text-align: center;
            }
            .item-img {
                margin: 0 auto 15px auto;
            }
        }
    </style>
</head>

<body>
<div class="dashboard-header text-center">
    <div class="container">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="profile.php" class="btn btn-sm btn-light border-0 px-3 py-2 rounded-3 text-dark fw-bold"><i class="fas fa-arrow-left me-2"></i>Back</a>
            <div></div> <!-- Spacer -->
        </div>
        <h1 class="fw-bold mb-2">My Super Orders</h1>
        <p class="opacity-75">Check the status and history of all your purchases with us.</p>
        <div class="mt-3">
            <a href="<?= $base_url; ?>" class="text-white opacity-75 text-decoration-none">Home</a>
            <span class="mx-2">/</span>
            <span class="fw-bold">My Orders</span>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <?php
            // Fetch all orders grouped by order_id
            $order_id_query = $con->prepare("SELECT DISTINCT order_id FROM tbl_order WHERE user_id = ? ORDER BY id DESC");
            $order_id_query->bind_param("s", $user_id);
            $order_id_query->execute();
            $order_id_result = $order_id_query->get_result();

            if ($order_id_result->num_rows > 0):
                while ($order_id_row = $order_id_result->fetch_assoc()):
                    $order_id = $order_id_row['order_id'];

                    // Fetch products for this order
                    $order_query = $con->prepare("SELECT * FROM tbl_order WHERE user_id = ? AND order_id = ?");
                    $order_query->bind_param("ss", $user_id, $order_id);
                    $order_query->execute();
                    $order_result = $order_query->get_result();

                    // Fetch payment info
                    $payment_query = $con->prepare("SELECT * FROM tbl_payment WHERE order_id = ?");
                    $payment_query->bind_param("s", $order_id);
                    $payment_query->execute();
                    $payment = $payment_query->get_result()->fetch_assoc();
                    
                    $order_status = "";
                    $order_date_str = "";
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="text-muted d-block small text-uppercase fw-bold">Order Tracking ID</span>
                            <span class="fs-5 fw-bold text-dark">#<?= $order_id; ?></span>
                        </div>
                        <div class="text-md-end">
                            <span class="text-muted d-block small text-uppercase fw-bold">Total Amount</span>
                            <span class="fs-5 fw-bold" style="color: #fd9800;">₹<?= number_format($payment['payable_amount'] ?? 0, 2); ?></span>
                        </div>
                    </div>

                    <!-- Product List -->
                    <div class="order-body">
                        <?php while ($row = $order_result->fetch_assoc()): 
                            $order_status = $row['order_status'];
                            $order_date_str = $row['order_date'];
                        ?>
                            <div class="item-row d-flex align-items-center gap-4">
                                <img src="<?= $base_url; ?>assets/img/product-detail/<?= $row['p_image']; ?>" class="item-img" alt="product">
                                <div class="flex-grow-1 text-md-start">
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($row['p_name']); ?></h6>
                                    <p class="text-muted small mb-0">Qty: <?= $row['no_of_item']; ?> | Price: ₹<?= number_format($row['p_actual_price'], 2); ?></p>
                                </div>
                                <div class="text-md-end">
                                    <span class="status-badge <?= ($order_status == 'Success' || $order_status == 'Delivered') ? 'status-success' : 'status-pending'; ?>">
                                        <?= ucfirst($order_status); ?>
                                    </span>
                                    <?php if ($order_status === 'Delivered'): ?>
                                        <div class="mt-2">
                                            <a href="invoice-pdf.php?order_id=<?= base64_encode($row['order_id']); ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-file-invoice me-1"></i>Invoice</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="order-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-start">
                                <span class="text-muted d-block small"><i class="far fa-calendar-alt me-1"></i> Order Placed: <b><?= $order_date_str; ?></b></span>
                                <span class="text-muted d-block small"><i class="fas fa-wallet me-1"></i> Payment: <b><?= ucfirst($payment['payment_method'] ?? 'Online'); ?></b></span>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <a href="order-tracking.php?order_id=<?= $order_id; ?>" class="btn btn-sm btn-outline-warning fw-bold px-3">Track Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <div class="order-card text-center py-5">
                    <div class="mb-4 text-muted" style="font-size: 4rem;"><i class="fas fa-box-open"></i></div>
                    <h3 class="fw-bold">No orders found</h3>
                    <p class="text-muted">You haven't placed any orders yet. Start shopping to fill your history!</p>
                    <a href="products.php" class="btn btn-warning text-white fw-bold px-4 py-2 mt-3">Browse Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>
<script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
