<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Payment</h1>
	</div>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover table-striped">
			<thead>
			    <tr>
			        <th>#</th>
                    <th>User Id</th>
                    <th>Order Id</th>
                    <th>Amount</th>
                    <th>Discount</th>
                    <th>Shipping Charge</th>
                    <th>Paid Amount</th>
                    <th>Payment Id</th>
                    <th>Payment Order Id</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
			    </tr>
			</thead>
            <tbody>
            	<?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * FROM tbl_payment ORDER by id DESC");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<!--<tr class="<?php if($row['payment_status']=='Pending'){echo 'bg-r';}else{echo 'bg-g';} ?>">-->
					<tr class="<?php echo (
        $row['payment_status'] === 'Pending' || 
        $row['payment_status'] === 'Failed'
    ) ? 'bg-r' : 'bg-g'; ?>">
	                    <td><?php echo $i; ?></td>
	                    <td><a href="user-details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['user_id']; ?></a></td>

                         <!--<td><a href="user-details.php?id=<?php echo $row['user_id']; ?><?php echo $row['user_id']; ?></a></td>-->
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td><?php echo $row['discount']; ?></td>
                        <td><?php echo $row['shipping_charge']; ?></td>
                        <td><?php echo $row['payable_amount']; ?></td> 
                        <td><?php echo $row['payment_id']; ?></td> 
                        <td><?php echo $row['payment_order_id']; ?></td> 
                        <td><?php echo $row['payment_method']; ?></td> 
                        <td><?php echo $row['payment_status']; ?></td> 
                        <td><?php echo $row['payment_date']; ?></td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
        </div>
      </div>
</section>

<?php require_once('footer.php'); ?>