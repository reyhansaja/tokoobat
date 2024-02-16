<?php
session_start();

// Jika pengguna tidak memiliki sesi atau level yang sesuai, arahkan ke halaman login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas') {
    header("Location: login.php");
    exit();
}
// Menggunakan autoloader jika Anda menginstal FPDF menggunakan Composer
require 'vendor/autoload.php';

require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

// Kueri SQL untuk mendapatkan data pembayaran dari tabel pembayaran
$sql_transaksi = "SELECT * FROM penjualan";
$result_transaksi = $conn->query($sql_transaksi);

// Atur header untuk membuat file PDF
header('Content-type: application/pdf');

// Instansiasi objek FPDF
$pdf = new FPDF('L', 'mm', array(300, 230));


$pdf->AddPage();



// Tambahkan judul
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Data Penjualan', 0, 1, 'C');

// Tambahkan header tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nama Petugas', 1, 0, 'C');
$pdf->Cell(40, 10, 'Nama Pelanggan', 1, 0, 'C');
$pdf->Cell(40, 10, 'Nama Obat', 1, 0, 'C');
$pdf->Cell(40, 10, 'Jumlah', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 0, 'C');
$pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(40, 10, 'Jenis Pembayaran', 1, 0, 'C');
$pdf->Ln();


// Tambahkan data ke dalam tabel
$pdf->SetFont('Arial', '', 12);
while ($row_transaksi = $result_transaksi->fetch_assoc()) {
    // Ambil ID_USER dari setiap baris pembayaran
    $id_user = $row_transaksi["ID_USER"];
    // Kueri SQL untuk mendapatkan nama petugas berdasarkan ID_USER
    $sql_user = "SELECT NAMA_USER FROM user WHERE ID_USER = $id_user";
    $result_user = $conn->query($sql_user);
    // Jika data petugas ditemukan
    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        $nama_user = $row_user["NAMA_USER"];
    } else {
        $nama_user = "Petugas Tidak Ditemukan"; // Jika kelas tidak ditemukan
    }
    // Ambil ID_PELANGGAN dari setiap baris pembayaran
    $id_pelanggan = $row_transaksi["ID_PELANGGAN"];
    // Kueri SQL untuk mendapatkan nama pelanggan berdasarkan ID_PELANGGAN
    $sql_pelanggan = "SELECT NAMA_PELANGGAN FROM pelanggan WHERE ID_PELANGGAN = '$id_pelanggan'";
    $result_pelanggan = $conn->query($sql_pelanggan);
    // Jika data pelanggan ditemukan
    if ($result_pelanggan->num_rows > 0) {
        $row_pelanggan = $result_pelanggan->fetch_assoc();
        $nama_pelanggan = $row_pelanggan["NAMA_PELANGGAN"];
    } else {
        $nama_pelanggan = "Pelanggan Tidak Ditemukan"; 
    }

    $kode_obat = $row_transaksi["KODE_OBAT"];
    // Kueri SQL untuk mendapatkan nama obat berdasarkan KODE_OBAT
    $sql_obat = "SELECT NAMA_OBAT FROM obat WHERE KODE_OBAT = '$kode_obat'";
    $result_obat = $conn->query($sql_obat);
    // Jika data obat ditemukan
    if ($result_obat->num_rows > 0) {
        $row_obat = $result_obat->fetch_assoc();
        $nama_obat = $row_obat["NAMA_OBAT"];
    } else {
        $nama_obat = "Obat Tidak Ditemukan"; 
    }

    $id_pembayaran = $row_transaksi["ID_PEMBAYARAN"];
    // Kueri SQL untuk mendapatkan jenis pembayaran berdasarkan ID_PEMBAYARAN
    $sql_pembayaran = "SELECT NAMA_PEMBAYARAN FROM pembayaran WHERE ID_PEMBAYARAN = $id_pembayaran";
    $result_pembayaran = $conn->query($sql_pembayaran);
    // Jika data pembayaran ditemukan
    if ($result_pembayaran->num_rows > 0) {
        $row_pembayaran = $result_pembayaran->fetch_assoc();
        $nama_pembayaran = $row_pembayaran["NAMA_PEMBAYARAN"];
    } else {
        $nama_pembayaran = "Pembayaran Tidak Ditemukan"; 
    }

    // Tengahkan teks di dalam sel
    $pdf->Cell(40, 10, $nama_user, 1, 0, 'C');
    $pdf->Cell(40, 10, $nama_pelanggan, 1, 0, 'C');
    $pdf->Cell(40, 10, $nama_obat, 1, 0, 'C');
    $pdf->Cell(40, 10, $row_transaksi["JUMLAH"], 1, 0, 'C');
    $pdf->Cell(40, 10, $row_transaksi["TOTAL"], 1, 0, 'C');
    $pdf->Cell(40, 10, $row_transaksi["TANGGAL"], 1, 0, 'C'); 
    $pdf->Cell(40, 10, $nama_pembayaran, 1, 1, 'C'); 
    // $pdf->Ln();
}
// Atur nama file untuk menyimpan PDF
$nama_file = 'data_pembayaran.pdf';

// Simpan file PDF ke dalam file yang ditentukan
$pdf->Output('F', $nama_file);

// Redirect pengguna ke file PDF yang disimpan
header("Location: $nama_file");
exit;
