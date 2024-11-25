<?php
header('Content-Type: application/json');
include '../../../config.php';

$response = ['status' => 'error', 'message' => 'Gagal menambah pembelian'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST
    $id_pemasok = $_POST['id_pemasok'] ?? null;
    $nama_produk = $_POST['nama_produk'] ?? null; // Ambil nama produk
    $harga_beli = $_POST['harga_beli'] ?? null;
    $jumlah = $_POST['jumlah'] ?? null;

    // Validasi input
    if (empty($id_pemasok) || empty($nama_produk) || empty($harga_beli) || empty($jumlah)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Cek apakah produk sudah ada
        $stmt_check = $koneksi->prepare("SELECT id_produk FROM produk WHERE nama_produk = ?");
        $stmt_check->bind_param("s", $nama_produk);
        $stmt_check->execute();
        $stmt_check->bind_result($id_produk);
        $stmt_check->fetch();
        $stmt_check->close();

        // Jika produk tidak ada, tambahkan produk baru
        if (is_null($id_produk)) {
            $stmt_insert_produk = $koneksi->prepare("INSERT INTO produk (nama_produk, harga_jual, stok) VALUES (?, ?, ?)");
            $stok = 0; // Stok awal untuk produk baru
            $stmt_insert_produk->bind_param("sdi", $nama_produk, $harga_beli, $stok);
            $stmt_insert_produk->execute();
            $id_produk = $stmt_insert_produk->insert_id; // Ambil ID produk yang baru ditambahkan
            $stmt_insert_produk->close();
        }

        // Tambah data ke tabel pembelian
        $stmt = $koneksi->prepare("INSERT INTO pembelian (id_pemasok, id_produk, harga_beli, jumlah) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidi", $id_pemasok, $id_produk, $harga_beli, $jumlah);
        $stmt->execute();
        $id_pembelian = $stmt->insert_id; // Ambil ID pembelian yang baru ditambahkan
        error_log("Insert Query: " . $stmt->error);

        // Hitung subtotal
        $subtotal = $harga_beli * $jumlah;

        // Tambah data ke tabel detail_pembelian
        $stmt_detail = $koneksi->prepare("INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_detail->bind_param("iiidd", $id_pembelian, $id_produk, $jumlah, $harga_beli, $subtotal);
        $stmt_detail->execute();

        // Update stok produk
        $stmt_update_stok = $koneksi->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
        $stmt_update_stok->bind_param("ii", $jumlah, $id_produk);
        
        if ($stmt_update_stok->execute()) {
            // Commit transaksi
            $koneksi->commit();
            echo json_encode(['status' => 'success', 'message' => 'Pembelian berhasil ditambahkan dan stok diperbarui']);
        } else {
            // Jika gagal memperbarui stok, rollback transaksi
            $koneksi->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui stok: ' . $stmt_update_stok->error]);
        }

        // Tutup statement
        $stmt->close();
        $stmt_detail->close();
        $stmt_update_stok->close();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $koneksi->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan pembelian: ' . $e->getMessage()]);
    }

    // Tutup koneksi
    $koneksi->close();
}