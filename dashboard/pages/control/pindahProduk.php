<?php
session_start();
include '../../../config.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk_lama = $_POST['id_produk_lama'];
    $id_produk_baru = $_POST['id_produk_baru'];

    // Validasi input
    if (empty($id_produk_lama) || empty($id_produk_baru)) {
        $response['message'] = 'ID produk tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Ambil data produk lama
        $stmt_lama = $koneksi->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt_lama->bind_param("i", $id_produk_lama);
        $stmt_lama->execute();
        $result_lama = $stmt_lama->get_result();
        $data_lama = $result_lama->fetch_assoc();

        // Ambil data produk baru
        $stmt_baru = $koneksi->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt_baru->bind_param("i", $id_produk_baru);
        $stmt_baru->execute();
        $result_baru = $stmt_baru->get_result();
        $data_baru = $result_baru->fetch_assoc();

        // Tukar data produk
        $stmt_update_lama = $koneksi->prepare("UPDATE produk SET 
            nama_produk = ?, 
            id_kategori = ?, 
            harga_beli = ?, 
            harga_jual = ?, 
            stok = ? 
            WHERE id_produk = ?");
        $stmt_update_lama->bind_param("sidisi", $data_baru['nama_produk'], $data_baru['id_kategori'], $data_baru['harga_beli'], $data_baru['harga_jual'], $data_baru['stok'], $id_produk_lama);
        $stmt_update_lama->execute();

        $stmt_update_baru = $koneksi->prepare("UPDATE produk SET 
            nama_produk = ?, 
            id_kategori = ?, 
            harga_beli = ?, 
            harga_jual = ?, 
            stok = ? 
            WHERE id_produk = ?");
        $stmt_update_baru->bind_param("sidisi", $data_lama['nama_produk'], $data_lama['id_kategori'], $data_lama['harga_beli'], $data_lama['harga_jual'], $data_lama['stok'], $id_produk_baru);
        $stmt_update_baru->execute();

        // Commit transaksi
        $koneksi->commit();

        $response['status'] = 'success';
        $response['message'] = 'Produk berhasil ditukar!';
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $koneksi->rollback();
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);