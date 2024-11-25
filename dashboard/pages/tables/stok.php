<?php
session_start();
include '../../../config.php';

function sinkronStok($koneksi) {
    // Hapus duplikasi stok
    $koneksi->query("
        DELETE s1 FROM stok s1
        INNER JOIN (
            SELECT id_produk, MIN(id_stok) as min_id 
            FROM stok 
            GROUP BY id_produk
        ) s2 ON s1.id_produk = s2.id_produk
        WHERE s1.id_stok NOT IN (s2.min_id);
    ");

    // Pastikan setiap produk memiliki satu entri stok
    $koneksi->query("
        INSERT INTO stok (id_produk, sisa)
        SELECT p.id_produk, 0
        FROM produk p
        LEFT JOIN stok s ON p.id_produk = s.id_produk
        WHERE s.id_produk IS NULL;
    ");
}
?>