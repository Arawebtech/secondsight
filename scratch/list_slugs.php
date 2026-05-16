<?php
include("../admin/inc/config.php");

function slugify($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

$query = "SELECT p_id, p_name FROM tbl_product";
$result = mysqli_query($con, $query);

echo "Listing all products and their slugs:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['p_id'] . " | Name: " . $row['p_name'] . " | Slug: " . slugify($row['p_name']) . "\n";
}
?>
