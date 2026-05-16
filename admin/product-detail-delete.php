<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("DELETE FROM tbl_product_price WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	header('location: product-detail.php');
}
?>