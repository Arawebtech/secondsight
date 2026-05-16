<?php require_once('header.php');

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {

    $user_id = trim($_POST['user_id']);
    $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : null;
    $p_id = !empty($_POST['p_id']) ? $_POST['p_id'] : null;
    $custom_url = !empty($_POST['custom_url']) ? trim($_POST['custom_url']) : null;
    $percentage = trim($_POST['percentage']);

    if ($user_id === '' || $percentage === '') {
        $error_message = 'User and Percentage are required.';
    } elseif (!is_numeric($percentage) || $percentage < 0) {
        $error_message = 'Percentage must be a non-negative number.';
    } else {
        // Check for duplicate assignment
        $stmt = $pdo->prepare("SELECT * FROM tbl_user_coupon WHERE user_id = ? AND (coupon_id = ? OR (coupon_id IS NULL AND ? IS NULL)) AND (p_id = ? OR (p_id IS NULL AND ? IS NULL))");
        $stmt->execute([$user_id, $coupon_id, $coupon_id, $p_id, $p_id]);

        if ($stmt->rowCount() > 0) {
            $error_message = 'This specific assignment already exists for this user.';
        } else {
            // Insert assignment
            $stmt = $pdo->prepare("INSERT INTO tbl_user_coupon (user_id, coupon_id, p_id, custom_url, percentage) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $coupon_id, $p_id, $custom_url, $percentage]);
            $success_message = 'User assignment added successfully.';
        }
    }
}
?>

<section class="content-header">
    <h1>Add User Coupon</h1>
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
                    <label class="col-sm-3 control-label">Select User <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="user_id" id="user_id" class="form-control select2" required>
                            <option value="">-- Select --</option>
                            <?php
                            $stmt = $pdo->query("SELECT id, full_name, email FROM tbl_user ORDER BY full_name ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['full_name']) . ' (' . htmlspecialchars($row['email']) . ')</option>';
                            }

                            function slugify_local($string)
                            {
                                $slug = strtolower(trim($string));
                                $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
                                $slug = preg_replace('/-+/', '-', $slug);
                                return trim($slug, '-');
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Coupon</label>
                    <div class="col-sm-4">
                        <select name="coupon_id" id="coupon_id" class="form-control">
                            <option value="">-- No Coupon (Optional) --</option>
                            <?php
                            $stmt = $pdo->query("SELECT c.id, c.coupon_code, p.p_name FROM tbl_coupon c LEFT JOIN tbl_product p ON c.p_id = p.p_id ORDER BY c.coupon_code ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $p_slug = !empty($row['p_name']) ? slugify_local($row['p_name']) : '';
                                echo '<option value="' . htmlspecialchars($row['id']) . '" data-slug="' . $p_slug . '">' . htmlspecialchars($row['coupon_code']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Product</label>
                    <div class="col-sm-4">
                        <select name="p_id" id="p_id" class="form-control select2">
                            <option value="">-- No Specific Product (Optional) --</option>
                            <?php
                            $stmt = $pdo->query("SELECT p_id, p_name FROM tbl_product ORDER BY p_name ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $p_slug = slugify_local($row['p_name']);
                                echo '<option value="' . htmlspecialchars($row['p_id']) . '" data-slug="' . $p_slug . '">' . htmlspecialchars($row['p_name']) . '</option>';
                            }
                            ?>
                        </select>
                        <small class="text-muted">If a coupon is selected, its product will take priority.</small>
                    </div>
                </div>

                <!-- <div class="form-group">
                    <label class="col-sm-3 control-label">Custom Referral URL</label>
                    <div class="col-sm-4">
                        <input type="text" name="custom_url" id="custom_url" class="form-control" placeholder="https://example.com/page (Optional)">
                    </div>
                </div> -->

                <div class="form-group">
                    <label class="col-sm-3 control-label">Commission (%) <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="number" name="percentage" class="form-control" min="0" step="0.01" required>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userIdSelect = document.getElementById('user_id');
        const couponIdSelect = document.getElementById('coupon_id');
        const pIdSelect = document.getElementById('p_id');
        const customUrlInput = document.getElementById('custom_url');
        const baseUrl = "<?= BASE_URL; ?>";

        function updateUrl() {
            const userId = userIdSelect.value;
            if (!userId) return;

            let slug = "";

            // Priority 1: Coupon's Product
            const selectedCoupon = couponIdSelect.options[couponIdSelect.selectedIndex];
            if (selectedCoupon && selectedCoupon.value && selectedCoupon.getAttribute('data-slug')) {
                slug = selectedCoupon.getAttribute('data-slug');
            }
            // Priority 2: Direct Product
            else {
                const selectedProduct = pIdSelect.options[pIdSelect.selectedIndex];
                if (selectedProduct && selectedProduct.value && selectedProduct.getAttribute('data-slug')) {
                    slug = selectedProduct.getAttribute('data-slug');
                }
            }

            let finalUrl = baseUrl;
            if (slug) {
                finalUrl += "product/" + slug;
            }

            customUrlInput.value = finalUrl + (finalUrl.includes('?') ? '&' : '?') + "ref=" + userId;
        }

        userIdSelect.addEventListener('change', updateUrl);
        couponIdSelect.addEventListener('change', updateUrl);
        pIdSelect.addEventListener('change', updateUrl);

        // Also trigger on load if values exist (e.g. on validation error return)
        if (userIdSelect.value) updateUrl();
    });
</script>

<?php require_once('footer.php'); ?>