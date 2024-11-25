<?php
include '../../../config.php';

// Pastikan ada parameter id_pembelian
if (!isset($_POST['id_pembelian'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'ID pembelian tidak ditemukan'
    ]);
    exit;
}

$id_pembelian = $_POST['id_pembelian'];

// Mulai transaksi
$koneksi->begin_transaction();

try {
    // 1. Hapus dari tabel detail_pembelian terlebih dahulu
    $stmt_detail = $koneksi->prepare("DELETE FROM detail_pembelian WHERE id_pembelian = ?");
    $stmt_detail->bind_param("i", $id_pembelian);
    $stmt_detail->execute();

    // 2. Hapus dari tabel stok (jika ada)
    $stmt_stok = $koneksi->prepare("DELETE FROM stok WHERE id_pembelian = ?");
    $stmt_stok->bind_param("i", $id_pembelian);
    $stmt_stok->execute();

    // 3. Hapus dari tabel pembelian
    $stmt_pembelian = $koneksi->prepare("DELETE FROM pembelian WHERE id_pembelian = ?");
    $stmt_pembelian->bind_param("i", $id_pembelian);
    $stmt_pembelian->execute();

    // Commit transaksi
    $koneksi->commit();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Pembelian berhasil dihapus'
    ]);
} catch (Exception $e) {
    // Rollback transaksi jika gagal
    $koneksi->rollback();

    error_log("Error menghapus pembelian: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'Gagal menghapus pembelian: ' . $e->getMessage()
    ]);
}

// Tutup statement dan koneksi
if (isset($stmt_detail)) $stmt_detail->close();
if (isset($stmt_stok)) $stmt_stok->close();
if (isset($stmt_pembelian)) $stmt_pembelian->close();
$koneksi->close();
?>