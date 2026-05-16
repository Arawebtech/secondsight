<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<div class="content-header-right">
		<a href="coupon-add.php" class="btn btn-primary btn-sm">Add coupon</a>
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
								<th>Global/Specific</th> 
								<th>Coupon Code</th>
								<th>Discount(%)</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i = 0;
								$statement = $pdo->prepare("SELECT c.*, p.p_name FROM tbl_coupon c 
															LEFT JOIN tbl_product p ON c.p_id = p.p_id 
															ORDER BY c.id ASC");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);

								foreach ($result as $row) {
									$i++;
									// Determine product display
									$product_name = ($row['p_id'] == 0) ? 'All Products (Global)' : htmlspecialchars($row['p_name']);
									// Determine discount type
									$discount_type = ($row['type'] == 'percent') ? '%' : '₹';
									?>
									<tr>
										<td><?= $i; ?></td>
										<td><?= $product_name; ?></td>
										<td><?= htmlspecialchars($row['coupon_code']); ?></td>
										<td><?= htmlspecialchars($row['amount']) . ' ' . $discount_type; ?></td>
										<td>
											<a href="coupon-edit.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
											<a href="#" class="btn btn-danger btn-xs" data-href="coupon-delete.php?id=<?= $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
										</td>
									</tr>
									<?php
								}
								?>
													
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
                <p style="color:red;">Be careful! This coupon will be deleted.</p>
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