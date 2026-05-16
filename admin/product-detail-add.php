<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    $path = $_FILES['p_featured_photo']['name'];
    $path_tmp = $_FILES['p_featured_photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' && $ext!='webp') {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must have to select a featured photo<br>';
    }

    if($valid == 1) {

    	$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$ai_id=$row[10];
		}
		
		/*$next_id1="";
    	if( isset($_FILES['photo']["name"]) && isset($_FILES['photo']["tmp_name"]) )
        {
        	$photo = array();
            $photo = $_FILES['photo']["name"];
            $photo = array_values(array_filter($photo));

        	$photo_temp = array();
            $photo_temp = $_FILES['photo']["tmp_name"];
            $photo_temp = array_values(array_filter($photo_temp));

        	$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product_price'");
			$statement->execute();
			$result = $statement->fetchAll();
			foreach($result as $row) {
				$next_id1=$row[10];
			}
			$z = $next_id1;

            $m=0;
            for($i=0;$i<count($photo);$i++)
            {
                $my_ext1 = pathinfo( $photo[$i], PATHINFO_EXTENSION );
		        if( $my_ext1=='jpg' || $my_ext1=='png' || $my_ext1=='jpeg' || $my_ext1=='gif' || $my_ext1=='webp') {
		            $final_name1[$m] = $z.'.'.$my_ext1;
                    move_uploaded_file($photo_temp[$i],"../assets/img/product-detail/".$final_name1[$m]);
                    $m++;
                    $z++;
		        }
            }           
        }*/


        $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_product_price'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$next_id1=$row[10];
		}


		$final_name = 'product-featured-'.$next_id1.'.'.$ext;
        move_uploaded_file( $path_tmp, '../assets/img/product-detail/'.$final_name );
        

		/*foreach($_POST['color'] as $value) {
			$statement = $pdo->prepare("INSERT INTO tbl_product_color (color_id,p_id) VALUES (?,?)");
			$statement->execute(array($value,$ai_id));
		}*/


		$p_name = $_POST['p_name'];
		$p_sku = $_POST['p_sku'];
		$p_qty = $_POST['p_quantity'];
		$p_old_price = $_POST['p_old_price'];
		$p_current_price = $_POST['p_current_price'];
		$p_weight = $_POST['p_weight'];
		$in_stoke = $_POST['in_stoke'];
		$color = $_POST['color'];
		// $p_featured_photo = $_POST['p_featured_photo'];

		$statement = $pdo->prepare("INSERT INTO tbl_product_price (p_id, p_sku, p_qty, p_old_price, p_current_price, p_weight, color, photo, in_stoke) VALUES (?,?,?,?,?,?,?,?,?)");
		$statement->execute(array($p_name, $p_sku, $p_qty, $p_old_price, $p_current_price, $p_weight, $color, $final_name, $in_stoke));
		

    	$success_message = 'Product is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Product</h1>
	</div>
	<div class="content-header-right">
		<a href="product.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


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

				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="p_name" class="form-control select2 top-cat">
									<option value="">Select Product Name</option>
									<?php
									$statement = $pdo->prepare("SELECT p_id, p_name FROM tbl_product ORDER BY p_id DESC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);	
									foreach ($result as $row) {
										?>
										<option value="<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">SKU <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="p_sku" class="form-control">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Quantity<br>
								<!-- <span style="font-size:10px;font-weight:normal;"></span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_quantity" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">MRP<br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In USD)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_old_price" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Selling Price<span>*</span><br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In USD)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_current_price" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Weight<br>
								<!-- <span style="font-size:10px;font-weight:normal;">(In Gram)</span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="p_weight" class="form-control">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Items in Stoke <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="in_stoke" class="form-control">
							</div>
						</div>						
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Select Color</label>
							<div class="col-sm-4">
								<select name="color" class="form-control select2">
									<option value="">Select Color</option>
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
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Add Product</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>