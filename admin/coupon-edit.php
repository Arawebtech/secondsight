<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

// Fetch coupon by ID
$statement = $pdo->prepare("SELECT * FROM tbl_coupon WHERE id=?");
$statement->execute([$_REQUEST['id']]);
$coupon = $statement->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
    die("Invalid coupon ID.");
}

$p_id = $coupon['p_id'];
$coupon_code = $coupon['coupon_code'];
$amount = $coupon['amount'];
$type = $coupon['type']; // 'flat' or 'percent'

if (isset($_POST['form1'])) {
    $p_id = trim($_POST['p_id']);
    $coupon_code = trim($_POST['coupon_code']);
    $amount = trim($_POST['amount']);
    $type = trim($_POST['type']);

    // Validation
    if ($p_id === '' || $coupon_code === '' || $amount === '' || $type === '') {
        $error_message = 'All fields are required.';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error_message = 'Amount must be a positive number.';
    } elseif (!in_array($type, ['flat','percent'])) {
        $error_message = 'Invalid discount type.';
    } else {
        // Check for duplicate coupon excluding current
        $statement = $pdo->prepare("SELECT * FROM tbl_coupon WHERE p_id = ? AND coupon_code = ? AND id != ?");
        $statement->execute([$p_id, $coupon_code, $_REQUEST['id']]);

        if ($statement->rowCount() > 0) {
            $error_message = 'This coupon already exists.';
        } else {
            // Update coupon
            $statement = $pdo->prepare("UPDATE tbl_coupon SET p_id=?, coupon_code=?, amount=?, type=? WHERE id=?");
            $statement->execute([$p_id, $coupon_code, $amount, $type, $_REQUEST['id']]);
            $success_message = 'Coupon updated successfully.';
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Coupon</h1>
    </div>
    <div class="content-header-right">
        <a href="coupon.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
                <div class="callout callout-danger"><p><?= $error_message; ?></p></div>
            <?php endif; ?>

            <?php if($success_message): ?>
                <div class="callout callout-success"><p><?= $success_message; ?></p></div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">

                        <!-- Product / Global -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Select Product or Global <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="p_id" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="0" <?= ($p_id == 0 ? 'selected' : '') ?>>All Products (Global)</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT p_id, p_name FROM tbl_product ORDER BY p_name ASC");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($row['p_id'] == $p_id) ? 'selected' : '';
                                        echo '<option value="'.htmlspecialchars($row['p_id']).'" '.$selected.'>'.htmlspecialchars($row['p_name']).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Coupon Code <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="coupon_code" class="form-control" value="<?= htmlspecialchars($coupon_code); ?>" required>
                            </div>
                        </div>

                        <!-- Discount Type -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Discount Type <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="type" class="form-control" required>
                                    <option value="flat" <?= ($type=='flat'?'selected':'') ?>>Flat (₹)</option>
                                    <option value="percent" <?= ($type=='percent'?'selected':'') ?>>Percentage (%)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Amount <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="number" name="amount" class="form-control" value="<?= htmlspecialchars($amount); ?>" min="1" required>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <button type="submit" class="btn btn-success" name="form1">Update</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>
