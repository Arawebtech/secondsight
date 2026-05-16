<?php
include("inc/config.php");
include_once('../TCPDF-main/tcpdf.php');

if(isset($_POST['download_report'])) {

    // Convert start and end dates
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date   = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    // Capture selected order IDs from checkbox selection (if any)
    $selected_orders = $_POST['order_ids'] ?? [];

// Fetch orders with optional date filter (grouped)
$query = "
    SELECT 
        o.order_id,
        u.full_name,
        u.email,
        u.phone,
        MAX(o.order_date) AS order_date,
        MAX(o.order_status) AS order_status,
        COUNT(o.p_id) AS total_items,
        p.payable_amount,
        MAX(o.billing_address) AS billing_address,
        MAX(o.shipping_address) AS shipping_address
    FROM tbl_order o
    JOIN tbl_user u ON o.user_id = u.id
    LEFT JOIN tbl_payment p ON o.order_id = p.order_id
    GROUP BY o.order_id, u.full_name, u.email, u.phone, p.payable_amount
    ORDER BY MAX(o.id) DESC
";
$stmt_orders = $pdo->prepare($query);
$stmt_orders->execute();
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);



        if ($start_date && $end_date) {
            $start = DateTime::createFromFormat('Y-m-d', $start_date)->setTime(0,0,0);
            $end   = DateTime::createFromFormat('Y-m-d', $end_date)->setTime(23,59,59);

            $filtered_orders = array_filter($orders, function($order) use ($start, $end) {
                $ts = DateTime::createFromFormat('d/m/Y h:i:sa', $order['order_date']);
                if (!$ts) return false; // skip if parse failed
                return $ts >= $start && $ts <= $end;
            });
        } else {
            $filtered_orders = $orders;
        }
        
        // If some orders are selected via checkboxes, filter $filtered_orders accordingly
        if(!empty($selected_orders)) {
            $filtered_orders = array_filter($filtered_orders, function($order) use ($selected_orders) {
                return in_array($order['order_id'], $selected_orders);
            });
        }
            if (empty($filtered_orders)) {
            die("No orders found for this date range.");
        }   


    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Second Sight Foundation');
    $pdf->SetTitle('Invoices');
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(TRUE, 20);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetFont('dejavusans', '', 10);

    foreach($filtered_orders as $order_info){

        // Fetch items for this order
        $sql_items = "SELECT * FROM tbl_order WHERE order_id = ?";
        $stmt_items = $pdo->prepare($sql_items);
        $stmt_items->execute([$order_info['order_id']]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        // Fetch payment info
        $sql_payment = "SELECT shipping_charge, discount FROM tbl_payment WHERE order_id = ?";
        $stmt_payment = $pdo->prepare($sql_payment);
        $stmt_payment->execute([$order_info['order_id']]);
        $payment_info = $stmt_payment->fetch(PDO::FETCH_ASSOC);

        // Calculate totals
        $total_subtotal = 0;
        foreach ($items as $item) {
            $total_subtotal += ($item['p_actual_price'] * $item['no_of_item']);
        }
        $shipping_cost  = !empty($payment_info['shipping_charge']) ? $payment_info['shipping_charge'] : 0;
        $discount_amount = !empty($payment_info['discount']) ? $payment_info['discount'] : 0;
        $grand_total = $total_subtotal + $shipping_cost - $discount_amount;

        // Format invoice date
        $invoice_date = DateTime::createFromFormat('d/m/Y h:i:sa', $order_info['order_date']);
        $invoice_date = $invoice_date ? $invoice_date->format('F j, Y') : 'Date not available';
        
        // 🔹 Split billing_address into name, number, and rest (trimmed cleanly)
        $billing_parts = array_map('trim', explode(',', $order_info['billing_address']));
        $billing_name   = $billing_parts[0] ?? '';
        $billing_number = $billing_parts[1] ?? '';
        $billing_rest   = count($billing_parts) > 2 ? implode(', ', array_slice($billing_parts, 2)) : '';

        
            // 🔹 Split shipping_address into name, number, and rest
        $shipping_parts = array_map('trim', explode(',', $order_info['shipping_address']));
        $shipping_name   = $shipping_parts[0] ?? '';
        $shipping_number = $shipping_parts[1] ?? '';
        $shipping_rest   = count($shipping_parts) > 2 ? implode(',', array_slice($shipping_parts, 2)) : ''; 


        // Add page per invoice
        $pdf->AddPage();

        // Build HTML for invoice (tables only)
        $html = '
        <table width="100%">
            <tr>
                <td width="50%">
                    <img src="'.realpath('../assets/img/admin/user-1.png').'" style="height:60px;">
                </td>
                <td width="50%" align="right" style="font-size:10pt;">
                    <strong>Second Sight Foundation – Delhi</strong><br>
                    AE 10 TAGORE G/F DELHI 110027<br>
                    GSTN-07HAAPS1767P2ZW
                </td>
            </tr>
        </table>
        <br><h2>INVOICE</h2>
        
<table width="100%" cellpadding="4" style="font-size:9pt; margin-bottom:40px;">
    <tr>
        <!-- Billing -->
        <td width="33%" style="vertical-align:top;">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr><td align="left"><strong>' . htmlspecialchars($billing_name) . '</strong></td></tr>
                <tr><td align="left">' . nl2br(htmlspecialchars($billing_rest)) . '</td></tr>
                <tr><td align="left">'.htmlspecialchars($order_info['email']).'</td></tr>
                <tr><td align="left">'.htmlspecialchars($order_info['phone']).'</td></tr>
            </table>
        </td>

        <!-- Shipping -->
        <td width="33%" style="vertical-align:top;">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr><td align="left"><strong>Ship To:</strong></td></tr>
                <tr><td align="left">' . htmlspecialchars($billing_name) . '</td></tr>
                <tr><td align="left">' . nl2br(htmlspecialchars($billing_rest)) . '</td></tr>
                <tr><td align="left">'.htmlspecialchars($order_info['phone']).'</td></tr>
            </table>
        </td>

        <!-- Invoice + Order -->
        <td width="33%" style="vertical-align:top;">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr><td align="left"><strong>Invoice Number:</strong> '.htmlspecialchars($order_info['order_id']).'</td></tr>
                <tr><td align="left"><strong>Invoice Date:</strong> '.(($d = DateTime::createFromFormat('d/m/Y h:i:sa', $order_info['order_date'])) ? $d->format('F j, Y') : 'Date not available').'</td></tr>
                <tr><td align="left"><strong>Order Number:</strong> '.htmlspecialchars($order_info['order_id']).'</td></tr>
                <tr><td align="left"><strong>Order Date:</strong> '.(($d = DateTime::createFromFormat('d/m/Y h:i:sa', $order_info['order_date'])) ? $d->format('F j, Y') : 'Date not available').'</td></tr>
                <tr><td align="left"><strong>Payment Method:</strong> Online</td></tr>
            </table>
        </td>
    </tr>
</table>
        <br><br><br>
       <table border="1" cellpadding="4" cellspacing="0" width="100%">
    <thead>
        <tr style="background-color:#000;color:#fff;">
            <th width="60%">Product</th>
            <th width="20%" style="text-align:right;">Quantity</th>
            <th width="20%" style="text-align:right;">Price</th>
        </tr>
    </thead>
    <tbody>';
foreach($items as $item){
    $html .= '<tr>
                <td width="60%">'.htmlspecialchars($item['p_name']).'</td>
                <td width="20%" style="text-align:right;">'.$item['no_of_item'].'</td>
                <td width="20%" style="text-align:right;">₹'.number_format($item['p_actual_price'] * $item['no_of_item'],2).'</td>
              </tr>';
}
$html .= '</tbody>
</table>
<br><br><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="60%"></td> <!-- Empty space to push totals right -->
        <td width="40%">
            <table border="1" cellpadding="4" cellspacing="0" width="100%">
                <tbody>
                    <tr>
                        <th width="60%">Subtotal</th>
                        <td width="40%" style="text-align:right;">₹'.number_format($total_subtotal,2).'</td>
                    </tr>';
if($shipping_cost > 0){
$html .= '<tr>
                        <th>Shipping (via Flat rate)</th>
                        <td style="text-align:right;">₹'.number_format($shipping_cost,2).'</td>
                    </tr>';
}
$html .= '<tr>
                        <th>Total</th>
                        <td style="text-align:right;"><strong>₹'.number_format($grand_total,2).'</strong></td>
                    </tr>
                    <tr>
                        <th>Payment Mode</th>
                        <td style="text-align:right;">Online</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>



        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Place footer at bottom of the page
$footer_html = '<hr><div align="center" style="font-weight:bold; font-size:14pt;">THANK YOU FOR SHOPPING WITH US!</div>';

// Move cursor to 20mm from the bottom of page
$pdf->SetY(-40); // adjust as needed for spacing
$pdf->writeHTML($footer_html, true, false, true, false, '');
    }

    // Prepare readable start/end dates for filename
$start_text = $start_date ? DateTime::createFromFormat('Y-m-d', $start_date)->format('d_m_Y') : '';
$end_text   = $end_date ? DateTime::createFromFormat('Y-m-d', $end_date)->format('d_m_Y') : '';

// Build PDF filename
$pdfFileName = 'Invoices';
if ($start_text) {
    $pdfFileName .= '_' . $start_text;
    if ($end_text) {
        $pdfFileName .= '_to_' . $end_text;
    }
}
$pdfFileName .= '.pdf';
    // Output PDF as download
    $pdf->Output($pdfFileName, 'D');
    exit();
}
?>


<!--include("inc/config.php");-->

<!--if(isset($_POST['download_report'])) {-->
    // Retrieve start and end dates from the form
<!--    $temp_start_date = $_POST['start_date'];-->
<!--    $start_date = date_create($temp_start_date);-->
<!--    $start_date = date_format($start_date,"d/m/Y 00:00:00");-->
    
<!--    $temp_end_date = $_POST['end_date'];-->
<!--    $end_date = date_create($temp_end_date);-->
<!--    $end_date = date_format($end_date,"d/m/Y 23:59:59");-->

<!--    $query = "SELECT * FROM tbl_order WHERE order_date BETWEEN ? AND ?";-->
    
<!--    $statement = $pdo->prepare($query);-->
<!--    $statement->execute([$start_date, $end_date]);-->
<!--    $orders = $statement->fetchAll(PDO::FETCH_ASSOC);-->
    
    // Define CSV headers
<!--    $headers = array(-->
<!--        'user_id', 'p_id', 'p_name', 'p_color', 'p_size', 'p_price', 'p_actual_price', 'p_gst', 'gst_Amount',-->
<!--        'igst', 'igst_Amount', 'cgst', 'cgst_Amount', 'sgst', 'sgst_Amount',-->
<!--        'p_image', 'p_quantity', 'no_of_item', 'weight', 'unit', 'sku', 'order_status', 'order_date'-->
<!--    );-->

    // Generate CSV content
    $csvContent = implode(',', $headers) . "\n"; // Add headers to CSV
<!--    foreach ($orders as $order) {-->
        $csvContent .= '"' . implode('","', $order) . '"' . "\n"; // Add data rows to CSV
<!--    }-->

    // Set CSV file name
<!--    $date = date("d_m_Y_h_i_s_a");-->
<!--    $csvFileName = 'orders_' . $date . '.csv';-->

    // Send CSV file as download
<!--    header('Content-Type: text/csv');-->
<!--    header('Content-Disposition: attachment; filename="' . $csvFileName . '"');-->
<!--    header('Content-Length: ' . strlen($csvContent));-->
<!--    echo $csvContent;-->
<!--    exit();-->
<!--}-->


<!--?>-->
