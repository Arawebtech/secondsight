<?php require_once('header.php'); 

if(isset($_GET['id'])) {
    $user_id = $_GET['id']; // Corrected to use 'id' instead of 'user_id'

    $user_statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id = :user_id");
    $user_statement->execute(array(':user_id' => $user_id));
    $user_result = $user_statement->fetch(PDO::FETCH_ASSOC);

    $billing_statement = $pdo->prepare("SELECT * FROM tbl_billing_address WHERE user_id = :user_id");
    $billing_statement->execute(array(':user_id' => $user_id));
    $billing_result = $billing_statement->fetch(PDO::FETCH_ASSOC);
    
    
    $shipping_statement = $pdo->prepare("SELECT * FROM tbl_shipping_address WHERE user_id = :user_id");
    $shipping_statement->execute(array(':user_id' => $user_id));
    $shipping_result = $shipping_statement->fetch(PDO::FETCH_ASSOC);
    
?>

<!--<section class="content-header">-->
<!--    <div class="content-header-left">-->
        
<!--    </div>-->
<!--</section>-->

<!-- Content Wrapper. Contains page content -->
<!--<div class="content-wrapper">-->
    <!--<section class="content-header">-->
    <!--    <P>-->
            <?php 
            /*$msg = "";
            if (!empty($msg)) {
                echo $msg;
            }
            if (isset($_GET['id']) && $_GET['id'] == 'Added') {
                echo '<p style="color:green;">New blog has been added successfully</p>';
            }
            if (isset($_GET['id']) and $_GET['id'] == 'Update') {
                echo '<p style="color:green;">Record has been updated successfully</p>';
            }*/
            ?>
    <!--    </P>-->
    <!--</section>-->
   

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body table-responsive">
                        <h3 class="box-title"></h3>
                        <!-- <div class="box-tools pull-right"> -->
                            <a href= "customer.php" class="btn btn-primary">Back</a>
                        <!-- </div> -->
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <tbody>
                                <tr><th><h4><b>User Details:</b></h4></th><th></th></tr>
                                <tr>
                                    <td>Name</td><td><?php echo $user_result['full_name']; ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td><td><?php echo $user_result['email']; ?></td>
                                </tr>
                                <tr>
                                    <td>Phone</td><td><?php echo $user_result['phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Referral Link</b></td>
                                    <td>
                                        <code id="refLink"><?php echo BASE_URL; ?>?ref=<?php echo $user_id; ?></code>
                                        <button class="btn btn-xs btn-default" onclick="copyRefLink()">Copy</button>
                                        <script>
                                        function copyRefLink() {
                                            var copyText = document.getElementById("refLink").innerText;
                                            navigator.clipboard.writeText(copyText);
                                            alert("Referral Link Copied!");
                                        }
                                        </script>
                                    </td>
                                </tr>
                                <!-- Add more user details if needed -->

                                <tr><th><h4><b>Billing Details:</b></h4></th><th></th></tr>
                                <tr>
                                    <td>Name</td><td><?php echo $billing_result['name']; ?></td>
                                </tr>
                                <tr>
                                    <td>Phone No</td><td><?php echo $billing_result['phone_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Building No</td><td><?php echo $billing_result['building_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Street Address</td><td><?php echo $billing_result['street_address']; ?></td>
                                </tr>
                                <tr>
                                    <td>Landmark</td><td><?php echo $billing_result['landmark']; ?></td>
                                </tr>
                                <tr>
                                    <td>Town</td><td><?php echo $billing_result['town']; ?></td>
                                </tr>
                                <tr>
                                    <td>District</td><td><?php echo $billing_result['district']; ?></td>
                                </tr>
                                <tr>
                                    <td>State</td><td><?php echo $billing_result['state']; ?></td>
                                </tr>
                                <tr>
                                    <td>Pincode</td><td><?php echo $billing_result['pincode']; ?></td>
                                </tr>
                                 <tr>
                                    <td>Gst No</td><td><?php echo $billing_result['gst_no']; ?></td>
                                </tr>
                                 <tr>
                                    <td>Address Type</td><td><?php echo $billing_result['AddressType']; ?></td>
                                </tr>
                                
                                 <tr><th><h4><b>Shipping Details:</b></h4></th><th></th></tr>
                                <tr>
                                    <td>Name</td><td><?php echo $shipping_result['name']; ?></td>
                                </tr>
                                <tr>
                                    <td>Phone No</td><td><?php echo $shipping_result['phone_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Building No</td><td><?php echo $shipping_result['building_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Street Address</td><td><?php echo $shipping_result['street_address']; ?></td>
                                </tr>
                                <tr>
                                    <td>Landmark</td><td><?php echo $shipping_result['landmark']; ?></td>
                                </tr>
                                <tr>
                                    <td>Town</td><td><?php echo $shipping_result['town']; ?></td>
                                </tr>
                                <tr>
                                    <td>District</td><td><?php echo $shipping_result['district']; ?></td>
                                </tr>
                                <tr>
                                    <td>State</td><td><?php echo $shipping_result['state']; ?></td>
                                </tr>
                                <tr>
                                    <td>Pincode</td><td><?php echo $shipping_result['pincode']; ?></td>
                                </tr>
                                 <tr>
                                    <td>Gst No</td><td><?php echo $shipping_result['gst_no']; ?></td>
                                </tr>
                                 <tr>
                                    <td>Address Type</td><td><?php echo $shipping_result['AddressType']; ?></td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            <!--</div>-->
        </div>
    </section>
</div>

<?php } // end if
require_once('footer.php'); ?>
