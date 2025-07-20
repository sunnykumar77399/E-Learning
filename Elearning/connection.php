<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Elearning";


try {
    $conn = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
