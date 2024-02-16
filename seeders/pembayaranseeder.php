<?php

// Koneksi ke database
require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

$id_pembayaran = 1;

// Data kategori yang akan dimasukkan ke dalam tabel
$pembayaran = [
    ['nama' => 'Cash'],
    ['nama' => 'Transfer'],
    ['nama' => 'Kredit']
];

// Query untuk menyisipkan data ke dalam tabel kategori
foreach ($pembayaran as $data) {
    $nama = $data['nama'];

    // Buat query SQL untuk menyisipkan data ke dalam tabel
    $sql = "INSERT INTO pembayaran (ID_PEMBAYARAN, NAMA_PEMBAYARAN) VALUES ('$id_pembayaran','$nama')";

    if ($conn->query($sql) === TRUE) {
        echo "Data kategori '$nama' berhasil dimasukkan.<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $id_pembayaran++;
}

// Tutup koneksi
$conn->close();

?>
