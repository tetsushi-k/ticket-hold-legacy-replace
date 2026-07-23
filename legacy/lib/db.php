<?php
require_once __DIR__ . '/config.php';

// intentional debt D1: global connection, die on error
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$conn) {
    die('DB connect failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
