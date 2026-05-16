<?php
error_reporting(0);
session_start();
include('include/db_config.php');
include("Classes/users.class.php");
$userdata = new users();

if (isset($_POST['submit']) and $_POST['submit'] == 'Sign in') {
  $phone = $_POST['username'];
  $password = $_POST['password'];
  
  $result_user = $userdata->userLogin($phone, $password);
  $count_user = $result_user->num_rows;
  
  if ($count_user > 0) {
    $row = mysqli_fetch_object($result_user);
    $_SESSION['username'] = $row;
    echo "<script>window.open('dashboard.php','_self')</script>";
  } else {
    $error = '<div class="alert alert-danger" role="alert">Username or password wrong.</div>';
  }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Diamond Furniture</title>
    <link rel="icon" type="image/png" sizes="192x192" href="../assets/imgs/fab.png">
    <!--<link rel="icon" type="image/png" sizes="192x192" href="../assets/images/favicon.jpg">-->
    
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
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
    

 <!--<link rel="icon" href="https://arawebtechnologies.in/images/header-logo.png" type="image/x-icon">-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="login-box" style="margin: 5% auto;">
        <div class="login-logo">
            <img src="../assets/imgs/head-logo.png" height="100px;" width="300px">
            <!--<a href="#"><b>IEC </b>PORTAL </a>-->
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <?php if (isset($error)) {
        echo $error;
      } ?>
            <form method="post">
                <div class="form-group has-feedback">
                    <input type="text" name="username" class="form-control" placeholder="Username">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <!--<input type="checkbox"> Remember Me-->
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <!--<button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>-->
                        <input type="submit" class="btn btn-primary btn-block btn-flat" name="submit" value="Sign in">
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <a href="forgot-password.php">Forgot Password</a><br>
            <!--<a href="register.html" class="text-center">Register a new membership</a>-->
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery 3 -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="plugins/iCheck/icheck.min.js"></script>
    <script>
    $(function() {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
    </script>
</body>

</html>