<?php
include '../../../config.php';

// Tangkap ID produk dari request
$id_produk = $_POST['id_produk'] ?? null;

// Validasi input
if (!$id_produk) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID produk tidak valid'
    ]);
    exit;
}

try {
    // Mulai transaksi
    $koneksi->begin_transaction();

    // Hapus produk
    $stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
    $stmt->bind_param("i", $id_produk);
    
    if ($stmt->execute()) {
        // Commit transaksi
        $koneksi->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus'
        ]);
    } else {
        // Rollback transaksi
        $koneksi->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menghapus produk: ' . $stmt->error
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $koneksi->rollback();
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

$koneksi->close();