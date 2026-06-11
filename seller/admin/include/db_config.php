<?php

$servername = "localhost";

/* First Database */
// $username   = "lyuzmkmy_secondseller";
// $password   = "lyuzmkmy_secondseller";
// $dbname     = "lyuzmkmy_secondseller";

$username = "projectuser";
$password = "Solutions@321@";
$dbname   = "secondside_seller";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (! $conn) {
    die("Connection failed (Seller DB): " . mysqli_connect_error());
}

/* Second Database */
// $username2   = "lyuzmkmy_jhbewdmy_ssf_in";
// $password2   = "lyuzmkmy_jhbewdmy_ssf_in";
// $dbname2     = "lyuzmkmy_jhbewdmy_ssf_in";

$username2 = "projectuser";
$password2 = "Solutions@321@";
$dbname2   = "jhbewdmy_ssf_in";

$conn3 = mysqli_connect($servername, $username2, $password2, $dbname2);

if (! $conn3) {
    die("Connection failed (SSF DB): " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Kolkata');

//$base_url = "http://localhost/araweb/secondsite-in/";
$base_url = "https://secondsightfoundation.in/";
