<?php
session_start();
include("../admin/inc/config.php");

$user_id = "";
if(isset($_SESSION['user_id']))
{
    $user_id = $_SESSION['user_id'];
}
else
{
	if(isset($_SESSION['temp_user_id']))
	{
		$user_id = $_SESSION['temp_user_id'];
	}
	else
	{
		$_SESSION['temp_user_id'] = rand(10000, 100000);
		$user_id = $_SESSION['temp_user_id'];
	}
}

// $user_id = $_SESSION['user_id'];
$p_id = $_POST['p_id'];
$p_name = $_POST['p_name'];
$p_color = $_POST['p_color'];
$p_size = $_POST['p_size'];
$p_price = $_POST['p_price'];

$p_gst_per = 0;
$query_gst = "select p_gst from tbl_product where p_id = '$p_id'";
$result_gst = mysqli_query($con, $query_gst);
$info_gst = mysqli_fetch_assoc($result_gst);
$p_gst_per = $info_gst['p_gst'];
$p_actual_price = round((($p_price*100) / ($p_gst_per+100)), 2);
$p_gst = round(($p_price - $p_actual_price), 2);

$p_image = $_POST['p_image'];
$p_qty = $_POST['p_qty'];
$p_total_item = $_POST['p_total_item'];
$p_weight = $_POST['p_weight'];
$p_unit = $_POST['p_unit'];
/*if($p_unit == "gm")
{
	if($p_weight == )
}
else if($p_unit == "no.")
{

}*/
$p_full_sku = $_POST['p_full_sku'];

$query_ins = "insert into tbl_wishlist(user_id, p_id, p_name, p_color, p_size, p_price, p_actual_price, p_gst, p_image, p_quantity, no_of_item, weight, unit, sku) values('$user_id', '$p_id', '$p_name', '$p_color', '$p_size', '$p_price', '$p_actual_price', '$p_gst', '$p_image', '$p_qty', '$p_total_item', '$p_weight', '$p_unit', '$p_full_sku')";
$result_ins = mysqli_query($con, $query_ins);

if($result_ins > 0)
{
	echo "Product Added Successfully";
}

error_reporting();
// echo "hi";
?>