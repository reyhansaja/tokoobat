<?php
session_start();

// middleware
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'koneksi.php'; // koneksi

$sql_user = "SELECT * FROM user";
$result_user = $conn->query($sql_user);

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Lakukan kueri SQL untuk menghapus data siswa
    $sql_delete = "DELETE FROM user WHERE ID_USER = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Data user berhasil dihapus.";
        // Refresh halaman agar perubahan terlihat
        header("Location: index-user.php");
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
    <title>Index User</title>
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
        <h2>Data User</h2>
        <div class="my-3">
            <a class="btn btn-primary" href="create-user.php">Tambah User</a>
        </div>
        <table id="userTable" class="display">
            <thead>
                <tr>
                    <th>Nama User</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_user->num_rows > 0) {
                    while ($row_user = $result_user->fetch_assoc()) {
                        echo "  <tr>";
                        echo "  <td>" . $row_user["NAMA_USER"] . "</td>";
                        echo "  <td>" . $row_user["USERNAME"] . "</td>";
                        echo "  <td>
                        <a href='edit-user.php?id=" . $row_user["ID_USER"] . "' class='btn btn-primary'>Edit</a>
                        <a href='?delete_id=" . $row_user["ID_USER"] . "' class='btn btn-danger'>Delete</a>
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
    <!-- <script src="/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js"></script> -->

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#userTable').DataTable();
        });
    </script>
</body>

</html>