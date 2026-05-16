<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {

	$zone_name = $_POST['zone_name'];
	$state_name = $_POST['state_name'];
	$shipping_charge = $_POST['shipping_charge'];
	$free_shipping = $_POST['free_shipping'];
	

	$statement = $pdo->prepare("INSERT INTO tbl_shipping_zone (zone_name, state_name, shipping_charge, free_shipping) VALUES (?,?,?,?)");
	$statement->execute(array($zone_name, $state_name, $shipping_charge, $free_shipping));
	

	$success_message = 'Zone is added successfully.';
 
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Shipping Zone</h1>
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
							<label for="" class="col-sm-3 control-label">Zone Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="zone_name" class="form-control select2 top-cat">
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
								<input type="text" name="shipping_charge" class="form-control">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Free Shipping <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="free_shipping" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>