<?php
session_start();

//middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

use Ramsey\Uuid\Uuid;

$uuid = Uuid::uuid4()->toString();
// Inisialisasi variabel pesan error
$errors = [];

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan melalui form
    $id_kategori = $_POST['id_kategori'];
    $nama_obat = $_POST['nama_obat'];
    $harga = $_POST['harga'];
    $keterangan = $_POST['keterangan'];
    $stok = $_POST['stok'];
    $exp = $_POST['exp'];
    // Tentukan folder penyimpanan gambar
    $folder_storage = "storage/";

    // Simpan gambar jika sudah dipilih
    if (!empty($_FILES['gambar']['name'])) {
        // Nama file gambar
        $gambar = basename($_FILES["gambar"]["name"]);

        // Path lengkap untuk menyimpan gambar
        $target_path = $folder_storage . $gambar;

        // Pindahkan gambar ke folder storage
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_path)) {
            echo "File " . htmlspecialchars(basename($_FILES["gambar"]["name"])) . " berhasil diunggah.";
        } else {
            $errors[] = "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }


    // Validasi input agar tidak ada field yang kosong

    if (empty($nama_obat)) {
        $errors[] = "Nama Obat tidak boleh kosong.";
    }
    if (empty($id_kategori)) {
        $errors[] = "Kategori tidak boleh kosong.";
    }

    // Jika tidak ada error, masukkan data ke dalam database
    if (empty($errors)) {
        // Query untuk memasukkan data obat baru ke dalam database
        $sql = "INSERT INTO obat (KODE_OBAT, GAMBAR, ID_KATEGORI, NAMA_OBAT, HARGA, KETERANGAN, STOK, EXP) VALUES ('$uuid', '$gambar', '$id_kategori', '$nama_obat', '$harga', '$keterangan', '$stok', '$exp')";

        if ($conn->query($sql) === TRUE) {
            // Jika insert berhasil, redirect ke halaman detail obat
            header("Location: index-obat.php");
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
    <title>Create Obat</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <style>
        .border-neon {
            border: 2px solid #f00;
            box-shadow: 0 0 5px #f00;
        }
    </style>
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
        <h2>Tambah Data Obat</h2>
        <!-- Form untuk input data obat -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Gambar -->
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
            </div>
            <!-- Nama Obat -->
            <div class="mb-3">
                <label for="nama_obat" class="form-label">Nama Obat</label>
                <input type="text" class="form-control <?php if (!empty($errors) && empty($nama_obat)) echo 'border-neon'; ?>" id="nama_obat" name="nama_obat" value="<?php echo isset($_POST['nama_obat']) ? $_POST['nama_obat'] : ''; ?>" required>
                <?php if (!empty($errors) && empty($nama_obat)) echo '<div class="text-danger">Nama Obat tidak boleh kosong.</div>'; ?>
            </div>
            <!-- ID Kategori -->
            <div class="mb-3">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select class="form-select" id="id_kategori" name="id_kategori">
                    <option value="">Pilih Kategori</option>
                    <?php
                    // Query untuk mengambil data kategori
                    $sql_kategori = "SELECT * FROM kategori";
                    $result_kategori = $conn->query($sql_kategori);

                    // Periksa apakah query berhasil dijalankan
                    if ($result_kategori->num_rows > 0) {
                        // Loop melalui hasil query dan tampilkan dalam dropdown
                        while ($row_kategori = $result_kategori->fetch_assoc()) {
                            echo "<option value='" . $row_kategori["ID_KATEGORI"] . "'>" . $row_kategori["NAMA_KATEGORI"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" class="form-control" id="harga" name="harga">
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan">
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="text" class="form-control" id="stok" name="stok">
            </div>
            <div class="mb-3">
                <label for="exp" class="form-label">Expired</label>
                <input type="date" class="form-control" id="exp" name="exp">
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
</body>

</html>