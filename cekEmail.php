<?php
// cek_email.php
include 'config.php';

// Ambil email yang dikirimkan melalui AJAX
$email = $_POST['email'];

// Cek apakah email sudah terdaftar di database
$query = mysqli_query($koneksi, "SELECT * FROM login WHERE email = '$email' and password= 'password'");
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) > 0) {
    // Ambil data user
    $user = mysqli_fetch_assoc($result);
    $response = [
        'status' => 'found',
        'email' => $user['email'],
        'role' => $user['role'] // Sesuaikan dengan kolom yang ada di database
    ];
}else {
    // Jika email tidak ditemukan, kirimkan response 'not_found'
    $response = ['status' => 'not_found'];
}
echo json_encode($response);
?>
