<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<div class="content-header-right">
		<a href="product-detail-add.php" class="btn btn-primary btn-sm">Add Product</a>
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
								<th>Photo</th>
								<th>Product Name</th>
								<th>SKU</th>
								<th>Qty</th>
								<th width="60">MRP</th>
								<th width="100">Selling Price</th>
								<th width="60">Weight</th>
								<!--<th width="60">p_sku</th>-->
								<th width="60">Color</th>
								<!-- <th width="60">photo</th> -->
								<th width="60">In Stoke</th>
								<!-- <th>Active?</th> -->
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT * from tbl_product_price order by id ASC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								$statement2 = $pdo->prepare("SELECT p_name from tbl_product where p_id='$row[p_id]'");
								$statement2->execute();
								$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
								foreach ($result2 as $row2) {
									$p_name = $row2['p_name'];
								}
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:82px;">
										<img src="../assets/img/product-detail/<?php echo $row['photo']; ?>" alt="<?php echo $p_name; ?>" style="width:80px;">
									</td>
									<td><?php echo $p_name; ?></td>
									<td><?php echo $row['p_sku']; ?></td>
									<td><?php echo $row['p_qty']; ?></td>
									<td><?php echo $row['p_old_price']; ?></td>					
									<td><?php echo $row['p_current_price']; ?></td>		
									<td><?php echo $row['p_weight']; ?></td>									
									<!--<td><?php echo $row['p_sku']; ?></td>									-->
									<td><?php echo $row['color']; ?></td>									
									<td><?php echo $row['in_stoke']; ?></td>
									<td>										
										<a href="product-detail-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Update</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="product-detail-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
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


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this item?</p>
                <p style="color:red;">Be careful! This product will be deleted from the order table, payment table, size table, color table and rating table also.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>