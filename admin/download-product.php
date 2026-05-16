<?php
include("inc/config.php");
if(isset($_POST['download_report']))
{
   
     $date = date("d_m_Y_h_i_s_a");
    $csvFileName = 'product_' . $date . '.csv';
    $csvData = array();
     $query = "SELECT p.*, pp.* FROM tbl_product p JOIN tbl_product_price pp ON p.p_id = pp.p_id";
    $result = mysqli_query($con, $query);
    $headers = array('p_id', 'p_name', 'p_sku', 'p_gst', 'p_hsn_code', 'p_unit', 'p_short_description',
        'p_description', 'p_length', 'p_width', 'p_height', 'p_qty', 'p_featured_photo', 'p_is_active', 'ecat_id',
        'id', 'p_old_price', 'p_current_price', 'p_weight', 'p_sku', 'color', 'photo', 'in_stoke'); 
     $csvData[] = $headers;
    while ($row = mysqli_fetch_assoc($result)) {
          $csvData[] = $row;
    }
     $csvFile = fopen('php://temp', 'w');
    foreach ($csvData as $rowData) {
        fputcsv($csvFile, $rowData);
    }
    rewind($csvFile);
    $csvContent = stream_get_contents($csvFile);
    fclose($csvFile);

   
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $csvFileName . '"');
    header('Content-Length: ' . strlen($csvContent));

    
    echo $csvContent;
    // exit;
}
?>