<?php
require_once('header.php');

if (!isset($_GET['id'])) {
    header('location: logout.php');
    exit;
} else {
    // Check if the id exists
    $stmt = $pdo->prepare("SELECT * FROM tbl_user_coupon WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $total = $stmt->rowCount();
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

// Delete the assignment
$stmt = $pdo->prepare("DELETE FROM tbl_user_coupon WHERE id = ?");
$stmt->execute([$_GET['id']]);

header('location: user-coupon.php');
?>
