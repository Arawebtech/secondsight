<?php 
require_once('header.php'); 

if(isset($_REQUEST['id']))
{
	$user_id = $_REQUEST['id'];
	$status = $_REQUEST['status'];
	$statement = $pdo->prepare("UPDATE tbl_user SET status=? WHERE id=?");
	$statement->execute(array($status, $user_id));
}
header('location: customer.php');
?>