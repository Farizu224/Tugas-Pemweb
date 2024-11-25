<?php
header('Content-Type: application/json');
include '../../config.php';

$response = ['status' => 'error', 'message' => 'Gagal mendapatkan harga beli'];

if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Ambil harga beli dari database
    $sql = "SELECT harga_beli FROM pembelian WHERE id_produk = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('i', $id_produk);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $response = [
            'status' => 'success',
            'harga_beli' => $data['harga_beli']
        ];
    } else {
        $response['message'] = 'Harga beli tidak ditemukan';
    }

    $stmt->close();
}

echo json_encode($response);