<?php
$host = 'localhost';
$dbname = 'grosir'; 
$username = 'root'; 
$password = '';

$koneksi = mysqli_connect($host, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}


?>