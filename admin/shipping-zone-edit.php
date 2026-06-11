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
		<h1>Edit Shipping Zone</h1>
	</div>
	<div class="content-header-right">
		<a href="shipping-zone.php" class="btn btn-primary btn-sm">View All</a>
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
									<option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
									<option value="Andhra Pradesh">Andhra Pradesh</option>
									<option value="Arunachal Pradesh">Arunachal Pradesh</option>
									<option value="Assam">Assam</option>
									<option value="Bihar">Bihar</option>
									<option value="Chandigarh">Chandigarh</option>
									<option value="Chhattisgarh">Chhattisgarh</option>
									<option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
									<option value="Delhi-NCR">Delhi-NCR</option>
									<option value="Goa">Goa</option>
									<option value="Gujarat">Gujarat</option>
									<option value="Haryana">Haryana</option>
									<option value="Himachal Pradesh">Himachal Pradesh</option>
									<option value="Jammu and Kashmir">Jammu and Kashmir</option>
									<option value="Jharkhand">Jharkhand</option>
									<option value="Karnataka">Karnataka</option>
									<option value="Kerala">Kerala</option>
									<option value="Ladakh">Ladakh</option>
									<option value="Lakshadweep">Lakshadweep</option>
									<option value="Madhya Pradesh">Madhya Pradesh</option>
									<option value="Maharashtra">Maharashtra</option>
									<option value="Manipur">Manipur</option>
									<option value="Meghalaya">Meghalaya</option>
									<option value="Mizoram">Mizoram</option>
									<option value="Nagaland">Nagaland</option>
									<option value="Odisha">Odisha</option>
									<option value="Puducherry">Puducherry</option>
									<option value="Punjab">Punjab</option>
									<option value="Rajasthan">Rajasthan</option>
									<option value="Sikkim">Sikkim</option>
									<option value="Tamil Nadu">Tamil Nadu</option>
									<option value="Telangana">Telangana</option>
									<option value="Tripura">Tripura</option>
									<option value="Uttar Pradesh">Uttar Pradesh</option>
									<option value="Uttarakhand">Uttarakhand</option>
									<option value="West Bengal">West Bengal</option>
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