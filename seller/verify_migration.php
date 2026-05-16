<?php
include('admin/include/db_config.php');
if ($conn3) {
    echo "Connected to SSF_IN successfully.\n";
    $res = mysqli_query($conn3, "SELECT id, full_name FROM tbl_user LIMIT 5");
    while ($row = mysqli_fetch_assoc($res)) {
        echo "ID: " . $row['id'] . " | Name: " . $row['full_name'] . "\n";
    }
} else {
    echo "Connection failed.\n";
}
?>
