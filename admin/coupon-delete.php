<?php
require_once('header.php');

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Delete coupon
    $stmt = $pdo->prepare("DELETE FROM tbl_coupon WHERE id=?");
    $stmt->execute([$id]);
}

// Redirect back to coupon list
header("Location: coupon.php");
exit;
?>
