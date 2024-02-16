<?php

// Koneksi ke database
require_once 'koneksi.php'; // Sertakan file koneksi.php atau sesuaikan dengan lokasi file Anda

// Data user yang akan dimasukkan ke dalam tabel
$user = [
    ['ID_USER' => 1, 'USERNAME' => 'admin', 'PASSWORD' => '123', 'NAMA_USER' => 'ADMIN', 'ROLE' => 'admin', 'ALAMAT' => 'Kemirahan'],
    ['ID_USER' => 2, 'USERNAME' => 'petugas', 'PASSWORD' => '123', 'NAMA_USER' => 'PETUGAS', 'ROLE' => 'petugas', 'ALAMAT' => 'Mojolangu'],
];

// Query untuk menyisipkan data ke dalam tabel user
foreach ($user as $data) {
    $id_user = $data['ID_USER'];
    $username = $data['USERNAME'];
    $password = $data['PASSWORD'];
    $nama_user = $data['NAMA_USER'];
    $role = $data['ROLE'];
    $alamat = $data['ALAMAT'];

    // Buat query SQL untuk menyisipkan data ke dalam tabel
    $sql = "INSERT INTO user (ID_USER, USERNAME, PASSWORD, NAMA_USER, ROLE, ALAMAT) 
            VALUES ('$id_user', '$username', '$password', '$nama_user', '$role', '$alamat')";

    if ($conn->query($sql) === TRUE) {
        echo "Data user dengan ID $id_user berhasil dimasukkan.<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Tutup koneksi
$conn->close();

?>
