<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== '1') {
    header("Location: login-pelanggan.php");
    exit();
}

// autoloader
require 'vendor/autoload.php';

require_once 'koneksi.php'; // koneksi

if (!isset($_GET['id'])) {
    echo "Invalid request. Missing 'id' parameter.";
    exit;
}

$id_transaksi = $_GET['id'];

// Kueri SQL untuk mendapatkan data pembayaran dari tabel pembayaran
$sql_transaksi = "SELECT * FROM penjualan WHERE ID_PENJUALAN = ?";
$stmt = $conn->prepare($sql_transaksi);
$stmt->bind_param("s", $id_transaksi);
$stmt->execute();
$result_transaksi = $stmt->get_result();
$stmt->close();

// panggil dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

// Ngeload konten html
$html = '<!DOCTYPE html>
<html>
<head>
    <style>
        .text-center:text-align:center;
    </style>
</head>
<body>';
$html .= '<h1 class="text-center">Struk Penjualan Toko Obat</h1>';
$html .= '<table border="1" cellspacing="0" cellpadding="5">
            <tr>
                <th>Nama Petugas</th>
                <th>Nama Pelanggan</th>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal</th>
                <th>Jenis Pembayaran</th>
            </tr>';

            while ($row_transaksi = $result_transaksi->fetch_assoc()) {
                $id_user = $row_transaksi["ID_USER"];
                $sql_user = "SELECT NAMA_USER FROM user WHERE ID_USER = $id_user";
                $result_user = $conn->query($sql_user);
                if ($result_user->num_rows > 0) {
                    $row_user = $result_user->fetch_assoc();
                    $nama_user = $row_user["NAMA_USER"];
                } else {
                    $nama_user = "Petugas Tidak Ditemukan";
                }
                $id_pelanggan = $row_transaksi["ID_PELANGGAN"];
                $sql_pelanggan = "SELECT NAMA_PELANGGAN FROM pelanggan WHERE ID_PELANGGAN = '$id_pelanggan'";
                $result_pelanggan = $conn->query($sql_pelanggan);
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
                    $nama_pembayaran = "Pembayaran Tidak Ditemukan"; // Jika kelas tidak ditemukan
                }
            

    // masukin data to $html
    $html .= '<tr>
                <td>' . $nama_user . '</td>
                <td>' . $nama_pelanggan . '</td>
                <td>' . $nama_obat . '</td>
                <td>' . $row_transaksi["JUMLAH"] . '</td>
                <td>' . $row_transaksi["TOTAL"] . '</td>
                <td>' . $row_transaksi["TANGGAL"] . '</td>
                <td>' . $nama_pembayaran . '</td>
              </tr>';
}

$html .= '</table></body></html>';

// Masukin html ke dompdf
$dompdf->loadHtml($html);

// ngeset kertas
$dompdf->setPaper('A4', 'landscape');

//render page
$dompdf->render();

// output pdf
$nama_file = 'data_pembayaran_pelanggan1.pdf';
$dompdf->stream($nama_file, array('Attachment' => 0));
$error = $dompdf->getError();
if (!empty($error)) {
    echo $error;
}