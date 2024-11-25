<?php
include '../../../config.php'; // Pastikan untuk menghubungkan ke database

header('Content-Type: application/json');

$sql = "SELECT id_pemasok, nama_pemasok FROM pemasok";
$result = $koneksi->query($sql);

$pemasok_options = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pemasok_options[] = $row;
    }
}

echo json_encode($pemasok_options);
$koneksi->close();
?>