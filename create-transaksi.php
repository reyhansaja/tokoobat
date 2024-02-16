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

    $sql_obat = "SELECT HARGA, STOK FROM obat WHERE KODE_OBAT = '$kode_obat'";
    $result_obat = $conn->query($sql_obat);

    if ($result_obat->num_rows > 0) {
        $row_obat = $result_obat->fetch_assoc();
        $harga = $row_obat['HARGA'];
        $stok = $row_obat['STOK'];

        // Check if there is enough stock
        $jumlah = $_POST['jumlah'];
        if ($stok >= $jumlah) {
            $total = $jumlah * $harga;

            // Update stock
            $newStok = $stok - $jumlah;
            $sql_update_stock = "UPDATE obat SET STOK = '$newStok' WHERE KODE_OBAT = '$kode_obat'";
            if ($conn->query($sql_update_stock) !== TRUE) {
                $errors[] = "Error updating stock: " . $conn->error;
            }

            // Continue with the transaction insertion
            // ... (your existing transaction insertion code)

            if (empty($errors)) {
                // Query untuk memasukkan data transaksi pelanggan baru ke dalam database
                $sql = "INSERT INTO penjualan (ID_PENJUALAN, ID_PELANGGAN, KODE_OBAT, ID_PEMBAYARAN, ID_USER, TANGGAL, JUMLAH, TOTAL) VALUES ('$uuid', '$id_pelanggan', '$kode_obat', '$id_pembayaran', '$id_user', '$tanggal', '$jumlah', '$total')";

                if ($conn->query($sql) === TRUE) {
                    header("Location: index-transaksi.php");
                    exit();
                } else {
                    // If there's an error in the transaction insertion, roll back the stock update
                    $errors[] = "Error: " . $sql . "<br>" . $conn->error;
                    $sql_rollback_stock = "UPDATE obat SET STOK = '$stok' WHERE KODE_OBAT = '$kode_obat'";
                    $conn->query($sql_rollback_stock); // Roll back the stock update
                }
            }
        } else {
            $errors[] = "Not enough stock available.";
        }
    } else {
        $errors[] = "KODE_OBAT tidak valid";
    }

    // If there are errors, display them
    if (!empty($errors)) {
        // Display errors
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entri Pembayaran</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
</head>

<body class="bg-success">
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
        <h2 class="text-center mb-3">Entri Transaksi</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="kode_obat" class="form-label">Nama Obat</label>
                <select class="form-select" id="kode_obat" name="kode_obat">
                    <option value="">Pilih Obat</option>
                    <?php
                    $sql_obat = "SELECT * FROM obat";
                    $result_obat = $conn->query($sql_obat);
                    if ($result_obat->num_rows > 0) {
                        // Loop melalui hasil query dan tampilkan dalam dropdown
                        while ($row_obat = $result_obat->fetch_assoc()) {
                            echo "<option value='" . $row_obat["KODE_OBAT"] . "'>" . $row_obat["NAMA_OBAT"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_pembayaran" class="form-label">Pembayaran</label>
                <select class="form-select" id="id_pembayaran" name="id_pembayaran">
                    <option value="">Pilih Pembayaran</option>
                    <?php
                    $sql_pembayaran = "SELECT * FROM pembayaran";
                    $result_pembayaran = $conn->query($sql_pembayaran);
                    if ($result_pembayaran->num_rows > 0) {
                        // Loop melalui hasil query dan tampilkan dalam dropdown
                        while ($row_pembayaran = $result_pembayaran->fetch_assoc()) {
                            echo "<option value='" . $row_pembayaran["ID_PEMBAYARAN"] . "'>" . $row_pembayaran["NAMA_PEMBAYARAN"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_pelanggan" class="form-label">Nama Pelanggan</label>
                <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                    <option value="">Pilih Pelanggan</option>
                    <?php
                    $sql_pelanggan = "SELECT * FROM pelanggan";
                    $result_pelanggan = $conn->query($sql_pelanggan);
                    if ($result_pelanggan->num_rows > 0) {
                        // Loop melalui hasil query dan tampilkan dalam dropdown
                        while ($row_pelanggan = $result_pelanggan->fetch_assoc()) {
                            echo "<option value='" . $row_pelanggan["ID_PELANGGAN"] . "'>" . $row_pelanggan["NAMA_PELANGGAN"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal Bayar</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal">
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah">
            </div>
            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
        <!-- Pesan error -->
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger mt-3" role="alert">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
            </div>
            <?php endif; ?>
        </div>
                    </div>

    <!-- Bootstrap JS -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <script>
    </script>

</body>

</html>