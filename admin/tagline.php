<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Taglines</h1>
    </div>
    <div class="content-header-right">
        <a href="add-tagline.php" class="btn btn-primary btn-sm">Add Tagline</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">All Taglines</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tagline</th>
                                <th>created_date</th>
                            </tr>
                        </thead>
                        	<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT * from tbl_tagline order by id ASC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<!-- <td><?php echo $row['id']; ?></td> -->
									<td><?php echo $row['tagline']; ?></td>
									<td><?php echo $row['created_date']; ?></td>
													
											
									<td>										
										<a href="tagline-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<!-- <a href="#" class="btn btn-danger btn-xs" data-href="shipping-zone-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>   -->
										<!--<a href="#" class="btn btn-danger btn-xs" data-href="shipping-zone-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a> -->
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
