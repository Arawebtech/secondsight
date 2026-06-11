<?php

// ==============================
// 🔧 DEBUG MODE (CHANGE HERE)
// ==============================
define('DEBUG', true); // true = ON, false = OFF

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==============================
// 🌍 TIME ZONE
// ==============================
date_default_timezone_set('Asia/Kolkata');

// ==============================
// 🌐 BASE URL (FIXED - IMPORTANT)
// ==============================
// define("BASE_URL", "http://localhost/araweb/secondsite-in/");
// define("ADMIN_URL", BASE_URL . "admin/");
define("BASE_URL", "https://secondsightfoundation.in/");
define("ADMIN_URL", BASE_URL . "admin/");
define("ADMIN_URL", BASE_URL . "admin/");
// ==============================
// 🗄️ DATABASE (LIVE)
// ==============================
$dbhost = 'localhost';
// $dbname = 'lyuzmkmy_jhbewdmy_ssf_in';
// $dbuser = 'lyuzmkmy_jhbewdmy_ssf_in';
// $dbpass = 'lyuzmkmy_jhbewdmy_ssf_in';

$dbname = 'jhbewdmy_ssf_in';
$dbuser = 'projectuser';
$dbpass = 'Solutions@321@';

// ==============================
// 🔌 PDO CONNECTION
// ==============================
try {
    $pdo = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Fix GROUP BY issue
    $pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
} catch (PDOException $e) {
    die("PDO Error: " . $e->getMessage());
}
// ==============================
// 🔌 MYSQLI CONNECTION
// ==============================
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (! $con) {
    die("MySQLi Error: " . mysqli_connect_error());
}

mysqli_query($con, "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
