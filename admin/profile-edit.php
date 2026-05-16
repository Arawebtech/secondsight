<?php require_once('header.php'); ?>
<?php
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$upload_dir = '../assets/img/admin/';
$error_message = '';
$success_message = '';

// === FORM 1: Update info ===
if (isset($_POST['form1'])) {
    $valid = 1;

    if (empty($_POST['full_name'])) {
        $valid = 0;
        $error_message .= "Name cannot be empty<br>";
    }

    if (empty($_POST['email'])) {
        $valid = 0;
        $error_message .= "Email cannot be empty<br>";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $valid = 0;
        $error_message .= "Email must be valid<br>";
    } else {
        $stmt = $pdo->prepare("SELECT email FROM tbl_admin WHERE id=?");
        $stmt->execute([$_SESSION['user']['id']]);
        $current_email = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT email FROM tbl_admin WHERE email=? AND email!=?");
        $stmt->execute([$_POST['email'], $current_email]);
        if ($stmt->rowCount()) {
            $valid = 0;
            $error_message .= "Email already exists<br>";
        }
    }

    if ($valid) {
        $_SESSION['user']['full_name'] = $_POST['full_name'];
        $_SESSION['user']['email'] = $_POST['email'];
        $_SESSION['user']['phone'] = $_POST['phone'];

        $stmt = $pdo->prepare("UPDATE tbl_admin SET full_name=?, email=?, phone=? WHERE id=?");
        $stmt->execute([
            $_POST['full_name'],
            $_POST['email'],
            $_POST['phone'],
            $_SESSION['user']['id']
        ]);

        $success_message = "Information updated successfully.";
    }
}

// === FORM 2: Update photo ===
if (isset($_POST['form2'])) {
    $valid = 1;
    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if ($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= "Only jpg, jpeg, png, gif files are allowed<br>";
        }
    }

    if ($valid) {
        // Delete old photo
        if (!empty($_SESSION['user']['photo'])) {
            $old_path = $upload_dir . $_SESSION['user']['photo'];
            if (file_exists($old_path)) unlink($old_path);
        }

        // Upload new photo
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $final_name = 'user-' . $_SESSION['user']['id'] . '.' . $ext;
        move_uploaded_file($path_tmp, $upload_dir . $final_name);
        $_SESSION['user']['photo'] = $final_name;

        $stmt = $pdo->prepare("UPDATE tbl_admin SET photo=? WHERE id=?");
        $stmt->execute([$final_name, $_SESSION['user']['id']]);

        $success_message = "Photo updated successfully.";
    }
}

// === FORM 3: Update password ===
if (isset($_POST['form3'])) {
    $valid = 1;
    if (empty($_POST['password']) || empty($_POST['re_password'])) {
        $valid = 0;
        $error_message .= "Password fields cannot be empty<br>";
    } elseif ($_POST['password'] != $_POST['re_password']) {
        $valid = 0;
        $error_message .= "Passwords do not match<br>";
    }

    if ($valid) {
        $hashed = md5($_POST['password']); // use password_hash() for better security
        $_SESSION['user']['password'] = $hashed;

        $stmt = $pdo->prepare("UPDATE tbl_admin SET password=? WHERE id=?");
        $stmt->execute([$hashed, $_SESSION['user']['id']]);

        $success_message = "Password updated successfully.";
    }
}

// === Fetch latest user data
$stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE id=?");
$stmt->execute([$_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Define variables for the form
$full_name = $user['full_name'] ?? '';
$email     = $user['email'] ?? '';
$phone     = $user['phone'] ?? '';
$photo     = $user['photo'] ?? '';
$role      = $user['role'] ?? '';
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Profile</h1>
    </div>
</section>

<?php if ($error_message): ?>
<div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>
<?php if ($success_message): ?>
<div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<section class="content">
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Update Info</a></li>
                <li><a href="#tab_2" data-toggle="tab">Update Photo</a></li>
                <li><a href="#tab_3" data-toggle="tab">Update Password</a></li>
            </ul>

            <div class="tab-content">
                <!-- TAB 1: Info -->
                <div class="tab-pane active" id="tab_1">
                    <form class="form-horizontal" action="" method="post">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Name *</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email *</label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Phone</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Role</label>
                                    <div class="col-sm-4" style="padding-top:7px;">
                                        <?php echo htmlspecialchars($role); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-6">
                                        <button type="submit" class="btn btn-success" name="form1">Update Info</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Photo -->
                <div class="tab-pane" id="tab_2">
                    <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Current Photo</label>
                                    <div class="col-sm-4">
                                        <?php if ($photo): ?>
                                        <img src="../assets/img/admin/<?php echo htmlspecialchars($photo); ?>" width="150">
                                        <?php else: ?>
                                        <p>No photo uploaded</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">New Photo</label>
                                    <div class="col-sm-4">
                                        <input type="file" name="photo">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-6">
                                        <button type="submit" class="btn btn-success" name="form2">Update Photo</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: Password -->
                <div class="tab-pane" id="tab_3">
                    <form class="form-horizontal" action="" method="post">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Retype Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" class="form-control" name="re_password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-6">
                                        <button type="submit" class="btn btn-success" name="form3">Update Password</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div> <!-- /.tab-content -->
        </div> <!-- /.nav-tabs-custom -->
    </div>
</div>
</section>

<?php require_once('footer.php'); ?>
