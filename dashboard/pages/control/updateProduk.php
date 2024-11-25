<?php
include '../../../config.php';

// Tangkap data dari request
$id_produk = $_POST['id_produk'] ?? null;
$id_kategori = $_POST['id_kategori'] ?? null;
$harga_jual = $_POST['harga_jual'] ?? null;

// Validasi input
if (!$id_produk || !$id_kategori || !$harga_jual) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

try {
    // Persiapkan statement SQL untuk update
    $stmt = $koneksi->prepare("UPDATE produk SET id_kategori = ?, harga_jual = ? WHERE id_produk = ?");
    $stmt->bind_param("iii", $id_kategori, $harga_jual, $id_produk);
    
    // Eksekusi query
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Berhasil memperbarui produk'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui produk: ' . $stmt->error
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

$koneksi->close();