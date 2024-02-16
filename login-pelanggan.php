<?php
session_start();

// Koneksi ke database
require_once 'koneksi.php'; // Sesuaikan dengan lokasi file koneksi Anda

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan melalui formulir login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa kredensial pengguna
    $sql = "SELECT * FROM pelanggan WHERE USERNAME='$username' AND PASSWORD='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Jika kredensial benar, simpan informasi pengguna dalam session
        $row = $result->fetch_assoc();
        $_SESSION['id_pelanggan'] = $row['ID_PELANGGAN'];
        $_SESSION['username'] = $row['USERNAME'];
        $_SESSION['nama_pelanggan'] = $row['NAMA_PELANGGAN'];
        $_SESSION['alamat'] = $row['ALAMAT'];
        $_SESSION['role'] = $row['ROLE'];

        // Redirect ke halaman dashboard atau halaman lain yang sesuai
        header("Location: history-pembelian.php");
        exit();
    } else {
        // Jika kredensial salah, tampilkan pesan kesalahan atau redirect ke halaman login kembali
        echo "Username atau password salah.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <style>
        .custom-login-form {
            max-width: 400px;
            margin: auto;
            margin-top: 140px;
        }

        .custom-login-btn {
            width: 100%;
        }
    </style>
</head>

<body class="bg-success">
    <div class="container custom-login-form">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Login Pelanggan</h4>
                <form action="login-pelanggan.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary custom-login-btn">Login</button>
                    <a href="register-pelanggan.php" class="btn btn-dark mt-3 custom-login-btn">Register Pelanggan</a>
                </form>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS (optional) -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.js"></script>
</body>

</html>