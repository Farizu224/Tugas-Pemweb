<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../../config.php';

$response = ['status' => 'error', 'message' => 'Gagal menambah produk'];

// Cek koneksi
if ($koneksi->connect_error) {
    $response['message'] = 'Koneksi database gagal: ' . $koneksi->connect_error;
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pembelian = $_POST['id_pembelian'] ?? null;
    $id_kategori = $_POST['id_kategori'] ?? null;
    $harga_jual = $_POST['harga_jual'] ?? null;

    if (empty($id_pembelian) || empty($id_kategori) || empty($harga_jual)) {
        $response['message'] = 'Semua field harus diisi';
        echo json_encode($response);
        exit;
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Ambil detail pembelian
        $stmt_pembelian = $koneksi->prepare("SELECT id_produk, harga_beli FROM pembelian WHERE id_pembelian = ?");
        if (!$stmt_pembelian) {
            throw new Exception('Gagal mempersiapkan statement: ' . $koneksi->error);
        }
        $stmt_pembelian->bind_param('i', $id_pembelian);
        $stmt_pembelian->execute();
        $result_pembelian = $stmt_pembelian->get_result();
    
        if ($result_pembelian->num_rows === 0) {
            throw new Exception('Pembelian tidak ditemukan');
        }
    
        $pembelian = $result_pembelian->fetch_assoc();
        $id_produk = $pembelian['id_produk'];
        $harga_beli = $pembelian['harga_beli'];
    
        // Ambil id_kategori dari tabel kategori
        $stmt_kategori = $koneksi->prepare("SELECT id_kategori FROM kategori WHERE id_kategori"); // Ganti kondisi_anda dengan kondisi yang sesuai
        if (!$stmt_kategori) {
            throw new Exception('Gagal mempersiapkan statement: ' . $koneksi->error);
        }
        $stmt_kategori->execute();
        $result_kategori = $stmt_kategori->get_result();
    
        if ($result_kategori->num_rows === 0) {
            throw new Exception('Kategori tidak ditemukan');
        }
    
        $kategori = $result_kategori->fetch_assoc();
        $id_kategori = $kategori['id_kategori'];
    
        // Cek apakah produk sudah ada
        $stmt_check = $koneksi->prepare("SELECT COUNT(*) FROM produk WHERE id_produk = ?");
        $stmt_check->bind_param('i', $id_produk);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();
    
        if ($count > 0) {
            throw new Exception('Produk dengan ID ' . $id_produk . ' sudah ada di tabel produk.');
        }
    
        // Insert produk
        $stmt = $koneksi->prepare("INSERT INTO produk (id_produk, id_kategori, harga_beli, harga_jual) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Gagal mempersiapkan statement: ' . $koneksi->error);
        }
        $harga_jual = $harga_beli * 1.2; // Contoh markup harga jual
        $stmt->bind_param('iidd', $id_produk, $id_kategori, $harga_beli, $harga_jual);
        
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan produk: ' . $stmt->error);
        }
        
        // Commit transaksi
        $koneksi->commit();
        $response = ['status' => 'success', 'message' => 'Berhasil menambah produk'];
    } catch (Exception $e) {
        $koneksi->rollback();
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;