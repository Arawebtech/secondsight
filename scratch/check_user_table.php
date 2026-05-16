<?php
include("admin/inc/config.php");
$res = mysqli_query($con, "DESCRIBE tbl_user");
if ($res) {
    echo "<h3>tbl_user Structure:</h3>";
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while($row = mysqli_fetch_assoc($res)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "Error describing tbl_user: " . mysqli_error($con);
}
?>
