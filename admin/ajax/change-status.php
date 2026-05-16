<?php
include("../inc/config.php");
$order_status = $_POST['status'];
$order_id = $_POST['order_id'];

$query = "update tbl_order set order_status = '$order_status' where order_id = '$order_id'";
$result = mysqli_query($con, $query);
// echo $result;


if ($_POST['action'] == 'bulk_order_status') {
    $order_ids = $_POST['order_ids']; // should be an array
    $status    = $_POST['status'];

    if (!empty($order_ids) && is_array($order_ids)) {
        $placeholders = rtrim(str_repeat('?,', count($order_ids)), ',');
        $sql = "UPDATE tbl_order SET order_status = ? WHERE order_id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);

        // Merge status + ids
        $params = array_merge([$status], $order_ids);
        $stmt->execute($params);

        echo "success";
    } else {
        echo "no_orders";
    }
    exit;
}

?>