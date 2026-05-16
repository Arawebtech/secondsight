<?php
error_reporting(0);
session_start();
if (empty($_SESSION['username'])) {
  header('Location:index.php');
}
include("include/db_config.php");

/* -----delete  record------*/
if (isset($_GET['del'])) {
  $id = $_GET['del'];
  $query  = "DELETE FROM user WHERE id ='$id' ";
  $result = mysqli_query($conn, $query);
  if ($result) {
    $msg = "<p style='color:green';>Record has been deleted successfully</p>";
  } else {
    $msg = "There is some problem in delete record";
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin- View Users</title>
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <section class="content-header">
        <P>
          <?PHP if (!empty($msg)) {
            echo $msg;
          }
          if (isset($_GET['id']) && $_GET['id'] == 'Added') {
            echo '<p style="color:green;">New User Profile has been added successfully</p>';
          }
          if (isset($_GET['id']) and $_GET['id'] == 'Update') {
            echo '<p style="color:green;">Record has been updated successfully</p>';
          }
          ?>
        </P>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">User List</h3>
                <div class="box-tools pull-right">
                  <a href="add-user.php" class="btn btn-primary">Add User</a>
                </div>
              </div>
              <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>SR.NO</th>
                      <th style="width:100px;">USER NAME</th>
                      <!-- <th>BLOG TITLE</th> -->
                      <!-- <th>URL SLUG</th> -->
                      <th>SSF USER</th>
                      <th>DESCRIPTION</th>
                      <th>IMAGE</th>
                      <!-- <th>THUMB</th> -->
                      <th>COUPON PERMISSION</th>
                      <th>Status</th>
                      <th>Referral Link</th>
                      <th>CREATED DATE</th>
                      <th width="10%">ACTION</th>
                    </tr>

                  </thead>
                  <tbody>
                    <?php
                    $query = "select * from  user ORDER BY id desc";
                    $result_item = mysqli_query($conn, $query);
                    $count = 1;
                    while ($info_item = mysqli_fetch_object($result_item)) {  ?>
                      <tr style="color:<?php if (!empty($color)) {
                                          echo $color;
                                        } ?>">
                        <td><?php echo $count++ ?></td>
                        <td><?php echo $info_item->user_name; ?></td>
                        <!-- <td><?php echo $info_item->blog_title; ?></td> -->
                        <!-- <td><?php echo $info_item->url; ?></td> -->
                        <td>
                          <?php
                          if ($info_item->customer_id > 0) {
                            $cust_q = "SELECT full_name FROM tbl_user WHERE id = '$info_item->customer_id'";
                            $cust_res = mysqli_query($conn3, $cust_q);
                            $cust_data = mysqli_fetch_assoc($cust_res);
                            echo ($cust_data['full_name'] ?? 'N/A') . " (ID: " . $info_item->customer_id . ")";
                          } else {
                            echo "N/A";
                          }
                          ?>
                        </td>

                        <td><?php echo substr(strip_tags($info_item->product_description), 0, 50) . '...'; ?></td>
                        <td><img src="../images/user/user/<?php echo $info_item->user_image; ?>" width="60px"></td>
                        <!-- <td><img src="../images/user/user/<?php echo $info_item->thumbnail_image; ?>" width="40px"></td> -->
                        <td><?php echo $info_item->coupon_permission; ?></td>
                        <td><?php echo $info_item->status; ?></td>
                        <td>
                          <?php if($info_item->customer_id > 0): ?>
                            <small><code><?php echo $base_url; ?>?ref=<?php echo $info_item->customer_id; ?></code></small>
                            <button class="btn btn-xs btn-default" onclick="navigator.clipboard.writeText('<?php echo $base_url; ?>?ref=<?php echo $info_item->customer_id; ?>'); alert('Copied!');">Copy</button>
                          <?php else: ?>
                            N/A
                          <?php endif; ?>
                        </td>
                        <td><?php echo $info_item->created_date; ?></td>

                        <td width="12%">
                          <a href="../view-profile.php?id=<?php echo urlencode(base64_encode(($info_item->id * 1234) + 567)) ?>" target="_blank"><i class="fa fa-eye" style="font-size:24px;" title="View Profile"></i></a> |
                          <a href="add-user.php?id=<?php echo $info_item->id ?>"><i class="fa fa-pencil" style="font-size:24px;" title="Edit"></i></a> |
                          <a href="view-user.php?del=<?php echo $info_item->id ?>" onclick="return confirm('Are you sure want to delete record ?');"><i class="fa fa-times" style="font-size:24px;" title="Delete"></i></a>
                        </td>
                      </tr>
                    <?php
                    } ?>
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
  <!-- ./wrapper -->

  <!-- Model of View Package Details Start -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Student Details</h4>
        </div>
        <div class="modal-body">
          <div id="packageDetails"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="bower_components/jquery/dist/jquery.min.js"></script>
  <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <script src="bower_components/fastclick/lib/fastclick.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  <script src="dist/js/demo.js"></script>

  <script>
    $(function() {
      $('#example1').DataTable()
      $('#example2').DataTable({
        'paging': true,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': true,
        'autoWidth': false
      })
    })
  </script>
</body>

</html>