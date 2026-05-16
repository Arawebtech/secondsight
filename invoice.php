<?php
include("admin/inc/config.php");
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Common label function
function addLabelBlockFixed($section, $ship, $fullAddress, $style) {
    // Table to keep both labels on one page
    $table = $section->addTable([
        'borderSize' => 0,
        'borderColor' => 'FFFFFF',
        'cellMargin' => 100,
        'alignment' => 'left'
    ]);

    // First row: top label
    $row = $table->addRow();
    $cell = $row->addCell(8000, ['valign' => 'top']); // top of page
    $cell->addText("To", $style);
    $cell->addText("Name: {$ship['name']}", $style);
    $cell->addText("Address: $fullAddress", $style);
    $cell->addText("Mobile: {$ship['phone_no']}", $style);

    // Second row: bottom label
    $row = $table->addRow(6000); // set fixed height to push content to bottom
    $cell = $row->addCell(8000, ['valign' => 'bottom']); // bottom of page
    $cell->addText("To", $style);
    $cell->addText("Name: {$ship['name']}", $style);
    $cell->addText("Address: $fullAddress", $style);
    $cell->addText("Mobile: {$ship['phone_no']}", $style);
}



function fetchOrderAndShip($con, $order_id) {
    // Fetch order
    $query = "SELECT * FROM tbl_order WHERE order_id = '$order_id' LIMIT 1";
    $result = mysqli_query($con, $query) or die("Order Query Failed: " . mysqli_error($con));
    if (mysqli_num_rows($result) == 0) return null;
    $order = mysqli_fetch_assoc($result);

    $user_id = $order['user_id'];

    // Fetch shipping address
    $query_ship = "SELECT name, phone_no, building_no, street_address, landmark, town, district, state, pincode 
                   FROM tbl_shipping_address 
                   WHERE user_id = '$user_id' LIMIT 1";
    $res_ship = mysqli_query($con, $query_ship) or die("Shipping Query Failed: " . mysqli_error($con));
    if (mysqli_num_rows($res_ship) == 0) return null;
    $ship = mysqli_fetch_assoc($res_ship);

    $parts = array_filter([
        $ship['building_no'],
        $ship['street_address'],
        $ship['landmark'],
        $ship['town'],
        $ship['district'],
        $ship['state'] . ' - ' . $ship['pincode']
    ]);
    $fullAddress = implode(', ', $parts);

    return [$ship, $fullAddress, $order];
}

// Initialize Word
$phpWord = new PhpWord();
$section = $phpWord->addSection([
    'paperSize'   => 'A5',
    'orientation' => 'portrait',
    'marginLeft'   => 600,
    'marginRight'  => 600,
    'marginTop'    => 600,
    'marginBottom' => 600
]);

$style = ['size' => 14, 'name' => 'Calibri', 'bold' => true];

// ---- Single Order ----
if (isset($_POST['download_label']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $data = fetchOrderAndShip($con, $order_id);
    if (!$data) die("❌ Order/Shipping not found for ID $order_id");

    list($ship, $fullAddress, $order) = $data;

    addLabelBlockFixed($section, $ship, $fullAddress, $style);


    $fileName = "Shipping_Label_{$order['order_id']}.docx";
}

// ---- Multiple Orders ----
elseif (isset($_POST['download_labels_bulk']) && !empty($_POST['order_ids'])) {
    $orderIds = $_POST['order_ids'];
    $total = count($orderIds);
    $current = 0;

    foreach ($orderIds as $order_id) {
        $current++;
        $data = fetchOrderAndShip($con, $order_id);
        if (!$data) continue;

        list($ship, $fullAddress, $order) = $data;

       addLabelBlockFixed($section, $ship, $fullAddress, $style);

        // Only add page break if not the last order
        if ($current < $total) {
            $section->addPageBreak();
        }
    }

    $fileName = "Shipping_Labels_Bulk.docx";
} else {
    die("❌ No order selected.");
}

// ---- Output ----
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save("php://output");
exit();
