<?php
include '../../../config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Ambil data dari POST
    $id_pembelian = $_POST['id_pembelian'] ?? null; // ID pembelian yang akan diedit
    $id_pemasok = $_POST['id_pemasok'] ?? null;
    $harga_beli = $_POST['harga_beli'] ?? null;
    $jumlah = $_POST['jumlah'] ?? null;

    // Validasi input
    if (empty($id_pembelian) || empty($id_pemasok) || empty($harga_beli) || empty($jumlah)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    // Update data ke tabel pembelian
    $stmt = $koneksi->prepare("UPDATE pembelian SET id_pemasok = ?, harga_beli = ?, jumlah = ? WHERE id_pembelian = ?");
    $stmt->bind_param("idii", $id_pemasok, $harga_beli, $jumlah, $id_pembelian);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal mengupdate pembelian: ' . $stmt->error);
    }

    // Commit transaksi
    $koneksi->commit();
    echo json_encode(['status' => 'success', 'message' => 'Pembelian berhasil diperbarui']);
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $koneksi->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate pembelian: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $koneksi->close();
}