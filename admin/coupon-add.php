<?php require_once('header.php');

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {

    // Trim inputs
    $p_id = trim($_POST['p_id']);
    $coupon_code = trim($_POST['coupon_code']);
    $amount = trim($_POST['amount']);
    $type = trim($_POST['type']); // 'flat' or 'percent'

    // Validate
    if ($p_id === '' || $coupon_code === '' || $amount === '' || $type === '') {
        $error_message = 'All fields are required.';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error_message = 'Amount must be a positive number.';
    } elseif (!in_array($type, ['flat','percent'])) {
        $error_message = 'Invalid discount type.';
    } else {

        // Check for duplicate coupon code for the same product/global
        $stmt = $pdo->prepare("SELECT * FROM tbl_coupon WHERE p_id = ? AND coupon_code = ?");
        $stmt->execute([$p_id, $coupon_code]);

        if ($stmt->rowCount() > 0) {
            $error_message = 'This coupon already exists.';
        } else {
            // Insert coupon
            $stmt = $pdo->prepare("INSERT INTO tbl_coupon (p_id, coupon_code, amount, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$p_id, $coupon_code, $amount, $type]);
            $success_message = 'Coupon added successfully.';
        }
    }
}
?>

<section class="content-header">
    <h1>Add New Coupon</h1>
</section>

<section class="content">
    <?php if ($error_message): ?>
        <div class="callout callout-danger"><?= $error_message; ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="callout callout-success"><?= $success_message; ?></div>
    <?php endif; ?>

    <form class="form-horizontal" action="" method="post">
        <div class="box box-info">
            <div class="box-body">

                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Product or Global <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="p_id" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="0">All Products (Global)</option>
                            <?php
                            $stmt = $pdo->query("SELECT p_id, p_name FROM tbl_product ORDER BY p_name ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="'.htmlspecialchars($row['p_id']).'">'.htmlspecialchars($row['p_name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Coupon Code <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" name="coupon_code" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Discount Type <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="type" class="form-control" required>
                            <option value="flat">Flat (₹)</option>
                            <option value="percent">Percentage (%)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Amount <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="number" name="amount" class="form-control" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-success" name="form1">Submit</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>


<?php require_once('footer.php'); ?>
