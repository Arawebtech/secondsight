<?php
error_reporting(0);
session_start();
if (empty($_SESSION['username'])) {
  header('Location:index.php');
}
include("include/db_config.php");

// Handle Payout Status Update
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $sql = "UPDATE tbl_payout_requests SET status = 'Paid', payment_date = NOW() WHERE id = '$id'";
    if(mysqli_query($conn, $sql)) {
        $msg = "<p style='color:green;'>Payout marked as Paid.</p>";
    }
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $sql = "UPDATE tbl_payout_requests SET status = 'Rejected' WHERE id = '$id'";
    if(mysqli_query($conn, $sql)) {
        $msg = "<p style='color:red;'>Payout request rejected.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin - Payout Requests</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="icon" type="image/png" href="https://secondsightfoundation.com/assets/img/logo-fav.png">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <?php include('include/header.php'); ?>
    <?php include('include/side-bar.php'); ?>

    <div class="content-wrapper">
      <section class="content-header">
        <h1>Payout Requests</h1>
        <?PHP if (!empty($msg)) echo $msg; ?>
      </section>

      <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>USER INFO</th>
                      <th>BANK DETAILS</th>
                      <th>AMOUNT</th>
                      <th>REQUEST DATE</th>
                      <th>STATUS</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT pr.*, u.full_name, u.phone, u.bank_name, u.account_no, u.ifsc_code, u.account_holder 
                              FROM tbl_payout_requests pr 
                              JOIN tbl_user u ON pr.user_id = u.id 
                              ORDER BY pr.request_date DESC";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_object($result)) { ?>
                      <tr>
                        <td><?php echo $row->id; ?></td>
                        <td>
                            <strong><?php echo $row->full_name; ?></strong><br>
                            <small><?php echo $row->phone; ?></small>
                        </td>
                        <td>
                            <strong>Holder:</strong> <?php echo $row->account_holder; ?><br>
                            <strong>Bank:</strong> <?php echo $row->bank_name; ?><br>
                            <strong>A/C:</strong> <?php echo $row->account_no; ?><br>
                            <strong>IFSC:</strong> <?php echo $row->ifsc_code; ?>
                        </td>
                        <td class="text-bold text-success">₹<?php echo number_format($row->amount, 2); ?></td>
                        <td><?php echo $row->request_date; ?></td>
                        <td>
                            <?php if($row->status == 'Pending') { ?>
                                <span class="label label-warning">Pending</span>
                            <?php } elseif($row->status == 'Paid') { ?>
                                <span class="label label-success">Paid</span>
                            <?php } else { ?>
                                <span class="label label-danger">Rejected</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($row->status == 'Pending') { ?>
                                <a href="?approve=<?php echo $row->id; ?>" class="btn btn-xs btn-success" onclick="return confirm('Mark as Paid?')">Approve</a>
                                <a href="?reject=<?php echo $row->id; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Reject request?')">Reject</a>
                            <?php } else { ?>
                                <small class="text-muted"><?php echo $row->payment_date; ?></small>
                            <?php } ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include('include/footer.php'); ?>
  </div>

  <script src="bower_components/jquery/dist/jquery.min.js"></script>
  <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  <script>
    $(function() {
      $('#example1').DataTable();
    })
  </script>
</body>
</html>
