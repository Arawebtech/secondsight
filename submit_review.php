<?php
session_start();
header('Content-Type: application/json');
include("admin/inc/config.php");

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $product_id = intval($_POST['product_id']);
    $user_id = intval($_SESSION['user_id']);
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($con, trim($_POST['review']));

    if ($rating < 1 || $rating > 5 || empty($review)) {
        $response['message'] = 'Invalid rating or review.';
    } else {
        $query = "INSERT INTO tbl_reviews (product_id, customer_id, rating, review, created_at)
                  VALUES ('$product_id', '$user_id', '$rating', '$review', NOW())";

        if (mysqli_query($con, $query)) {
            $response['success'] = true;
            $response['message'] = 'Review submitted successfully.';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($con);
        }
    }
} else {
    $response['message'] = 'You must be logged in to submit a review.';
}

echo json_encode($response);
?>
