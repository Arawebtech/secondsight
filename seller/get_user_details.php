<?php

include('admin/include/db_config.php');

// User ID to filter
$target_user_id = isset($_GET['id']) ? mysqli_real_escape_string($conn3, $_GET['id']) : '';

if (empty($target_user_id)) {
    echo "<h3>Please provide a User ID (e.g., get_user_details.php?id=123)</h3>";
} else {
    echo "<h3>Matched User & Coupon Details for ID: $target_user_id</h3>";

    /* JOIN Query — Single user details with Coupon info */
    $query = "
    SELECT 
        u.*,
        c.coupon_id,
        c.percentage,
        c.created_date as coupon_assigned_date
    FROM tbl_user_coupon c
    INNER JOIN tbl_user u
        ON c.user_id = u.id
    WHERE u.id = '$target_user_id'
    ";

    $result = mysqli_query($conn3, $query);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn3));
    }

    if (mysqli_num_rows($result) > 0) {
        /* Table Start */
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";

        /* Dynamic column headers */
        $fields = mysqli_fetch_fields($result);
        echo "<tr style='background-color: #f2f2f2;'>";
        foreach ($fields as $field) {
            echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";

        /* Row */
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No matching user found with ID: $target_user_id who has a coupon record.</p>";
    }
}

?>
