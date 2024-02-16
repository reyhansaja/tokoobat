<?php

// Kredensial untuk koneksi database
$servername = "localhost"; // Ganti dengan host database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "ukk2024_raihan_203"; // Ganti dengan nama database Anda

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} else {
    echo "<script>console.log('Koneksi berhasil!');</script>";
}

// require_once 'vendor/autoload.php'; // Sesuaikan dengan lokasi autoload file Composer Anda
