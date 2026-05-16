<?php
include("admin/inc/config.php");

$order_id = base64_decode($_GET['order_id']);
$query = "select * from tbl_order where order_id = '$order_id'";
$result = mysqli_query($con, $query);
$info = mysqli_fetch_assoc($result);
$order_date = $info['order_date'];
$order_number = $info['order_id'];
$invoice_date = $info['order_date'];
$invoice_number = $info['order_id'];
$billing_address = $info['billing_address'];
$shipping_address = $info['shipping_address'];


$query_pay = "select * from tbl_payment where order_id = '$order_id'";
$result_pay = mysqli_query($con, $query_pay);
$info_pay = mysqli_fetch_assoc($result_pay);
$shipping_charge = $info_pay['shipping_charge'];
$discount = $info_pay['discount'];
$transaction_id = $info_pay['payment_id'];
$payment_mode = $info_pay['payment_method'];
$transaction_amount = $info_pay['payable_amount'];


$user_id = $info['user_id'];
$query_b_address = "select state, gst_no from tbl_billing_address where user_id = '$user_id'";
$result_b_address = mysqli_query($con, $query_b_address);
$info_b_address = mysqli_fetch_assoc($result_b_address);
// $billing_address = $info_b_address['billing_address'];
$billing_state = $info_b_address['state'];
$billing_gst_code = $info_b_address['gst_no'];

$query_s_address = "select phone_no, town, state, gst_no from tbl_shipping_address where user_id = '$user_id'";
$result_s_address = mysqli_query($con, $query_s_address);
$info_s_address = mysqli_fetch_assoc($result_s_address);
$shipping_state = $info_s_address['state'];
$shipping_gst_code = $info_s_address['gst_no'];
$phone = $info_s_address['phone_no'];
$place_of_supply = $info_s_address['town'];



///// amount in words /////////////////////////////////////////
$number = $transaction_amount;
   $no = floor($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
//   $amount_in_words = $result . "rupees  " . $points . " paise";
  $amount_in_words = $result . "rupees";
  
///// amount in words /////////////////////////////////////////




// if (isset($_POST['submit'])) {


    include_once('TCPDF-main/tcpdf.php');

    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetAutoPageBreak(TRUE, 10);      
    $pdf->AddPage(); 
    $margin = $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
    

$pdfInvoice = '<!DOCTYPE html>
				<html lang="en">
				<head>
    				<meta charset="UTF-8">
    				<meta name="viewport" content="width=device-width, initial-scale=1.0">
    				<title> Tax Invoice</title>
					<style>
							body {
								font-family: Arial, sans-serif;
								font-size: 8px;
								line-height: 1.6;
								margin: 0;
								padding: 0;

							}

							.container {
								width: 100%;
								max-width: 700px;
								margin: 0 auto;
								padding: 5px;
							}

							.invoice-header {
								text-align: center;
								margin-bottom: 10px;
							}

							.invoice-header h2 {
								margin: 0;
							}

							table {
								width: 100%;
								border-collapse: collapse;
								margin-bottom: 10px;
							}

							table,
							th,
							td {
								border: 1px solid #000;
								padding: 4px;
								text-align: left;
							}

							th {
								background-color: #f2f2f2;
							}

							.footer {
								text-align: center;
								margin-top: 10px;
							}
							.logo{
								width: 500px;
							}
						</style>
					</head>';


$pdfInvoice .= '<body>
                    <div class="container">
						<div class="invoice-header">
							<h2> Tax Invoice</h2>
						</div>
						<table>
							<tr>
								<td colspan="6"><img src="assets/img/logo/logo-for-invoice.jpg" alt="Goonmala Logo" class="logo"></td>
								<th colspan="6"><h2>CreatingBharat</h2> <br><p>706, T11, RPS Savana, Sector 88, Faridabad, HARYANA-121002<br>Email: goonmala2021@gmail.com <br>GST No. 06BBCPG1542D1ZD, PAN No. BBCPG1542D</p></th>
							</tr>
							<tr>
								<th colspan="2">Order Date:</th>
								<td colspan="3">' . $order_date . '</td>
								<th colspan="3">Order No:</th>
								<td colspan="4">' . $order_number .'</td>
							</tr>
							<tr>
								<th colspan="2">Invoice Date:</th>
								<td colspan="3">' . $invoice_date . '</td>
								<th colspan="3">Invoice No:</th>
								<td colspan="4">' . $invoice_number . '</td>
							</tr>
							<tr>
								<th colspan="2">Billing Address:</th>
								<td colspan="10">' . $billing_address . '</td>
							</tr>
							<tr>
								<th colspan="2">GST No:</th>
								<td colspan="3">Code: ' . $billing_gst_code . '</td>
								<th colspan="2">State:</th>
								<td colspan="5">' . $billing_state . '</td>
							</tr>
							<tr>
								<th colspan="2">Shipping Address:</th>
								<td colspan="10">' .  $shipping_address . '</td>
							</tr>
							<tr>
								<th colspan="2">GST No:</th>
								<td colspan="3">Code: ' . $shipping_gst_code . '</td>
								<th colspan="2">State:</th>
								<td colspan="5">' . $shipping_state . '</td>
							</tr>
							<tr>
								<th>Phone</th>
								<td colspan="2">' . $phone . '</td>
								<th colspan="2">Place of Supply :</th>
								<td colspan="2">' . $place_of_supply . '</td>
								<th colspan="2">Delivery Mode :</th>
								<td colspan="3">Online</td>
							</tr>
							
							<tr>
								<th>S. No</th>
								<th colspan="2">Product name (SKU)</th>
								<th>Qty</th>
								<th>Rate</th>
								<th>Amount</th>
								<th>HSN No</th>
								<th>GST (%)</th>
								<th>CGST</th>
								<th>SGST</th>
								<th>IGST</th>
								<th>Amount</th>
							</tr>';
							
							$count = 1;
							$sub_total = 0;
							$total_gst_amount = 0;
							$total_igst_Amount = 0;
							$total_cgst_Amount = 0;
							$total_sgst_Amount = 0;
							$gst_amount = 0;
							$gst = 0;
							$query = "select * from tbl_order where order_id = '$order_id'";
							$result = mysqli_query($con, $query);
							while($info = mysqli_fetch_assoc($result))
							{
								$p_name = $info['p_name'];
								$sku = $info['sku'];
								$no_of_item = $info['no_of_item'];
								$p_price = $info['p_price'];
								$amount = $info['p_actual_price'] * $info['no_of_item'];
								$gst_Amount = $info['gst_Amount'] * $info['no_of_item'];
								$p_gst = $info['p_gst'];
								$igst_Amount = $info['igst_Amount'] * $info['no_of_item'];
								$igst = $info['igst'];
								$cgst_Amount = $info['cgst_Amount'] * $info['no_of_item'];
								$cgst = $info['cgst'];
								$sgst_Amount = $info['sgst_Amount'] * $info['no_of_item'];
								$sgst = $info['sgst'];
								$total = $info['p_price'] * $info['no_of_item'];


								$p_id = $info['p_id'];
								$query_hsn = "select p_hsn_code from tbl_product where p_id = '$p_id'";
								$result_hsn = mysqli_query($con, $query_hsn);
								$info_hsn  = mysqli_fetch_assoc($result_hsn);
								$p_hsn_code = $info_hsn['p_hsn_code'];
								

								$total_gst_amount += $gst_amount;
								$total_igst_Amount += $igst_Amount;
								$total_cgst_Amount += $cgst_Amount;
								$total_sgst_Amount += $sgst_Amount;
								$sub_total += $total;

								
							$pdfInvoice .= '
							<tr>
								<td>' . $count++ . '</td>
								<td colspan="2">' . $p_name . ' (' . $sku . ')</td>
								<td>' . $no_of_item . '</td>
								<td>₹' . $p_price . '</td>
								<td>₹' . $amount . '</td>
								<td>' . $p_hsn_code . '</td>
								<td>₹' . $gst_amount . ' ('. $gst .'%)</td>
								<td>₹' . $igst_Amount . ' ('. $igst .'%)</td>
								<td>₹' . $cgst_Amount . ' ('. $cgst .'%)</td>
								<td>₹' . $sgst_Amount . ' ('. $sgst .'%)</td>
								<td>₹' . $total . '</td>
							</tr>';
							
							}
							// $transaction_amount = ($sub_total + $total_gst_amount + $total_igst_Amount + $total_cgst_Amount + $total_sgst_Amount + $shipping_charge) - ($discount);
							$pdfInvoice .= '<tr>
								<td colspan="11">Shipping Charges</td>
								<td>₹' . $shipping_charge . '</td>
							</tr>
							<tr>
								<td colspan="11">Discount if any</td>
								<td>₹' . $discount . '</td>
							</tr>
							<tr>
								<td colspan="6"><strong>Total Amount in Words</strong></td>
								<td><strong>Total</strong></td>
								<td>₹' . $total_gst_amount . '</td>
								<td>₹' . $total_igst_Amount . '</td>
								<td>₹' . $total_cgst_Amount . '</td>
								<td>₹' . $total_sgst_Amount . '</td>
								<td>₹' . $sub_total . '</td>
							</tr>
							<tr>
								<td colspan="8">' . $amount_in_words . '</td>
								<td colspan="4" style="text-align:center">
									<strong>Certified that the particulars given above are true and correct</strong>
									</td>
							</tr>
							<tr>
								<td colspan="4"><strong>Payment Transaction ID</strong></td>
								<td colspan="4">' . $transaction_id . '</td>
								<td colspan="4" rowspan="2"></td>
							</tr>
							<tr>
								<td colspan="4"><strong>Transaction Amount</strong></td>
								<td colspan="4"><strong>₹' . $transaction_amount . '</strong></td>
							</tr>
							<tr>
								<td colspan="4"><strong>Payment Mode: </strong></td>
								<td colspan="4">' . $payment_mode . '</td>
								<td colspan="4" style="text-align:center"><strong>Authorised Signatory</strong></td>
							</tr>

							</table>
						<p><strong>This is a computer generated invoice and does not require signature. </strong></p>
					</div>
                </body>
            </html>';

$pdf->writeHTML($pdfInvoice);
$file_name = "INVOICE_" . $order_id . ".pdf";
$pdfOutPut = $pdf->output($file_name, 'D');

// }
?>

