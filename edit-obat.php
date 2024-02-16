<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

// Inisialisasi variabel pesan error
$errors = [];

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan melalui form
    $kode_obat = $_POST['kode_obat'];
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
        // Nama file gambar baru
        $gambar_baru = basename($_FILES["gambar"]["name"]);

        // Path lengkap untuk menyimpan gambar baru
        $target_path_baru = $folder_storage . $gambar_baru;

        // Pindahkan gambar baru ke folder storage
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_path_baru)) {
            // Hapus gambar lama jika ada
            if (!empty($row_obat['GAMBAR'])) {
                $gambar_lama = $folder_storage . $row_obat['GAMBAR'];
                $gambar_baru = $folder_storage . $gambar;
                if ($gambar_baru !== $gambar_lama && file_exists($gambar_lama)) {
                    if (unlink($gambar_lama)) {
                        echo "File lama berhasil dihapus.";
                    } else {
                        $errors[] = "Gagal menghapus file lama.";
                    }
                }
            }
            // Update kolom gambar dengan nama gambar baru
            $gambar = $gambar_baru;
            echo "File " . htmlspecialchars(basename($_FILES["gambar"]["name"])) . " berhasil diunggah.";
        } else {
            $errors[] = "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }



    // Validasi input (misalnya, pastikan tidak ada field yang kosong)
    if (empty($nama_obat)) {
        $errors[] = "Nama Obat tidak boleh kosong.";
    }
    if (empty($id_kategori)) {
        $errors[] = "Kategori tidak boleh kosong.";
    }

    // Jika tidak ada error, update data ke dalam database
    if (empty($errors)) {
        // Query untuk update data obat berdasarkan KODE_OBAT
        $sql = "UPDATE obat SET ID_KATEGORI = '$id_kategori', NAMA_OBAT = '$nama_obat', HARGA = '$harga', KETERANGAN = '$keterangan', STOK = '$stok', EXP = '$exp'";
        if (!empty($gambar)) {
            $sql .= ", GAMBAR = '$gambar'";
        }
        $sql .= " WHERE KODE_OBAT = '$kode_obat'";

        if ($conn->query($sql) === TRUE) {
            // Jika update berhasil, redirect ke halaman index obat
            header("Location: index-obat.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat update, tampilkan pesan error
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    // Jika tidak disubmit via POST, ambil data obat dari database berdasarkan KODE_OBAT yang diberikan
    if (isset($_GET['id'])) {
        $kode_obat = $_GET['id'];
        // Query untuk mendapatkan data obat berdasarkan KODE_OBAT
        $sql_obat = "SELECT * FROM obat WHERE KODE_OBAT = '$kode_obat'";
        $result_obat = $conn->query($sql_obat);
        if ($result_obat->num_rows == 1) {
            $row_obat = $result_obat->fetch_assoc();
            $id_kategori = $row_obat['ID_KATEGORI'];
            $nama_obat = $row_obat['NAMA_OBAT'];
            $harga = $row_obat['HARGA'];
            $keterangan = $row_obat['KETERANGAN'];
            $stok = $row_obat['STOK'];
            $exp = $row_obat['EXP'];
        } else {
            // Jika tidak ada data obat dengan KODE_OBAT yang diberikan, tampilkan pesan error
            $errors[] = "Data obat tidak ditemukan";
        }
    } else {
        // Jika tidak ada KODE_OBAT yang diberikan, tampilkan pesan error
        $errors[] = "KODE_OBAT obat tidak diberikan";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Obat</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
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
        <h2>Edit Data Obat</h2>
        <!-- Form untuk edit data obat -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Gambar -->
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
            </div>
            <!-- NISN -->
            <div class="mb-3">
                <label for="kode_obat" class="form-label">KODE_OBAT</label>
                <input type="text" class="form-control" id="kode_obat" name="kode_obat" value="<?php echo $kode_obat; ?>" readonly>
            </div>
            <!-- NIS -->
            <div class="mb-3">
                <label for="nama_obat" class="form-label">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="<?php echo $nama_obat; ?>" required>
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
                            $selected = ($row_kategori['ID_KATEGORI'] == $id_kategori) ? 'selected' : '';
                            echo "<option value='" . $row_kategori["ID_KATEGORI"] . "' $selected>" . $row_kategori["NAMA_KATEGORI"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- harga -->
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" class="form-control" id="harga" name="harga" value="<?php echo $harga; ?>">
            </div>
            <!-- Keterangan -->
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo $keterangan; ?>">
            </div>
            <!-- Stok -->
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="text" class="form-control" id="stok" name="stok" value="<?php echo $stok; ?>">
            </div>
            <!-- EXP -->
            <div class="mb-3">
                <label for="exp" class="form-label">Expired</label>
                <input type="date" class="form-control" id="exp" name="exp" value="<?php echo $exp; ?>">
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