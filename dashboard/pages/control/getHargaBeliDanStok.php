<?php
session_start();
include '../../../config.php';

// Ambil ID Pembelian dari parameter GET
$id_pembelian = $_GET['id_pembelian'];

// Query untuk mendapatkan harga beli dari tabel pembelian
$sql = "SELECT 
            pb.harga_beli, 
            COALESCE(
                (SELECT SUM(jumlah) FROM pembelian WHERE id_produk = pb.id_produk) - 
                COALESCE((SELECT SUM(jumlah_jual) FROM penjualan WHERE id_produk = pb.id_produk), 0),
                0
            ) as stock
        FROM pembelian pb
        WHERE pb.id_pembelian = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_pembelian);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Kembalikan response JSON
    echo json_encode([
        'status' => 'success',
        'harga_beli' => $row['harga_beli'],
        'stock' => $row['stock']
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Data tidak ditemukan'
    ]);
}

$stmt->close();
$koneksi->close();
?>