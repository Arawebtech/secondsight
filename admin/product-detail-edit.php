<?php require_once('header.php'); ?>

<?php
// echo $_REQUEST['id']; exit;
if(isset($_POST['form1'])) {
    $valid = 1;

    $current_photo = $_POST['current_photo'];
    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];

    $final_name = $current_photo; // default to existing photo

    if($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if( !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ) {
            $valid = 0;
            $error_message .= 'You must upload jpg, jpeg, png, gif, or webp format only.<br>';
        } else {
            // Delete old file if exists
            if(file_exists('../assets/img/product-detail/'.$current_photo)) {
                unlink('../assets/img/product-detail/'.$current_photo);
            }
            // Generate new filename and move
            $final_name = 'product-featured-'.$_REQUEST['id'].'.'.$ext;
            move_uploaded_file($path_tmp, '../assets/img/product-detail/'.$final_name);
        }
    }

    if($current_photo == '' && $path == '') {
        $valid = 0;
        $error_message .= 'You must upload or select a photo.<br>';
    }

    if($valid == 1) {
        // Now use $final_name in your update query
        $statement = $pdo->prepare("UPDATE tbl_product_price SET 
            p_id = ?, 
            p_qty = ?, 
            p_old_price = ?, 
            p_current_price = ?,
            p_weight = ?,
            p_sku = ?,
            color = ?,
            photo = ?,
            in_stoke = ?
            WHERE id = ?");
        $statement->execute([
            $_POST['p_name'],
            $_POST['p_quantity'],
            $_POST['p_old_price'],
            $_POST['p_current_price'],
            $_POST['p_weight'],
            $_POST['p_sku'],
            $_POST['color'],
            $final_name,
            $_POST['in_stoke'],
            $_REQUEST['id']
        ]);

        $success_message = 'Product updated successfully.';
    }
}


?>

<?php
/*if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
    // echo $_REQUEST['id'].'hi'; exit;
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}*/
// echo $_REQUEST['id'].'hiiii'; exit;
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Product</h1>
	</div>
	<div class="content-header-right">
		<a href="product.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_product_price WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$p_id = $row['p_id'];
	$p_sku = $row['p_sku'];
	$p_qty = $row['p_qty'];
	$p_old_price = $row['p_old_price'];
	$p_current_price = $row['p_current_price'];
	$p_weight = $row['p_weight'];
	$in_stoke = $row['in_stoke'];
	$photo = $row['photo'];
	$color = $row['color'];

	
	$statement2 = $pdo->prepare("SELECT p_name FROM tbl_product WHERE p_id=?");
	$statement2->execute(array($row['p_id']));
	$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result2 as $row2) {
		$p_name = $row2['p_name'];
	}
}

?>


<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
			
			<p>
			<?php echo $error_message; ?>
			</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
			
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<!-- <input type="hidden" name="price_id_1" value="<?= $price_id_1; ?>">
				<input type="hidden" name="price_id_2" value="<?= $price_id_2; ?>">
				<input type="hidden" name="price_id_3" value="<?= $price_id_3; ?>"> -->
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="p_name" class="form-control select2 top-cat">
									<?php 
									if($p_name == "")
									{
										$key = "";
										$value = "Select Product Name";
									}	
									else
									{
										$key = $p_id;
										$value = $p_name;
									}					
									?>
									<option value="<?= $key; ?>"><?= $value; ?></option>
									<?php
									$statement = $pdo->prepare("SELECT p_id, p_name FROM tbl_product ORDER BY p_id DESC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);	
									foreach ($result as $row) {
										?>
										<option value="<?php echo $row['p_id']; ?>">
											<?php echo $row['p_name']; ?>
										</option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">SKU <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="p_sku" class="form-control" value="<?= $p_sku; ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Quantity<br>
								<!-- <span style="font-size:10px;font-weight:normal;"></span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_quantity" class="form-control" value="<?= $p_qty ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">MRP<br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In USD)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_old_price" class="form-control" value="<?= $p_old_price ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Selling Price<span>*</span><br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In USD)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_current_price" class="form-control" value="<?= $p_current_price ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Weight<br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In Gram)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_weight" class="form-control" value="<?= $p_weight ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Items in Stoke <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="in_stoke" class="form-control" value="<?= $in_stoke ?>">
							</div>
						</div>						
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Select Color</label>
							<div class="col-sm-4">
								<select name="color" class="form-control select2">
									<?php 
									if($color == "")
									{
										$key = "";
										$value = "Select Color";
									}	
									else
									{
										$key = $color;
										$value = $color;
									}					
									?>
									<option value="<?= $key; ?>"><?= $key; ?></option>
									<?php
									$statement = $pdo->prepare("SELECT * FROM tbl_color ORDER BY color_id ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);			
									foreach ($result as $row) 
									{ ?>
										<option value="<?php echo $row['color_name']; ?>">
											<?php echo $row['color_name']; ?>
										</option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Featured Photo <span>*</span></label>
							<div class="col-sm-4" style="padding-top:4px;">
								<input type="file" name="p_featured_photo">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Existing Featured Photo</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<img src="../assets/img/product-detail/<?php echo $photo; ?>" alt="" style="width:150px;">
								<input type="hidden" name="current_photo" value="<?php echo $photo; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update Product</button>
							</div>
						</div>
					</div>
				</div>

			</form>


		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>