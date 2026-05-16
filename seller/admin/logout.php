<?php
ob_start();
session_start();
date_default_timezone_set("Asia/Calcutta");
include('include/db_config.php');
global $dbLink;

// Agar session me username hai to use clear karo
if(isset($_SESSION["username"])){
    unset($_SESSION["username"]);
}

// Sab session destroy kar do
session_destroy();

// index.php pe redirect
header("Location: index.php");
exit();
?>