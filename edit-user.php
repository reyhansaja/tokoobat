<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

use Ramsey\Uuid\Uuid;

// Inisialisasi variabel pesan error
$errors = [];

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan melalui form
    $id_user = $_POST['id_user'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_user = $_POST['nama_user'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];

    // Jika tidak ada error, update data ke dalam database
    if (empty($errors)) {
        // Query untuk update data user berdasarkan ID
        $sql = "UPDATE user SET USERNAME = '$username', PASSWORD = '$password', NAMA_USER = '$nama_user', ROLE = '$role', ALAMAT = '$alamat' WHERE ID_USER = '$id_user'";

        if ($conn->query($sql) === TRUE) {
            header("Location: index-user.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat update, tampilkan pesan error
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    // Jika tidak disubmit via POST, ambil data user dari database berdasarkan ID yang diberikan
    if (isset($_GET['id'])) {
        $id_user = $_GET['id'];
        // Query untuk mendapatkan data user berdasarkan ID
        $sql_user = "SELECT * FROM user WHERE ID_USER = '$id_user'";
        $result_user = $conn->query($sql_user);
        if ($result_user->num_rows == 1) {
            $row_user = $result_user->fetch_assoc();
            $username = $row_user['USERNAME'];
            $password = $row_user['PASSWORD'];
            $nama_user = $row_user['NAMA_USER'];
            $role = $row_user['ROLE'];
            $alamat = $row_user['ALAMAT'];
        } else {
            // Jika tidak ada data user dengan ID yang diberikan, tampilkan pesan error
            $errors[] = "Data user tidak ditemukan";
        }
    } else {
        // Jika tidak ada ID yang diberikan, tampilkan pesan error
        $errors[] = "ID user tidak diberikan";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="index-history.php">History</a>
                    </li> -->
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
        <h2>Edit Data User</h2>
        <!-- Form untuk edit data user -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
            </div>
            <div class="mb-3">
                <label for="nama_user" class="form-label">Nama User</label>
                <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?php echo $nama_user; ?>">
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $alamat; ?>">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Pilih Role</option>
                    <option value="admin" <?php if ($role == "admin") echo "selected"; ?>>Admin</option>
                    <option value="petugas" <?php if ($role == "petugas") echo "selected"; ?>>Petugas</option>
                </select>
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