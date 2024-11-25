<?php
$namaProduk = $_GET['nama_produk'];

// Query ke database untuk mengambil harga_beli baru
$hargaBeliBaru = // hasil query ke database

header('Content-Type: application/json');
echo json_encode(['harga_beli_baru' => $hargaBeliBaru]);
?>