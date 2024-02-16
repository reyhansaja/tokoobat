<?php
session_start();

// Jika pengguna tidak memiliki sesi atau role yang sesuai, arahkan ke halaman login
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

// Kueri SQL untuk mendapatkan data pembayaran dari tabel pembayaran
$sql_transaksi = "SELECT * FROM penjualan";
$result_transaksi = $conn->query($sql_transaksi);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Transaksi</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
</head>

<body class="bg-primary">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Selamat datang, <?php echo $_SESSION['nama_user']; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index-transaksi.php">Transaksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index-obat.php">Obat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index-kategori.php">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index-user.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5 card">
        <div class="mx-2 my-4">
        <h2>Data Transaksi</h2>
        <div class="my-4"> 
            <a class="btn btn-primary" href="create-transaksi.php">Entri Transaksi</a><a style="margin-left: 10px;" class="btn btn-success" href="createpdf.php">Print</a>
        </div>
        <table id="Table" class="display">
            <thead>
                <tr>
                    <th>Nama Petugas</th>
                    <th>Nama Pelanggan</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Tanggal Bayar</th>
                    <th>Jenis Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php
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
                        echo "  <tr>";
                        echo "  <td>" . $nama_user . "</td>";
                        echo "  <td>" . $nama_pelanggan . "</td>";
                        echo "  <td>" . $nama_obat . "</td>";
                        echo "  <td>" . $row_transaksi["JUMLAH"] . "</td>";
                        echo "  <td>" . $row_transaksi["TOTAL"] . "</td>";
                        echo "  <td>" . $row_transaksi["TANGGAL"] . "</td>";
                        echo "  <td>" . $nama_pembayaran . "</td>";
                        echo "  </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
            </div>

    <!-- jQuery -->
    <script src="node_modules/jquery/dist/jquery.js"></script>
    <!-- Bootstrap JS -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <!-- DataTables JS -->
    <script src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#Table').DataTable();
        });
    </script>
</body>

</html>