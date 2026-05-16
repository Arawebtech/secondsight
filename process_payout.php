<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/inc/config.php");

$user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) ($_POST['payout_amount'] ?? 0);
    $bank_name = mysqli_real_escape_string($con, $_POST['bank_name']);
    $account_no = mysqli_real_escape_string($con, $_POST['account_no']);
    $ifsc_code = mysqli_real_escape_string($con, $_POST['ifsc_code']);
    $account_holder = mysqli_real_escape_string($con, $_POST['account_holder']);

    if ($amount < 2000) {
        echo json_encode(['status' => 'error', 'message' => 'Minimum withdrawal amount is ₹2000.']);
        exit;
    }

    // 1. Update Bank Details in tbl_user
    $update_user = "UPDATE tbl_user SET 
                    bank_name = '$bank_name', 
                    account_no = '$account_no', 
                    ifsc_code = '$ifsc_code', 
                    account_holder = '$account_holder' 
                    WHERE id = '$user_id'";
    mysqli_query($con, $update_user);

    // 2. Check for existing pending request to avoid duplicates
    $check_pending = mysqli_query($con, "SELECT id FROM tbl_payout_requests WHERE user_id = '$user_id' AND status = 'Pending'");
    if (mysqli_num_rows($check_pending) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already have a pending payout request. Please wait for admin approval.']);
        exit;
    }

    // 3. Create Payout Request
    $insert_request = "INSERT INTO tbl_payout_requests (user_id, amount, status, request_date) 
                       VALUES ('$user_id', '$amount', 'Pending', NOW())";

    if (mysqli_query($con, $insert_request)) {
        // Optional: Send email notification to admin here
        // $admin_email = "admin@example.com";
        // mail($admin_email, "New Payout Request", "User ID $user_id has requested a payout of ₹$amount.");

        echo json_encode(['status' => 'success', 'message' => 'Payout request sent successfully! Admin will process it soon.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit request: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>