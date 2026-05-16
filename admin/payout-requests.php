<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form_approve'])) {
	$id = $_POST['payout_id'];
	$statement = $pdo->prepare("UPDATE tbl_payout_requests SET status=? WHERE id=?");
	$statement->execute(array('Paid', $id));
	$success_message = 'Payout request approved successfully.';
}

if(isset($_POST['form_reject'])) {
	$id = $_POST['payout_id'];
	$statement = $pdo->prepare("UPDATE tbl_payout_requests SET status=? WHERE id=?");
	$statement->execute(array('Rejected', $id));
	$success_message = 'Payout request rejected successfully.';
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Payout Requests</h1>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if($error_message): ?>
			<div class="callout callout-danger">
				<p><?php echo $error_message; ?></p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
				<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">SL</th>
								<th>User Details</th>
								<th>Bank Account Info</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Date</th>
								<th width="150">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT pr.*, u.full_name, u.phone, u.bank_name, u.account_no, u.ifsc_code, u.account_holder 
														FROM tbl_payout_requests pr 
														JOIN tbl_user u ON pr.user_id = u.id 
														ORDER BY pr.id DESC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td>
										<b>Name:</b> <?php echo $row['full_name']; ?><br>
										<b>Phone:</b> <?php echo $row['phone']; ?>
									</td>
									<td>
										<b>Holder:</b> <?php echo $row['account_holder']; ?><br>
										<b>Bank:</b> <?php echo $row['bank_name']; ?><br>
										<b>A/C No:</b> <?php echo $row['account_no']; ?><br>
										<b>IFSC:</b> <?php echo $row['ifsc_code']; ?>
									</td>
									<td>₹<?php echo $row['amount']; ?></td>
									<td>
										<?php if($row['status'] == 'Pending'): ?>
											<span class="label label-warning">Pending</span>
										<?php elseif($row['status'] == 'Paid'): ?>
											<span class="label label-success">Paid</span>
										<?php else: ?>
											<span class="label label-danger">Rejected</span>
										<?php endif; ?>
									</td>
									<td><?php echo $row['request_date']; ?></td>
									<td>
										<?php if($row['status'] == 'Pending'): ?>
										<form method="post" action="" style="display:inline;">
											<input type="hidden" name="payout_id" value="<?php echo $row['id']; ?>">
											<button type="submit" name="form_approve" class="btn btn-success btn-xs" onclick="return confirm('Are you sure you want to approve this payout?')">Approve</button>
											<button type="submit" name="form_reject" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to reject this payout?')">Reject</button>
										</form>
										<?php else: ?>
											<span class="text-muted">Processed</span>
										<?php endif; ?>
									</td>
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
