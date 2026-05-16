<?php require_once('header.php'); ?>

<?php
// echo $_REQUEST['id']; exit;
if(isset($_POST['form1'])) {
	
    	
    	$statement = $pdo->prepare("UPDATE tbl_shipping_zone SET 
    							zone_name=?, 
    							state_name=?, 
    							shipping_charge=?, 
    							free_shipping=?  							

    							WHERE id=?");
    	$statement->execute(array(
    							$_POST['zone_name'],
    							$_POST['state_name'],
    							$_POST['shipping_charge'],
    							$_POST['free_shipping'],
    							
    							$_REQUEST['id']
    						));
        
		
	
    	$success_message = 'Zone is updated successfully.';
    
}

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
$statement = $pdo->prepare("SELECT * FROM tbl_shipping_zone WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) 
{
	$zone_name = $row['zone_name'];
	$state_name = $row['state_name'];
	$shipping_charge = $row['shipping_charge'];
	$free_shipping	 = $row['free_shipping'];
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
				<input type="hidden" name="price_id_1" value="<?= $price_id_1; ?>">
				<input type="hidden" name="price_id_2" value="<?= $price_id_2; ?>">
				<input type="hidden" name="price_id_3" value="<?= $price_id_3; ?>">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Zone Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="zone_name" class="form-control select2 top-cat">
									<option value="<?= $zone_name; ?>"><?= $zone_name; ?></option>
									<option value="">Select Zone Name</option>
									<option value="Local">Local</option>
									<option value="Regional">Regional</option>
									<option value="National">National</option>
									<option value="Rest of India">Rest of India</option>								
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">State Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="state_name" class="form-control select2 top-cat">
									<option value="<?= $state_name; ?>"><?= $state_name; ?></option>
									<option value="">Select State Name</option>
									<option value="Haryana">Haryana</option>
									<option value="Regional">Regional</option>
									<option value="National">National</option>
									<option value="Rest of India">Rest of India</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Shipping Charge <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="shipping_charge" class="form-control" value="<?= $shipping_charge; ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Free Shipping<br>
								<!-- <span style="font-size:10px;font-weight:normal;"></span> -->
							</label>
							<div class="col-sm-4">
								<input type="text" name="free_shipping" class="form-control" value="<?= $free_shipping ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
							</div>
						</div>
					</div>
				</div>

			</form>


		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>