<?php
$c = mysqli_connect("localhost", "root", "", "jhbewdmy_ssf_in");
if (!$c) {
    die("Connection failed: " . mysqli_connect_error());
}
$r = mysqli_query($c, "SHOW TABLES");
while ($row = mysqli_fetch_row($r)) {
    echo $row[0] . "\n";
}
?>
