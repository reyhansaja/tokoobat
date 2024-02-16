<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php'; // koneksi

$sql_kategori = "SELECT * FROM kategori";
$result_kategori = $conn->query($sql_kategori);

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Lakukan kueri SQL untuk menghapus data siswa
    $sql_delete = "DELETE FROM kategori WHERE ID_KATEGORI = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Data kategori berhasil dihapus.";
        // Refresh halaman agar perubahan terlihat
        header("Location: index-kategori.php");
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Kategori</title>
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
        <h2>Data Kategori</h2>
        <div class="my-4">
        <a class="btn btn-primary" href="create-kategori.php">Tambah Kategori</a>
        </div>
        <table id="kategoriTable" class="display">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_kategori->num_rows > 0) {
                    while ($row_kategori = $result_kategori->fetch_assoc()) {
                        echo "  <tr>";
                        echo "  <td>" . $row_kategori["NAMA_KATEGORI"] . "</td>";
                        echo "  <td>
                        <a href='edit-kategori.php?id=" . $row_kategori["ID_KATEGORI"] . "' class='btn btn-primary'>Edit</a>
                        <a href='?delete_id=" . $row_kategori["ID_KATEGORI"] . "' class='btn btn-danger'>Delete</a>
                        </td>";
                        echo "  </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Tidak ada data</td></tr>";
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
            $('#kategoriTable').DataTable();
        });
    </script>
</body>

</html>