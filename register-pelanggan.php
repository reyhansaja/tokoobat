<?php
session_start();

require_once 'koneksi.php'; // koneksi

use Ramsey\Uuid\Uuid;

$uuid = Uuid::uuid4()->toString();

// Inisialisasi variabel pesan error
$errors = [];

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data yang dikirimkan melalui form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];

    // Jika tidak ada error, masukkan data ke dalam database
    if (empty($errors)) {
        // Query untuk memasukkan data pelanggan baru ke dalam database
        $sql = "INSERT INTO pelanggan (ID_PELANGGAN, USERNAME, PASSWORD, NAMA_PELANGGAN, ALAMAT, ROLE) VALUES ('$uuid', '$username', '$password', '$nama_pelanggan', '$alamat', '$role')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login-pelanggan.php");
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
    <title>Register Pelanggan</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <style>
        /* CSS untuk menengahkan form dan mengatur lebar */
        .custom-login-form {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
        }
        .custom-login-btn {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container mt-5 custom-login-form">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Register Pelanggan</h4>
                <form action="register-pelanggan.php" method="post">
                    <input type="hidden" name="role" value="1">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" required>
                    </div>
                    <button type="submit" class="btn btn-primary custom-login-btn">Register</button>
                    <a href="login-pelanggan.php" class="btn btn-secondary mt-3 custom-login-btn">Login Pelanggan</a>
                </form>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS (optional) -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.js"></script>
</body>

</html>