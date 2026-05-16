<?php require_once('header.php');

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
    $tagline = $_POST['tagline'];
     $date = date('d/m/Y');
    $statement = $pdo->prepare("INSERT INTO tbl_tagline (tagline, created_date) VALUES (?, ?)");
    $result = $statement->execute([$tagline, $date]);
   

    if ($result) {
        $success_message = 'Tagline is added successfully.';
    } else {
        $error_message = 'Error adding tagline.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Tagline</h1>
    </div>
    <div class="content-header-right">
        <a href="tagline.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if ($error_message): ?>
                <div class="callout callout-danger">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Tagline <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="tagline" class="form-control">
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
