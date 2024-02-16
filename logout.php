<?php
session_start();

// Hapus semua variabel sesi
session_unset();

// Hapus sesi dari server
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
