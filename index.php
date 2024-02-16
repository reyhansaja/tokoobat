<?php
session_start();

// Jika pengguna tidak memiliki sesi atau level yang sesuai, arahkan ke halaman login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    // exit();
}

if (!isset($_SESSION['username']) || $_SESSION['role'] == 'petugas') {
    header("Location: index-transaksi.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
</head>

<body class="bg-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Selamat datang, <?php echo $_SESSION['nama_user']; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="btn btn-danger" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 card">
        <div class="mx-2 my-4">
        <p>Silakan pilih opsi di bawah ini:</p>
        <div class="list-group">
            <a href="index-transaksi.php" class="list-group-item list-group-item-action">Halaman Transaksi</a>
        </div>
        <div class="list-group mt-3">
            <a href="index-obat.php" class="list-group-item list-group-item-action">Halaman Obat</a>
        </div>
        <div class="list-group mt-3">
            <a href="index-kategori.php" class="list-group-item list-group-item-action">Halaman Kategori</a>
        </div>
        <div class="list-group mt-3">
            <a href="index-user.php" class="list-group-item list-group-item-action">Halaman User</a>
        </div>
</div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
</body>

</html>