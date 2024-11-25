<?php
// Pastikan file ini dilindungi dan hanya bisa diakses setelah login
session_start();
include '../../../config.php'; // Sesuaikan path ke file konfigurasi

function sendJsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

try {
    // Pastikan request adalah POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metode request tidak valid");
    }

    // Ambil data dari POST
    $old_produk_id = filter_input(INPUT_POST, 'old_produk_id', FILTER_VALIDATE_INT);
    $new_produk_id = filter_input(INPUT_POST, 'new_produk_id', FILTER_VALIDATE_INT);
    $old_jumlah = filter_input(INPUT_POST, 'old_jumlah', FILTER_VALIDATE_INT);
    $new_jumlah = filter_input(INPUT_POST, 'new_jumlah', FILTER_VALIDATE_INT);
    $id_pembelian = filter_input(INPUT_POST, 'id_pembelian', FILTER_VALIDATE_INT);

    // Validasi input
    if (!$old_produk_id || !$new_produk_id || !$old_jumlah || !$new_jumlah || !$id_pembelian) {
        throw new Exception("Parameter tidak valid");
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    // Kurangi stok produk lama
    $update_old_stok_sql = "UPDATE stok SET sisa = sisa - ? WHERE id_produk = ?";
    $update_old_stok_stmt = $koneksi->prepare($update_old_stok_sql);
    $update_old_stok_stmt->bind_param("ii", $old_jumlah, $old_produk_id);
    
    if (!$update_old_stok_stmt->execute()) {
        throw new Exception("Gagal mengupdate stok produk lama: " . $update_old_stok_stmt->error);
    }

    // Tambah stok produk baru
    $update_new_stok_sql = "INSERT INTO stok (id_produk, sisa) 
                             VALUES (?, ?) 
                             ON DUPLICATE KEY UPDATE sisa = sisa + ?";
    $update_new_stok_stmt = $koneksi->prepare($update_new_stok_sql);
    $update_new_stok_stmt->bind_param("iii", $new_produk_id, $new_jumlah, $new_jumlah);
    
    if (!$update_new_stok_stmt->execute()) {
        throw new Exception("Gagal mengupdate stok produk baru: " . $update_new_stok_stmt->error);
    }

    // Commit transaksi
    $koneksi->commit();

    // Ambil data stok terbaru untuk produk baru
    $get_stok_sql = "SELECT * FROM stok WHERE id_produk = ?";
    $get_stok_stmt = $koneksi->prepare($get_stok_sql);
    $get_stok_stmt->bind_param("i", $new_produk_id);
    $get_stok_stmt->execute();
    $stok_result = $get_stok_stmt->get_result();
    $stok_data = $stok_result->fetch_assoc();

    // Kirim response sukses
    sendJsonResponse('success', 'Stok berhasil diupdate', [
        'stok_data' => $stok_data
    ]);

} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    if ($koneksi && $koneksi->ping()) {
        $koneksi->rollback();
    }
    
    // Log error untuk debugging
    error_log("Error in updateStok.php: " . $e->getMessage());
    
    // Kirim response error
    sendJsonResponse('error', $e->getMessage());

} finally {
    // Tutup statement
    if (isset($update_old_stok_stmt)) {
        $update_old_stok_stmt->close();
    }
    if (isset($update_new_stok_stmt)) {
        $update_new_stok_stmt->close();
    }
    if (isset($get_stok_stmt)) {
        $get_stok_stmt->close();
    }
    
    // Tutup koneksi database
    if ($koneksi && $koneksi->ping()) {
        $koneksi->close();
    }
    
    exit();
}
?>