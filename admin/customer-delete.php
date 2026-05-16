<?php 
require_once('header.php'); 

if(isset($_REQUEST['id']))
{
	$user_id = $_REQUEST['id'];	

	$statement = $pdo->prepare("DELETE FROM tbl_user WHERE id=?");
	$statement->execute(array($user_id));
}

header('location: customer.php');
?>