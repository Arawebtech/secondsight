<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View User Coupons</h1>
	</div>
	<div class="content-header-right">
		<a href="user-coupon-add.php" class="btn btn-primary btn-sm">Add User Coupon</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th width="10">#</th>
								<th>User Name</th>
								<th>Coupon Code</th>
								<th>Commission (%)</th>
								<th>Referral Link</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							$statement = $pdo->prepare("SELECT uc.*, u.full_name as user_name, c.coupon_code, c.p_id as coupon_p_id, p_c.p_name as coupon_p_name, p_direct.p_name as direct_p_name
															FROM tbl_user_coupon uc 
															JOIN tbl_user u ON uc.user_id = u.id 
															LEFT JOIN tbl_coupon c ON uc.coupon_id = c.id 
															LEFT JOIN tbl_product p_c ON c.p_id = p_c.p_id
															LEFT JOIN tbl_product p_direct ON uc.p_id = p_direct.p_id
															ORDER BY uc.id ASC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
								
								// Generate Referral URL with Priority
								$final_url = BASE_URL;
								
								if(!empty($row['custom_url'])) {
									// 1. Custom URL
									$final_url = $row['custom_url'];
								} else {
									// 2. Coupon's Product or Direct Product
									$p_name = !empty($row['coupon_p_name']) ? $row['coupon_p_name'] : $row['direct_p_name'];
									if(!empty($p_name)) {
										$slug = strtolower(trim($p_name));
										$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
										$slug = preg_replace('/-+/', '-', $slug);
										$slug = trim($slug, '-');
										$final_url .= "product/" . $slug;
									}
								}
								
								$ref_url = $final_url . (strpos($final_url, '?') !== false ? '&' : '?') . "ref=" . $row['user_id'];
							?>
								<tr>
									<td><?= $i; ?></td>
									<td><?= htmlspecialchars($row['user_name'] ?? ''); ?></td>
									<td><?= htmlspecialchars($row['coupon_code'] ?? ''); ?></td>
									<td><?= htmlspecialchars($row['percentage'] ?? '0'); ?>%</td>
									<td>
										<small><code id="ref_<?= $row['id']; ?>"><?= $ref_url; ?></code></small>
										<button class="btn btn-xs btn-default" onclick="copyText('ref_<?= $row['id']; ?>')">Copy</button>
									</td>
									<td>
										<a href="user-coupon-edit.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="user-coupon-delete.php?id=<?= $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
									</td>
								</tr>
							<?php
							}
							?>
							
							<script>
							function copyText(id) {
								var text = document.getElementById(id).innerText;
								navigator.clipboard.writeText(text);
								alert("Referral Link Copied!");
							}
							</script>

						</tbody>
					</table>
					<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
								</div>
								<div class="modal-body">
									<p>Are you sure want to delete this item?</p>
									<p style="color:red;">Be careful! This assignment will be deleted.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<a class="btn btn-danger btn-ok">Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	$('#confirm-delete').on('show.bs.modal', function(e) {
		var deleteUrl = $(e.relatedTarget).data('href'); // get data-href from clicked button
		$(this).find('.btn-ok').attr('href', deleteUrl);
	});
</script>

<?php require_once('footer.php'); ?>