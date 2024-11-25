<?php
include '../../../config.php';

// Menonaktifkan tampilan kesalahan untuk produksi
ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari request
    $id_penjualan = isset($_POST['id_penjualan']) ? $_POST['id_penjualan'] : null;
    $jumlah_jual = isset($_POST['jumlah_jual']) ? $_POST['jumlah_jual'] : null;

    // Validasi input
    if (empty($id_penjualan) || empty($jumlah_jual)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Siapkan dan eksekusi query untuk memperbarui penjualan
    $stmt = $koneksi->prepare("UPDATE penjualan SET jumlah_jual = ? WHERE id_penjualan = ?");
    
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $koneksi->error]);
        exit;
    }

    $stmt->bind_param("ii", $jumlah_jual, $id_penjualan);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Penjualan berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan yang dilakukan']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui penjualan: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid']);
}

$koneksi->close();
?>