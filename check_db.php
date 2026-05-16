<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/inc/config.php");

echo "<h3>Database Connection:</h3>";
if (isset($con) && $con) {
    echo "✅ mysqli Connected<br>";
} else {
    echo "❌ mysqli Failed<br>";
}

if (isset($pdo) && $pdo) {
    echo "✅ PDO Connected<br>";
} else {
    echo "❌ PDO Failed<br>";
}

echo "<h3>tbl_cart Structure:</h3>";
$res1 = mysqli_query($con, "DESCRIBE tbl_cart");
if ($res1) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = mysqli_fetch_assoc($res1)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>tbl_payment Structure:</h3>";
$res_p = mysqli_query($con, "DESCRIBE tbl_payment");
if ($res_p) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = mysqli_fetch_assoc($res_p)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>tbl_order Structure:</h3>";
$res_o = mysqli_query($con, "DESCRIBE tbl_order");
if ($res_o) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = mysqli_fetch_assoc($res_o)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>Check Config:</h3>";
echo "Base URL: " . BASE_URL . "<br>";
echo "DB Name: " . $dbname . "<br>";
?>
