<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Order</h1>
    </div>
</section>

<?php
if (!isset($_GET['order_id'])) {
    die("No order ID provided");
}

$order_id = $_GET['order_id'];

// 1. Get order info + customer details
$sql_order_info = "
    SELECT o.*, u.full_name, u.email, u.phone
    FROM tbl_order o
    LEFT JOIN tbl_user u ON o.user_id = u.id
    WHERE o.order_id = ?
    LIMIT 1
";
$stmt = $pdo->prepare($sql_order_info);
$stmt->execute([$order_id]);
$order_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order_info) {
    die("Order not found");
}

// 2. Get all products in this order
$sql_items = "SELECT * FROM tbl_order WHERE order_id = ?";
$stmt = $pdo->prepare($sql_items);
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch discount_amount from tbl_payment for the order
$sql_payment = "SELECT shipping_charge, discount FROM tbl_payment WHERE order_id = ?";
$stmt = $pdo->prepare($sql_payment);
$stmt->execute([$order_id]);
$payment_info = $stmt->fetch(PDO::FETCH_ASSOC);


// Calculate totals (GST already included in p_actual_price)
$total_subtotal = 0;
foreach ($items as $item) {
    $total_subtotal += ($item['p_actual_price'] * $item['no_of_item']);
}

// Fetch shipping & discount from order_info
$shipping_cost  = !empty($payment_info['shipping_charge']) ? $payment_info['shipping_charge'] : 0;
$discount_amount = !empty($payment_info['discount']) ? $payment_info['discount'] : 0;
$grand_total = $total_subtotal + $shipping_cost - $discount_amount;
?>

<style>
  @media print {
    body { margin: 10mm; font-family: Arial, sans-serif; font-size: 11pt; color:#000; }
    table { width:100%; border-collapse: collapse; font-size: 10pt; }
    th, td { border:1px solid #444; padding:6px 8px; }
    th { background:#000; color:#fff; font-weight:bold; text-align:left; }
    .right { text-align:right; }
    h1, h2, h3, h4 { margin:0; }
    @page { size: A4 portrait; margin: 10mm; }
    .print-button { display:none; }
  }
  /* Screen preview */
  table { border-collapse: collapse; margin-bottom: 15px; }
  th, td { border:1px solid #ddd; padding:6px 8px; }
  th { background:#000; color:#fff; }
  .right { text-align:right; }
  .print-button { margin-bottom:15px; padding:8px 15px; font-size:14px; cursor:pointer; }
</style>

<div style="padding:20px; font-family:Arial, sans-serif;">
  <button class="print-button" onclick="window.print()">🖨️ Print Invoice</button>

  <!-- Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px;">
    <div>
      <img src="../assets/img/admin/user-1.png" alt="Company Logo" style="max-height:80px;">
    </div>
    <div style="text-align:left; font-size:12px;">
      <strong>Second Sight Foundation – Delhi</strong><br>
      SECOND SIGHT FOUNDATION AE 10 TAGORE<br>
      GARDEN G/F DELHI 110027<br>
      GSTN-07HAAPS1767P2ZW
    </div>
  </div>

  <!-- Invoice title -->
  <h3 style="margin:0 0 20px 0;">INVOICE</h3>

    <!-- 3 Column Info -->
  <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px; margin-bottom:20px; font-size:12px;">
    <div>
      <strong>Billing Address:</strong><br>
      <?php if (!empty($order_info['b_name'])): ?>
        <?= htmlspecialchars($order_info['b_name']) ?><br>
        <?= htmlspecialchars($order_info['b_phone']) ?><br>
        <?= htmlspecialchars($order_info['b_building']) ?>, <?= htmlspecialchars($order_info['b_street']) ?><br>
        <?= !empty($order_info['b_landmark']) ? htmlspecialchars($order_info['b_landmark']).'<br>' : '' ?>
        <?= htmlspecialchars($order_info['b_town']) ?>, <?= htmlspecialchars($order_info['b_district']) ?><br>
        <?= htmlspecialchars($order_info['b_state']) ?> - <?= htmlspecialchars($order_info['b_pincode']) ?><br>
        <?= !empty($order_info['b_gst']) ? 'GST: '.htmlspecialchars($order_info['b_gst']) : '' ?>
      <?php else: ?>
        <?php
        $billing_parts = explode(',', $order_info['billing_address']);
        $billing_name = isset($billing_parts[0]) ? trim($billing_parts[0]) : '';
        $billing_rest = count($billing_parts) > 1 ? implode(', ', array_slice($billing_parts, 1)) : '';
        ?>
        <strong><?= htmlspecialchars($billing_name) ?></strong><br>
        <?= nl2br(htmlspecialchars($billing_rest)) ?>
      <?php endif; ?>
    </div>

    <div>
      <strong>Shipping Address:</strong><br>
      <?php if (!empty($order_info['s_name'])): ?>
        <?= htmlspecialchars($order_info['s_name']) ?><br>
        <?= htmlspecialchars($order_info['s_phone']) ?><br>
        <?= htmlspecialchars($order_info['s_building']) ?>, <?= htmlspecialchars($order_info['s_street']) ?><br>
        <?= !empty($order_info['s_landmark']) ? htmlspecialchars($order_info['s_landmark']).'<br>' : '' ?>
        <?= htmlspecialchars($order_info['s_town']) ?>, <?= htmlspecialchars($order_info['s_district']) ?><br>
        <?= htmlspecialchars($order_info['s_state']) ?> - <?= htmlspecialchars($order_info['s_pincode']) ?><br>
        <?= !empty($order_info['s_gst']) ? 'GST: '.htmlspecialchars($order_info['s_gst']) : '' ?>
      <?php else: ?>
        <?php
        $shipping_parts = explode(',', $order_info['shipping_address']);
        $shipping_name = isset($shipping_parts[0]) ? trim($shipping_parts[0]) : '';
        $shipping_rest = count($shipping_parts) > 1 ? implode(', ', array_slice($shipping_parts, 1)) : '';
        ?>
        <strong><?= htmlspecialchars($shipping_name) ?></strong><br>
        <?= nl2br(htmlspecialchars($shipping_rest)) ?>
      <?php endif; ?>
    </div>

    <!-- Invoice + Order -->
    <div>
      <strong style="display:inline-block; width:100px;">Invoice Number:</strong> <?= htmlspecialchars($order_info['order_id']) ?><br>
      <strong style="display:inline-block; width:100px;">Invoice Date:</strong> <?= ($d = DateTime::createFromFormat('d/m/Y h:i:sa', $order_info['order_date'])) ? $d->format('F j, Y') : 'Date not available'; ?><br>
      <strong style="display:inline-block; width:100px;">Order Number:</strong> <?= htmlspecialchars($order_info['order_id']) ?><br>
      <strong style="display:inline-block; width:100px;">Order Date:</strong> <?= ($d = DateTime::createFromFormat('d/m/Y h:i:sa', $order_info['order_date'])) ? $d->format('F j, Y') : 'Date not available'; ?><br>
      <strong style="display:inline-block; width:100px;">Payment Method:</strong> Online
    </div>
  </div>
  
  
  <!-- Products Table -->
  <table width="100%">
    <thead>
      <tr>
        <th style="width:70%">Product</th>
        <th style="width:15%">Quantity</th>
        <th style="width:15%">Price</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['p_name']) ?></td>
        <td class="right"><?= $item['no_of_item'] ?></td>
        <td class="right">₹<?= number_format($item['p_actual_price'] * $item['no_of_item'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Totals -->
  <div style="width:300px; margin-left:auto; font-size:12px; margin-top:10px;">
    <table>
      <tbody>
        <tr>
          <th>Subtotal</th>
          <td class="right">₹<?= number_format($total_subtotal, 2) ?></td>
        </tr>
        <?php if ($shipping_cost > 0): ?>
        <tr>
          <th>Shipping (via Flat rate)</th>
          <td class="right">₹<?= number_format($shipping_cost, 2) ?> </td>
        </tr>
        <?php endif; ?>
        
        <tr>
          <th>Total</th>
          <td class="right"><strong>₹<?= number_format($grand_total, 2) ?></strong></td>
        </tr>
        <tr>
          <th>Payment Mode</th>
          <td class="right">Online</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

  
<div style="clear: both;"></div>
<div class="print-only" style="display:flex; justify-content:center; align-items:center; height:30vh; margin-top:50px; flex-direction:column;">
  <hr style="width:60%; margin-bottom:30px;">
  <div style="font-size:18px; font-weight:bold;">THANK YOU FOR SHOPPING WITH US!</div>
</div>
<style>
  @media screen {
    .print-only { display: none !important; }
  }
  @media print {
    .print-only { display: flex !important; }
  }
</style>
<?php include("footer.php"); ?>
