<?php
include '../../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['id_produk'];
    $jumlah_jual = $_POST['jumlah_jual'];
    $tanggal_penjualan = date('Y-m-d'); // Atau ambil dari input jika ada

    // Validasi input
    if (empty($id_produk) || empty($jumlah_jual)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Siapkan dan eksekusi query untuk menambah penjualan
    $stmt = $koneksi->prepare("INSERT INTO penjualan (id_produk, tanggal_penjualan, jumlah_jual, total) VALUES (?, ?, ?, ?)");
    $total = 0; // Hitung total jika perlu
    $stmt->bind_param("isid", $id_produk, $tanggal_penjualan, $jumlah_jual, $total);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Penjualan berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan penjualan: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid']);
}
$koneksi->close();
?>