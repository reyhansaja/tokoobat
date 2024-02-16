<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "ukk2024_raihan_203"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "<script>console.log('Koneksi berhasil!');</script>";
}

require_once 'vendor/autoload.php';
