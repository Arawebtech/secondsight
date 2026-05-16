<?php
error_reporting(0);
session_start();
if(empty($_SESSION['username'])){
	header('Location:index.php');
}
include('include/db_config.php');
include("Classes/users.class.php");
$usersdata = new users();

$user_id=$member_id=$_SESSION['username']->id;
$result_users=$usersdata->getuserinfo($user_id);
$info_users=mysqli_fetch_object($result_users);

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Own Holiday Club | User Profile</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link rel="icon" href="<?=$base_url;?>assets/images/logo-fav.png" type="image/png">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('include/header.php');?>
 <?php include('include/side-bar.php');?>
  <!-- Left side column. contains the logo and sidebar -->
  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         Profile
      </h1>
      <!--<ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">User profile</li>
      </ol>-->
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-6" style="margin-left:200px;">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="dist/img/user4-128x128.jpg" alt="User profile picture">

              <!--<h3 class="profile-username text-center"><?php echo $info_users->name;  ?></h3>-->              
              <p></p>
              <ul class="list-group list-group-unbordered">
			    <li class="list-group-item">
                  <b>User Name</b> <a class="pull-right"><?php echo $info_users->name;  ?></a>
                </li>
                <li class="list-group-item">
                  <b>Mobile</b> <a class="pull-right"><?php echo $info_users->phone;  ?></a>
                </li>
				<li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo $info_users->email;  ?></a>
                </li>
				<li class="list-group-item">
                  <b>Created Date</b> <a class="pull-right"><?php echo $info_users->created_date;   ?></a>
                </li>
                <li class="list-group-item">
                  <b>Updated Date</b> <a class="pull-right"><?php echo $info_users->updated_date;   ?></a>
                </li>
                <li class="list-group-item">
                  <b>Account Status</b> <a class="pull-right" ><?php if($info_users->status=='1'){ echo '<strong style="color:green;">Active</strong>';}else{ echo '<strong style="color:red;">De Active</strong>';}  ?></a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			 <strong><i class="fa fa-map-marker margin-r-5"></i> Address</strong>
              <p class="text-muted"><?php echo $info_users->address;  ?></p>
              <hr>
            </div>            
          </div>          
        </div>        
      </div>      
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include('include/footer.php'); ?>
  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
</body>
</html>
