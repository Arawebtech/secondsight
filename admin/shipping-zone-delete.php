<?php
require_once('header.php'); 

$statement = $pdo->prepare("DELETE FROM tbl_shipping_zone WHERE id=?");
$statement->execute(array($_REQUEST['id']));

header('location: shipping-zone.php');
?>