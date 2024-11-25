<?php
session_start();
include '../../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pemasok = $_POST['id_pemasok'];
    $nama_pemasok = $_POST['nama_pemasok'];
    $alamat = $_POST['alamat'];
    $nomor_telepon = $_POST['nomor_telepon'];
    
    // Query untuk mengupdate data
    $sql = "UPDATE pemasok SET 
            nama_pemasok = '$nama_pemasok',
            alamat = '$alamat',
            nomor_telepon = '$nomor_telepon'
            WHERE id_pemasok = '$id_pemasok'";
    
    if ($koneksi->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid request";
}
?>