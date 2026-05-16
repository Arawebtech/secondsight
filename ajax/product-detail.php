<?php
include("../admin/inc/config.php");
$pro_id = $_POST['pro_id'];

$query = "select * from tbl_product_price where id = '$pro_id'";
$result = mysqli_query($con, $query);
$data = mysqli_fetch_assoc($result);

/*$p_id = $data['p_id'];
$query2 = "select p_sku from tbl_product where p_id = '$p_id'";
$result2 = mysqli_query($con, $query2);
$data2 = mysqli_fetch_assoc($result2);*/

$p_price = $data['p_current_price'];
$approx_qty = $data['p_qty'];
$p_sku = $data['p_sku'];
$p_weight = $data['p_weight'];
$p_color = $data['color'];

$str = $p_price.'~'.$approx_qty.'~'.$p_sku.'~'.$p_weight.'~'.$p_color;
echo $str;

?>