<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/inc/config.php");

$user_id = "";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
} else {
    header("location: login.php");
    exit;
}

// Fetch user data
$query_user = "SELECT * FROM tbl_user WHERE id = '$user_id'";
$result_user = mysqli_query($con, $query_user);
$user_info = mysqli_fetch_assoc($result_user);

// Initialize with fallback values from session if query returned null
if (!$user_info) {
    $user_info = [
        'full_name' => $_SESSION['user_name'] ?? 'User',
        'phone' => $_SESSION['phone'] ?? '',
        'email' => $_SESSION['email_id'] ?? '',
        'password' => '', // Password should not be in session for security
        'bank_name' => '',
        'account_no' => '',
        'ifsc_code' => '',
        'account_holder' => ''
    ];
}



// Fetch assigned coupons with product names and custom URLs
$query_coupons = "SELECT uc.*, c.coupon_code, c.p_id as coupon_p_id, p_c.p_name as coupon_p_name, p_direct.p_name as direct_p_name
                  FROM tbl_user_coupon uc 
                  LEFT JOIN tbl_coupon c ON uc.coupon_id = c.id 
                  LEFT JOIN tbl_product p_c ON c.p_id = p_c.p_id
                  LEFT JOIN tbl_product p_direct ON uc.p_id = p_direct.p_id
                  WHERE uc.user_id = '$user_id'";
$result_coupons = mysqli_query($con, $query_coupons);
$assigned_coupons = [];
if ($result_coupons) {
    while ($row = mysqli_fetch_assoc($result_coupons)) {
        $assigned_coupons[] = $row;
    }
}
// Note: $assigned_coupons now includes both coupon and non-coupon assignments
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Account | Dashboard</title>
    <link rel="icon" href="<?=BASE_URL;?>assets/images/logo-fav.png" type="image/png">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #ffb200 0%, #fd9800 100%);
            --secondary-gradient: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard-header {
            background: var(--primary-gradient);
            padding: 50px 0 80px 0;
            color: white;
            margin-bottom: -50px;
        }

        .profile-card {
            background: var(--glass-bg);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .nav-pills-custom .nav-link {
            color: #555;
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-right: 10px;
            border: 1px solid transparent;
        }

        .nav-pills-custom .nav-link.active {
            background: var(--primary-gradient);
            color: white !important;
            box-shadow: 0 5px 15px rgba(253, 152, 0, 0.3);
        }

        .nav-pills-custom {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }

        .nav-pills-custom::-webkit-scrollbar {
            display: none;
        }

        .stat-card {
            border-radius: 16px;
            padding: 20px;
            color: white;
            height: 100%;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .stat-value {
            font-size: 1.3rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .badge-commission {
            background: rgba(46, 125, 50, 0.1);
            color: #2e7d32;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-modern {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-modern-primary {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }

        .table-modern th {
            font-weight: 600;
            padding: 15px;
            background: #f1f3f5;
            border: none;
        }

        .table-modern td {
            padding: 15px;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <?php include("include/header.php"); ?>

    <div class="dashboard-header text-center">
        <div class="container">
            <h1 class="fw-bold mb-2">Welcome, <?= htmlspecialchars($user_info['full_name']); ?>!</h1>
            <p class="opacity-75">Partner Dashboard (ID: <?= $user_id; ?>)</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="profile-card">
                    <ul class="nav nav-pills nav-pills-custom mb-4 justify-content-center" id="pills-tab"
                        role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-profile" type="button"><i
                                    class="fas fa-user-circle me-2"></i>Personal Info</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="pills-address-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-address" type="button"><i
                                    class="fas fa-map-marker-alt me-2"></i>Addresses</button>
                        </li>
                        <?php if (count($assigned_coupons) > 0): ?>
                            <li class="nav-item">
                                <button class="nav-link" id="pills-commission-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-commission" type="button"><i
                                        class="fas fa-money-bill-wave me-2"></i>Commission</button>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item"><a href="my-orders.php" class="nav-link text-dark"><i
                                    class="fas fa-shopping-bag me-2"></i>Orders</a></li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">

                        <!-- Tab 1: Personal Info -->
                        <div class="tab-pane fade show active" id="pills-profile">
                            <div class="row justify-content-center">
                                <div class="col-md-9">
                                    <form action="update_profile.php" method="post">
                                        <div class="row g-3">
                                            <div class="col-md-6 text-start">
                                                <label class="form-label fw-bold small">Full Name</label>
                                                <input type="text" name="full_name" class="form-control"
                                                    value="<?= htmlspecialchars($user_info['full_name']); ?>">
                                            </div>
                                            <div class="col-md-6 text-start">
                                                <label class="form-label fw-bold small">Mobile Number</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="<?= htmlspecialchars($user_info['phone']); ?>">
                                            </div>
                                            <div class="col-md-6 text-start">
                                                <label class="form-label fw-bold small">Email Address</label>
                                                <input type="email" name="email"
                                                    value="<?= htmlspecialchars($user_info['email']); ?>"
                                                    class="form-control <?= !empty($user_info['email']) ? 'bg-light' : ''; ?>"
                                                    <?= !empty($user_info['email']) ? 'readonly' : ''; ?>>
                                            </div>
                                            <div class="col-md-6 text-start">
                                                <label class="form-label fw-bold small">Security Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    value="<?= htmlspecialchars($user_info['password']); ?>">
                                            </div>
                                            <div class="col-12 text-center mt-4">
                                                <button type="submit" name="update_personal_info"
                                                    class="btn btn-modern btn-modern-primary">Update Personal
                                                    Info</button>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <div class="p-3 bg-light rounded-4 text-start border shadow-sm">
                                                    <h6 class="fw-bold mb-3 text-primary"><i
                                                            class="fas fa-map-marked-alt me-2"></i>Your Saved Address
                                                    </h6>
                                                    <?php
                                                    $res_ba = mysqli_query($con, "SELECT * FROM tbl_billing_address WHERE user_id = '$user_id'");
                                                    if ($ba = mysqli_fetch_assoc($res_ba)) {
                                                        echo "<div class='fw-bold text-dark mb-1'>" . htmlspecialchars($ba['name']) . "</div>";
                                                        echo "<div class='text-muted small'>" . htmlspecialchars($ba['street_address']) . ", " . htmlspecialchars($ba['town']) . ", " . htmlspecialchars($ba['state']) . " - " . htmlspecialchars($ba['pincode']) . "</div>";
                                                        echo "<div class='text-muted small'><i class='fas fa-phone-alt me-1'></i>" . htmlspecialchars($ba['phone_no']) . "</div>";
                                                    } else {
                                                        echo "<span class='text-muted small'><i class='fas fa-info-circle me-1'></i>No address saved yet. Update it in the 'Addresses' tab.</span>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Addresses -->
                        <div class="tab-pane fade" id="pills-address">
                            <form action="update_profile.php" method="post">
                                <div class="row justify-content-center text-start">
                                    <div class="col-md-9">
                                        <div class="p-4 bg-light rounded-4 mb-3 border">
                                            <h5 class="fw-bold mb-4 text-primary"><i
                                                    class="fas fa-map-marker-alt me-2"></i>Address Details</h5>
                                            <?php
                                            $res_b = mysqli_query($con, "SELECT * FROM tbl_billing_address WHERE user_id = '$user_id'");
                                            $addr = mysqli_fetch_assoc($res_b) ?? [];
                                            ?>
                                            <div class="row g-3">
                                                <div class="col-md-6"><label class="form-label small fw-bold">Building /
                                                        Flat / House No</label><input type="text" name="u_building"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($addr['building_no'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6"><label class="form-label small fw-bold">Street
                                                        Address / Colony</label><input type="text" name="u_address"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($addr['street_address'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6"><label
                                                        class="form-label small fw-bold">Landmark</label><input
                                                        type="text" name="u_landmark" class="form-control"
                                                        value="<?= htmlspecialchars($addr['landmark'] ?? ''); ?>"
                                                        placeholder="Near ..."></div>
                                                <div class="col-md-6"><label class="form-label small fw-bold">Town /
                                                        City</label><input type="text" name="u_town"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($addr['town'] ?? ''); ?>"></div>
                                                <div class="col-md-6"><label
                                                        class="form-label small fw-bold">District</label><input
                                                        type="text" name="u_district" class="form-control"
                                                        value="<?= htmlspecialchars($addr['district'] ?? ''); ?>"></div>
                                                <div class="col-md-6"><label
                                                        class="form-label small fw-bold">State</label><input type="text"
                                                        name="u_state" class="form-control"
                                                        value="<?= htmlspecialchars($addr['state'] ?? ''); ?>"></div>
                                                <div class="col-md-6"><label
                                                        class="form-label small fw-bold">Pincode</label><input
                                                        type="text" name="u_pincode" class="form-control"
                                                        value="<?= htmlspecialchars($addr['pincode'] ?? ''); ?>"></div>
                                                <div class="col-md-6"><label class="form-label small fw-bold">GST No
                                                        (Optional)</label><input type="text" name="u_gst"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($addr['gst_no'] ?? ''); ?>"></div>
                                            </div>
                                        </div>
                                        <div class="text-center"><button type="submit" name="update_user_address"
                                                class="btn btn-modern btn-modern-primary">Save My Address</button></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 3: Commission -->
                        <div class="tab-pane fade" id="pills-commission">
                                <?php if ($assigned_coupons):
                                    // Get default percentage for referral sales
                                    $total_sales = 0;
                                    $total_sales = 0;
                                    $total_earned = 0;
                                    $total_paid = 0;
                                    
                                    // Calculate using the NEW precise commission table
                                    $q_stats = mysqli_query($con, "SELECT SUM(commission_amount) as earned FROM tbl_affiliate_commission WHERE user_id = '$user_id'");
                                    $total_earned = (float)(mysqli_fetch_assoc($q_stats)['earned'] ?? 0);

                                    // For total sales, we sum up orders linked to this partner
                                    $q_sales = mysqli_query($con, "SELECT SUM(o.p_actual_price * o.no_of_item) as val 
                                                                  FROM tbl_order o 
                                                                  JOIN tbl_affiliate_commission ac ON o.order_id = ac.order_id AND o.p_id = ac.p_id
                                                                  WHERE ac.user_id = '$user_id' AND o.order_status = 'Success'");
                                    $total_sales = (float)(mysqli_fetch_assoc($q_sales)['val'] ?? 0);

                                    // Total Paid
                                    $q_p = mysqli_query($con, "SELECT SUM(amount_paid) as paid FROM tbl_commission_payment WHERE user_id = '$user_id'");
                                    $total_paid += (float) (mysqli_fetch_assoc($q_p)['paid'] ?? 0);
                                    
                                    $balance = $total_earned - $total_paid;
                                ?>
                                <div class="row g-2 mb-4 text-start justify-content-center">
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <div class="stat-card earned" style="background: #1e3c72;">
                                            <div><span class="stat-label">Sales</span><span
                                                    class="stat-value">₹<?= number_format($total_sales, 0); ?></span></div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <div class="stat-card earned" style="background: #2a5298;">
                                            <div><span class="stat-label">Earned</span><span
                                                    class="stat-value">₹<?= number_format($total_earned, 0); ?></span></div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <div class="stat-card paid" style="background: #2e7d32;">
                                            <div><span class="stat-label">Received</span><span
                                                    class="stat-value">₹<?= number_format($total_paid, 0); ?></span></div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <div class="stat-card balance" style="background: #d31027;">
                                            <div><span class="stat-label">Pending</span><span
                                                    class="stat-value">₹<?= number_format($balance, 0); ?></span></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <?php if ($balance >= 2000): ?>
                                            <button type="button" class="btn btn-dark w-100 h-100 fw-bold py-3"
                                                data-bs-toggle="modal" data-bs-target="#payoutModal">
                                                <i class="fas fa-hand-holding-usd me-2"></i>Withdraw Earnings
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary w-100 h-100 fw-bold py-3" disabled title="Minimum ₹2,000 required to withdraw">
                                                <i class="fas fa-lock me-2"></i>Withdraw (Min ₹2,000)
                                            </button>
                                            <small class="text-danger d-block mt-1">Collect ₹<?= number_format(2000 - $balance, 0); ?> more to withdraw</small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-3 text-start mt-4"><i class="fas fa-ticket-alt me-2"></i>Assigned Products & Coupons Performance</h5>

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover text-start">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Coupon Code</th>
                                                <th>Product Link</th>
                                                <th>Comm. %</th>
                                                <th>Items Sold</th>
                                                <th>Sales Amount</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <?php
                                                foreach ($assigned_coupons as $uc) {
                                                    $code = $uc['coupon_code'] ?: '<i>URL Only</i>';
                                                    $query_cond = "1=1";
                                                    if (!empty($uc['coupon_code'])) {
                                                        $query_cond = "LOWER(applied_coupon) = LOWER('{$uc['coupon_code']}')";
                                                    } elseif (!empty($uc['p_id'])) {
                                                        $query_cond = "p_id = '{$uc['p_id']}'";
                                                    }
                                                    
                                                    $q = mysqli_query($con, "SELECT SUM(no_of_item) as qty, SUM(p_actual_price * no_of_item) as val FROM tbl_order WHERE $query_cond AND order_status = 'Success' AND commission_user_id = '$user_id'");
                                                    $d = mysqli_fetch_assoc($q);
                                                    $s_val = (float) ($d['val'] ?? 0);
                                                    $s_qty = (int) ($d['qty'] ?? 0);
                                                    $s_earn = ($s_val * (float) $uc['percentage']) / 100;

                                                    // Generate Link with Priority
                                                    $final_p_url = $base_url;
                                                    if(!empty($uc['custom_url'])) {
                                                        $final_p_url = $uc['custom_url'];
                                                    } else {
                                                        $p_name = !empty($uc['coupon_p_name']) ? $uc['coupon_p_name'] : $uc['direct_p_name'];
                                                        if(!empty($p_name)) {
                                                            $p_slug = strtolower(trim($p_name));
                                                            $p_slug = preg_replace('/[^a-z0-9]+/i', '-', $p_slug);
                                                            $p_slug = preg_replace('/-+/', '-', $p_slug);
                                                            $p_slug = trim($p_slug, '-');
                                                            $final_p_url .= "product/" . $p_slug;
                                                        }
                                                    }
                                                    $p_ref_url = $final_p_url . (strpos($final_p_url, '?') !== false ? '&' : '?') . "ref=" . $user_id;
                                                    ?>
                                                    <tr>
                                                        <td><span class="badge bg-primary px-3 py-2"><?= $code; ?></span></td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control" value="<?= $p_ref_url; ?>" id="ref_<?= $uc['id']; ?>" readonly>
                                                                <button class="btn btn-outline-secondary" type="button" onclick="copyRef('ref_<?= $uc['id']; ?>')"><i class="fas fa-copy"></i></button>
                                                            </div>
                                                        </td>
                                                        <td class="fw-bold"><?= $uc['percentage']; ?>%</td>
                                                        <td><?= $s_qty; ?> Items</td>
                                                        <td>₹<?= number_format($s_val, 0); ?></td>
                                                        <td class="text-success fw-bold">₹<?= number_format($s_earn, 2); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            

                                        </tbody>
                                    </table>
                                </div>
                                <script>
                                function copyRef(id) {
                                    var copyText = document.getElementById(id);
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999);
                                    navigator.clipboard.writeText(copyText.value);
                                    alert("Product Referral Link Copied!");
                                }
                                </script>

                                <h5 class="fw-bold mb-3 text-start mt-4"><i class="fas fa-history me-2"></i>Sales History
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-modern text-start">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Buyer</th>
                                                <th>Product</th>
                                                <th>Earning</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $has_sales = false;
                                            $query_h = "SELECT o.*, ac.commission_amount as earned_amt 
                                                         FROM tbl_order o 
                                                         JOIN tbl_affiliate_commission ac ON o.order_id = ac.order_id AND o.p_id = ac.p_id
                                                         WHERE ac.user_id = '$user_id' AND o.order_status = 'Success' 
                                                         ORDER BY o.id DESC";
                                            $res_h = mysqli_query($con, $query_h);
                                            if ($res_h && mysqli_num_rows($res_h) > 0) {
                                                while ($h = mysqli_fetch_assoc($res_h)) {
                                                    $has_sales = true;
                                                    $comm = (float)$h['earned_amt']; ?>
                                                    <tr>
                                                        <td>#<?= htmlspecialchars($h['order_id']); ?><br><small><?= htmlspecialchars($h['order_date']); ?></small>
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($h['b_name'] ?: 'Guest'); ?></strong><br>
                                                            <small
                                                                class="text-muted"><?= htmlspecialchars($h['b_phone'] ?? ''); ?></small>
                                                        </td>
                                                        <td><?= htmlspecialchars($h['p_name']); ?><br>
                                                            <?php if (!empty($h['applied_coupon'])): ?>
                                                                <span class='badge bg-primary'><?= htmlspecialchars($h['applied_coupon']); ?></span>
                                                            <?php else: ?>
                                                                <span class='badge bg-info'><i class="fas fa-link"></i> URL Link</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="badge-commission">₹<?= number_format($comm, 2); ?></div>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } 
                                            
                                            if (!$has_sales) {
                                                echo "<tr><td colspan='4' class='text-center py-4'>No sales recorded yet.</td></tr>";
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("include/footer.php"); ?>

    <!-- Payout Request Modal -->
    <div class="modal fade" id="payoutModal" tabindex="-1" aria-labelledby="payoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="payoutModalLabel">Request Payout (₹<?= number_format($balance, 2); ?>)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="payoutForm">
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i> Please ensure your bank details are correct for a
                            smooth transfer.
                        </div>
                        <input type="hidden" name="payout_amount" value="<?= $balance; ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Account Holder Name</label>
                            <input type="text" name="account_holder" class="form-control"
                                value="<?= htmlspecialchars($user_info['account_holder'] ?? $user_info['full_name']); ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control"
                                value="<?= htmlspecialchars($user_info['bank_name'] ?? ''); ?>" required
                                placeholder="e.g. SBI, HDFC">
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label small fw-bold">Account Number</label>
                                <input type="text" name="account_no" class="form-control"
                                    value="<?= htmlspecialchars($user_info['account_no'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label small fw-bold">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control"
                                    value="<?= htmlspecialchars($user_info['ifsc_code'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Send Request <i
                                class="fas fa-paper-plane ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#payoutForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.html();

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

                $.ajax({
                    url: 'process_payout.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                            btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function () {
                        alert('Something went wrong. Please try again.');
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
</body>

</html>