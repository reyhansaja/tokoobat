<?php
session_start();

// Jika pengguna tidak memiliki sesi atau level yang sesuai, arahkan ke halaman login
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

use Ramsey\Uuid\Uuid;

$uuid = Uuid::uuid4();

// Inisialisasi variabel pesan error
$errors = [];

// Query untuk mendapatkan nilai nominal yang dipilih
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data yang dikirimkan melalui form
    $id_pelanggan = $_POST['id_pelanggan'];
    $kode_obat = $_POST['kode_obat'];
    $id_pembayaran = $_POST['id_pembayaran'];
    $id_user = $_SESSION['id_user'];
    $tanggal = $_POST['tanggal'];

    // Ambil data jumlah dan harga dari obat
    $sql_obat_data = "SELECT HARGA, STOK FROM obat WHERE KODE_OBAT = '$kode_obat'";
    $result_obat_data = $conn->query($sql_obat_data);

    if ($result_obat_data->num_rows > 0) {
        $row_obat_data = $result_obat_data->fetch_assoc();
        $harga = $row_obat_data['HARGA'];
        $current_stock = $row_obat_data['STOK'];
    } else {
        $errors[] = "KODE_OBAT tidak valid";
    }

    $jumlah = $_POST['jumlah'];

    // Validate if there is enough stock
    if ($jumlah > $current_stock) {
        $errors[] = "Stok tidak mencukupi";
    } else {
        // Calculate new stock
        $new_stock = $current_stock - $jumlah;

        // Update stock in the database
        $sql_update_stock = "UPDATE obat SET STOK = $new_stock WHERE KODE_OBAT = '$kode_obat'";
        if ($conn->query($sql_update_stock) !== TRUE) {
            $errors[] = "Error updating stock: " . $conn->error;
        }
    }

    $total = $jumlah * $harga;

    // Jika tidak ada error, masukkan data ke dalam database
    if (empty($errors)) {
        // Query untuk memasukkan data transaksi pelanggan baru ke dalam database
        $sql = "INSERT INTO penjualan (ID_PENJUALAN, ID_PELANGGAN, KODE_OBAT, ID_PEMBAYARAN, ID_USER, TANGGAL, JUMLAH, TOTAL) VALUES ('$uuid', '$id_pelanggan', '$kode_obat', '$id_pembayaran', '$id_user', '$tanggal', '$jumlah', '$total')";

        if ($conn->query($sql) === TRUE) {
            header("Location: index-transaksi.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat insert, tampilkan pesan error
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Obat</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
</head>

<body class="bg-info">
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
        <h2>Data Obat</h2>
        <div class="my-4">
            <a class="btn btn-primary" href="create-obat.php">Tambah Obat</a>
        </div>
        <table id="obatTable" class="display">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Keterangan</th>
                    <th>Stok</th>
                    <th>EXP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_obat = "SELECT * FROM obat";
                $result_obat = $conn->query($sql_obat);
                
                if ($result_obat !== false && $result_obat->num_rows > 0) {
                    while ($row_obat = $result_obat->fetch_assoc()) {
                        echo "  <tr>";
                        echo "  <td><img src='storage/" . $row_obat["GAMBAR"] . "' alt='gambar obat' style='max-width: 100px;'></td>";
                        echo "  <td>" . $row_obat["NAMA_OBAT"] . "</td>";
                        // Ambil ID_KATEGORI dari setiap baris kategori
                        $id_kategori = $row_obat["ID_KATEGORI"];
                        // Kueri SQL untuk mendapatkan nama kelas berdasarkan ID_KATEGORI
                        $sql_kategori = "SELECT NAMA_KATEGORI FROM kategori WHERE ID_KATEGORI = $id_kategori";
                        $result_kategori = $conn->query($sql_kategori);

                        // Jika data kelas ditemukan
                        if ($result_kategori->num_rows > 0) {
                            $row_kategori = $result_kategori->fetch_assoc();
                            $nama_kategori = $row_kategori["NAMA_KATEGORI"];
                        } else {
                            $nama_kategori = "Kategori Tidak Ditemukan"; // Jika kategori tidak ditemukan
                        }
                        echo "  <td>" . $nama_kategori . "</td>";
                        echo "  <td>" . $row_obat["HARGA"] . "</td>";
                        echo "  <td>" . $row_obat["KETERANGAN"] . "</td>";
                        echo "  <td>" . $row_obat["STOK"] . "</td>";
                        echo "  <td>" . $row_obat["EXP"] . "</td>";
                        echo "  <td>
                                <a href='edit-obat.php?id=" . $row_obat["KODE_OBAT"] . "' class='btn btn-primary'>Edit</a>
                                <a href='?delete_id=" . $row_obat["KODE_OBAT"] . "' class='btn btn-danger'>Delete</a>
                                </td>";
                        echo "  </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data</td></tr>";
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
            $('#obatTable').DataTable();
        });
    </script>
</body>

</html>