<?php

// Koneksi ke database
require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

$id_kategori = 1;

// Data kategori yang akan dimasukkan ke dalam tabel
$kategori = [
    ['nama' => 'Sirup'],
    ['nama' => 'Puyer'],
    ['nama' => 'Tablet'],
    ['nama' => 'Kapsul']
];

// Query untuk menyisipkan data ke dalam tabel kategori
foreach ($kategori as $data) {
    $nama = $data['nama'];

    // Buat query SQL untuk menyisipkan data ke dalam tabel
    $sql = "INSERT INTO kategori (ID_KATEGORI, NAMA_KATEGORI) VALUES ('$id_kategori','$nama')";

    if ($conn->query($sql) === TRUE) {
        echo "Data kategori '$nama' berhasil dimasukkan.<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $id_kategori++;
}

// Tutup koneksi
$conn->close();

?>
