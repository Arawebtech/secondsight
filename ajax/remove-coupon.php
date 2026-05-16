<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['coupon'])) {
    unset($_SESSION['coupon']);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
