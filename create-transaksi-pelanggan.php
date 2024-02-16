<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== '1') {
    header("Location: login-pelanggan.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

use Ramsey\Uuid\Uuid;

$uuid = Uuid::uuid4()->toString();

// Inisialisasi variabel pesan error
$errors = [];
// Query untuk mendapatkan nilai nominal dari total harga yang dipilih

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data yang dikirimkan melalui form
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $kode_obat = $_POST['kode_obat'];
    $id_pembayaran = $_POST['id_pembayaran'];
    $id_user = $_POST['id_user'];
    $tanggal = $_POST['tanggal'];

    $sql_obat = "SELECT HARGA, STOK FROM obat WHERE KODE_OBAT = '$kode_obat'";
    $result_obat = $conn->query($sql_obat);

    if ($result_obat->num_rows > 0) {
        $row_obat = $result_obat->fetch_assoc();
        $harga = $row_obat['HARGA'];
        $stok = $row_obat['STOK'];
    } else {
        $errors[] = "KODE_OBAT tidak valid";
    }

    $jumlah = $_POST['jumlah'];
    $total = $jumlah * $harga;

    if ($stok >= $jumlah) {
        // Update stock
        $newStok = $stok - $jumlah;
        $sql_update_stock = "UPDATE obat SET STOK = '$newStok' WHERE KODE_OBAT = '$kode_obat'";
        if ($conn->query($sql_update_stock) !== TRUE) {
            $errors[] = "Error updating stock: " . $conn->error;
        }
    } else {
        $errors[] = "Not enough stock available.";
    }

    if (empty($kode_obat)) {
        $errors[] = "Nama Obat tidak boleh kosong.";
    }
    if (empty($id_pembayaran)) {
        $errors[] = "Jenis Pembayaran tidak boleh kosong.";
    }
    if (empty($jumlah)) {
        $errors[] = "Kuantitas tidak boleh kosong.";
    }

    // Jika tidak ada error, masukkan data ke dalam database
    if (empty($errors)) {
        // Query untuk memasukkan data transaksi pelanggan baru ke dalam database
        $sql = "INSERT INTO penjualan (ID_PENJUALAN, ID_PELANGGAN, KODE_OBAT, ID_PEMBAYARAN, ID_USER, TANGGAL, JUMLAH, TOTAL) VALUES ('$uuid', '$id_pelanggan', '$kode_obat', '$id_pembayaran', '$id_user', '$tanggal', '$jumlah', '$total')";

        if ($conn->query($sql) === TRUE) {
            header("Location: history-pembelian.php");
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
    <title>Entri Pembayaran</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
</head>

<body class="bg-success">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Selamat datang, <?php echo $_SESSION['nama_pelanggan']; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="history-pembelian.php">History</a>
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
            <h2>Entri Transaksi</h2>
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
                <!-- Inside the "Nama Petugas" dropdown -->
                <div class="mb-3">
                    <label for="id_user" class="form-label">Nama Petugas</label>
                    <select class="form-select" id="id_user" name="id_user">
                        <?php
                        $sql_admin_user = "SELECT * FROM user WHERE NAMA_USER = 'admin'";
                        $result_admin_user = $conn->query($sql_admin_user);

                        if ($result_admin_user->num_rows > 0) {
                            // Display the 'admin' user in the dropdown
                            $row_admin_user = $result_admin_user->fetch_assoc();
                            echo "<option value='" . $row_admin_user["ID_USER"] . "' selected>" . $row_admin_user["NAMA_USER"] . "</option>";
                        } else {
                            echo "<option value='' disabled>No admin user found</option>";
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
                    <input type="number" class="form-control" id="jumlah" name="jumlah" oninput="updateTotal()">
                </div>
                <div class="mb-3">
                    <label for="total" class="form-label">Total Harga</label>
                    <input type="text" class="form-control" id="total" name="total" readonly>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary">Submit</button>
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
        $(document).ready(function () {
            // Define the updateTotal function
            function updateTotal() {
                var harga = <?php echo json_encode($harga); ?>;
                var jumlah = parseFloat($('#jumlah').val()) || 0;
                var total = jumlah * harga;
                $('#total').val(total.toFixed(2));
            }

            // Attach the updateTotal function to the input's 'input' event
            $('#jumlah').on('input', updateTotal);

            // Initial calculation on page load
            updateTotal();
        });
    </script>

    </body>
</html>
