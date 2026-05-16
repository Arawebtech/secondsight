<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Database Credentials */

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "jhbewdmy_ssf_in";

/* Connection */

$conn3 = mysqli_connect(
    $servername,
    $username,
    $password,
    $dbname
);

if (!$conn3) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h3>User Details with All Coupons</h3>";

/* JOIN Query */

$query = "
SELECT 
    u.id AS user_id,
    u.full_name,
    c.coupon_id,
    c.percentage,
    c.created_at
FROM tbl_user_coupon c
INNER JOIN tbl_user u
    ON c.user_id = u.id
ORDER BY u.id DESC, c.created_at DESC
";

$result = mysqli_query($conn3, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn3));
}

echo "<table border='1' cellpadding='8'>";

echo "<tr>
        <th>User ID</th>
        <th>Full Name</th>
        <th>Coupon ID</th>
        <th>Percentage</th>
        <th>Created At</th>
      </tr>";

while ($row = mysqli_fetch_assoc($result)) {

    echo "<tr>";

    echo "<td>" . $row['user_id'] . "</td>";
    echo "<td>" . $row['full_name'] . "</td>";
    echo "<td>" . $row['coupon_id'] . "</td>";
    echo "<td>" . $row['percentage'] . "%</td>";
    echo "<td>" . $row['created_at'] . "</td>";

    echo "</tr>";
}

echo "</table>";
