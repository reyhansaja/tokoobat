<?php
session_start();
require_once 'koneksi.php';
require 'vendor/autoload.php';

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== '1') {
    header("Location: login-pelanggan.php");
    exit();
}

use Dompdf\Dompdf;
use Dompdf\Options;

// Kueri SQL untuk mendapatkan data pembayaran dari tabel pembayaran
$id_user = $_SESSION['id_pelanggan'];
$sql_transaksi = "SELECT * FROM penjualan WHERE ID_PELANGGAN = '$id_user'";
$result_transaksi = $conn->query($sql_transaksi);

// Initialize Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

// Load HTML content
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


if ($result_transaksi->num_rows > 0) {
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
            $nama_pelanggan = "Pelanggan Tidak Ditemukan"; // Jika kelas tidak ditemukan
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
            $nama_obat = "Obat Tidak Ditemukan"; // Jika kelas tidak ditemukan
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
        $html .= "<tr>";
        $html .= "<td>" . $nama_user . "</td>";
        $html .= "<td>" . $nama_pelanggan . "</td>";
        $html .= "<td>" . $nama_obat . "</td>";
        $html .= "<td>" . $row_transaksi["JUMLAH"] . "</td>";
        $html .= "<td>" . $row_transaksi["TOTAL"] . "</td>";
        $html .= "<td>" . $row_transaksi["TANGGAL"] . "</td>";
        $html .= "<td>" . $nama_pembayaran . "</td>";
        $html .= "</tr>";
    }
} else {
    $html .= "<tr><td colspan='7'>Tidak ada data</td></tr>";
}

$html .= '</tbody>
            </table>
        </div>
    </div>
</body>

</html>';

$dompdf->loadHtml($html);

// Set paper size (optional)
$dompdf->setPaper('A4', 'landscape');

// Render PDF (first buffer)
$dompdf->render();

// Save PDF to file
$dompdf->stream('data_transaksi.pdf', ['Attachment' => 0]);
?>
