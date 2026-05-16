<?php require_once('header.php'); ?>

<?php
if(!isset($_GET['uid'])) {
    header('location: commission-report.php');
    exit;
}
$coupon_code = $_GET['coupon'] ?? '';
$user_id = $_GET['uid'];
$percentage = $_GET['percent'] ?? 0;
?>

<section class="content-header">
	<div class="content-header-left">
		<h1><i class="fa fa-history"></i> Commission History: <span class="text-success"><?= htmlspecialchars($coupon_code ?: 'URL Link'); ?></span></h1>
	</div>
	<div class="content-header-right">
		<a href="commission-report.php" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back to Report</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
                    <div class="well">
                        <p>Showing all products sold using <b><?= htmlspecialchars($coupon_code ?: 'URL Link'); ?></b> at <b><?= htmlspecialchars($percentage); ?>%</b> commission.</p>
                    </div>
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th width="30">#</th>
								<th>Order ID</th>
								<th>Date</th>
								<th>Product Name</th>
								<th>Quantity</th>
								<th>Price (Incl. GST)</th>
								<th>Commission</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
                            $total_comm = 0;
							$p_id = $_GET['pid'] ?? '';
							
                            // Fetch all products joined with their recorded commission
                            if (!empty($coupon_code)) {
                                $stmt = $pdo->prepare("SELECT o.*, ac.commission_amount 
                                                       FROM tbl_order o 
                                                       JOIN tbl_affiliate_commission ac ON o.order_id = ac.order_id AND o.p_id = ac.p_id
                                                       WHERE ac.user_id = ? AND LOWER(o.applied_coupon) = LOWER(?) AND o.order_status = 'Success' 
                                                       ORDER BY o.id DESC");
                                $stmt->execute([$user_id, $coupon_code]);
                            } elseif (!empty($p_id)) {
                                $stmt = $pdo->prepare("SELECT o.*, ac.commission_amount 
                                                       FROM tbl_order o 
                                                       JOIN tbl_affiliate_commission ac ON o.order_id = ac.order_id AND o.p_id = ac.p_id
                                                       WHERE ac.user_id = ? AND o.p_id = ? AND o.order_status = 'Success' 
                                                       ORDER BY o.id DESC");
                                $stmt->execute([$user_id, $p_id]);
                            } else {
                                $stmt = $pdo->prepare("SELECT o.*, ac.commission_amount 
                                                       FROM tbl_order o 
                                                       JOIN tbl_affiliate_commission ac ON o.order_id = ac.order_id AND o.p_id = ac.p_id
                                                       WHERE ac.user_id = ? AND o.order_status = 'Success' 
                                                       ORDER BY o.id DESC");
                                $stmt->execute([$user_id]);
                            }
                            
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
                                $row_price = $row['p_actual_price'];
                                $row_qty = $row['no_of_item'];
                                $row_total = $row_price * $row_qty;
                                $row_comm = (float)$row['commission_amount'];
                                $total_comm += $row_comm;
							?>
								<tr>
									<td><?= $i; ?></td>
									<td>
                                        <i class="fa fa-shopping-cart text-muted"></i> 
                                        <b><?= htmlspecialchars($row['order_id']); ?></b>
                                    </td>
									<td><i class="fa fa-calendar-o text-muted"></i> <?= htmlspecialchars($row['order_date']); ?></td>
									<td>
                                        <?= htmlspecialchars($row['p_name']); ?>
                                        <br>
                                        <small class="text-muted">SKU: <?= htmlspecialchars($row['sku']); ?></small>
                                    </td>
									<td><?= $row_qty; ?></td>
									<td>₹<?= number_format($row_total, 2); ?></td>
									<td>
                                        <span class="badge bg-green">
                                            ₹<?= number_format($row_comm, 2); ?>
                                        </span>
                                    </td>
								</tr>
							<?php
							}
							?>
						</tbody>
                        <tfoot>
                            <tr style="background: #f4f4f4; font-weight: bold;">
                                <td colspan="6" style="text-align: right;">Total Commission Earned:</td>
                                <td style="color: #2e7d32; font-size: 1.2em;">₹<?= number_format($total_comm, 2); ?></td>
                            </tr>
                        </tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
