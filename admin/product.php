<?php require_once('header.php'); ?>





<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<!--<div class="content-header-right">-->
	<!--	<a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a-->
	<!--	<div style="float:right">-->
	<!--	<form action="" method="post">-->
 <!--         <button class="btn btn-success" name="download_report">Download</button>-->
 <!--       </form>-->
 <!--       	</div>-->
	<!--</div>-->
	<div class="content-header-right">
    <a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a>
    <div style="float:right">
        <form action="download-product.php" method="post">
            <button class="btn btn-success" name="download_report">Download</button>
        </form>
    </div>
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
								<th>Category</th>
								<th width="160">Product Name</th>
								<!-- <th width="60">SKU</th> -->
								<th width="60">GST(%)</th>
								<th width="60">LBH</th>
								<th>Active</th>
								<th>Trending</th>
								<th width="100">Product Detail</th>
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT
														
														t1.p_id,
														t1.p_name,
														t1.p_gst,
														t1.p_length,
														t1.p_width,
														t1.p_height,											
														t1.p_featured_photo,
														t1.p_is_active,
														t1.p_is_trending,
														t1.ecat_id,
														t2.ecat_id,
														t2.ecat_name,
														t3.mcat_id,
														t3.mcat_name,
														t4.tcat_id,
														t4.tcat_name

							                           	FROM tbl_product t1
							                           	JOIN tbl_end_category t2
							                           	ON t1.ecat_id = t2.ecat_id
							                           	JOIN tbl_mid_category t3
							                           	ON t2.mcat_id = t3.mcat_id
							                           	JOIN tbl_top_category t4
							                           	ON t3.tcat_id = t4.tcat_id
							                           	ORDER BY t1.p_id DESC
							                           	");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:82px;"><img src="../assets/img/product/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:80px;"></td>
									<td><?php echo $row['tcat_name']; ?><br><?php echo $row['mcat_name']; ?><br><?php echo $row['ecat_name']; ?></td>
									<td><?php echo $row['p_name']; ?></td>
									<td><?php echo $row['p_gst']; ?></td>
									<td>
										<?php echo $row['p_length']; ?>x<?php echo $row['p_width']; ?>x<?php echo $row['p_height']; ?>
									</td>									
									<td>
										<?php if($row['p_is_active'] == 1) {echo '<span class="badge badge-success" style="background-color:green;">Yes</span>';} else {echo '<span class="badge badge-danger" style="background-color:red;">No</span>';} ?>
									</td>
									<td>
									    <?php if($row['p_is_trending'] == 1) {echo '<span class="badge badge-success" style="background-color:green;">Yes</span>';} else {echo '<span class="badge badge-danger" style="background-color:red;">No</span>';} ?>
									</td>  
									<td>
										<a href="product-detail.php?p_id=<?php echo $row['p_id']; ?>">View Detail</a>
									</td>
									<td>										
										<a href="product-edit.php?id=<?php echo $row['p_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="product-delete.php?id=<?php echo $row['p_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
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