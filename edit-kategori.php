<?php
session_start();

// Middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

// Inisialisasi variabel pesan error
$errors = [];

// Cek apa parameter id ada dalam URL
if (!isset($_GET['id'])) {
    header("Location: index-kategori.php");
    exit();
}

$id_kategori = $_GET['id'];

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data yang dikirimkan melalui form
    $nama = $_POST['nama'];

    // Jika tidak ada error, update data di dalam database
    if (empty($errors)) {
        // Query untuk memperbarui data kategori
        $sql = "UPDATE kategori SET NAMA_KATEGORI='$nama' WHERE ID_KATEGORI='$id_kategori'";

        if ($conn->query($sql) === TRUE) {
            header("Location: index-kategori.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat update, tampilkan pesan error
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Query untuk mendapatkan data kategori berdasarkan ID
$sql_kategori = "SELECT * FROM kategori WHERE ID_KATEGORI='$id_kategori'";
$result_kategori = $conn->query($sql_kategori);

// Periksa apakah ID kategori yang dimaksud ada dalam database
if ($result_kategori->num_rows == 0) {
    header("Location: index-kategori.php");
    exit();
}

$row_kategori = $result_kategori->fetch_assoc();
$nama_kategori = $row_kategori['NAMA_KATEGORI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
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
        <div class="mx-2 my-5">
        <h2>Edit Data Kategori</h2>
        <!-- Form untuk input data kategori -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_kategori); ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama_kategori; ?>">
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