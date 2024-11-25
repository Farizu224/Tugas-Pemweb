<?php
session_start();
include 'config.php'; // Koneksi ke database

// Menangkap data yang dikirim dari form login
$email = $_POST['email'];
$password = $_POST['pwd'];
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Menyeleksi data pada tabel login berdasarkan email dan pwd yang sesuai
$query = "SELECT * FROM login WHERE email = '$email' AND pwd = '$password'";
$data = mysqli_query($koneksi, $query);

// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($data);

if ($cek > 0) {
    // Mengambil data dari hasil query
    $row = mysqli_fetch_assoc($data);
    
    // Menyimpan email dan role ke dalam sesi
    $_SESSION['email'] = $row['email'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['status'] = "login";
    
    // Redirect ke halaman dashboard
    header("Location: dashboard/index.php");
    exit();
} else {
    // Jika akun tidak ditemukan atau password salah
    $_SESSION['error'] = "Akun tidak ditemukan atau password salah";
    header("Location: login.php");
    exit();
}
?>
