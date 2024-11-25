<?php
$namaProduk = $_GET['nama_produk'];

// Query ke database untuk mengambil stok baru
$stokBaru = // hasil query ke database

header('Content-Type: application/json');
echo json_encode(['stok_baru' => $stokBaru]);
?>