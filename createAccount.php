<?php 
// koneksi database
include 'config.php';
 

$email     = $_POST['email'];
$password   = $_POST['pwd'];

// menginput data ke database
mysqli_query($koneksi,"insert into login (email,pwd) values('$email','$password')");
 
// mengalihkan halaman kembali ke index.php
header("location:login.php");
 
?>