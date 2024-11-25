<?php
include '../../../config.php';

class KontrolStok {
    private $koneksi;

    public function __construct($koneksi) {
        $this->koneksi = $koneksi;
    }

    // Metode untuk menambah stok
    public function tambahStok($id_produk, $jumlah) {
        try {
            // Cek apakah produk sudah ada di tabel stok
            $stmt = $this->koneksi->prepare("SELECT * FROM stok WHERE id_produk = ?");
            $stmt->bind_param("i", $id_produk);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update stok yang sudah ada
                $stmt = $this->koneksi->prepare("UPDATE stok SET sisa = sisa + ? WHERE id_produk = ?");
                $stmt->bind_param("di", $jumlah, $id_produk);
            } else {
                // Tambah stok baru
                $stmt = $this->koneksi->prepare("INSERT INTO stok (id_produk, sisa) VALUES (?, ?)");
                $stmt->bind_param("id", $id_produk, $jumlah);
            }

            if (!$stmt->execute()) {
                throw new Exception("Gagal mengupdate stok: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Metode untuk mengurangi stok
    public function kurangiStok($id_produk, $jumlah) {
        try {
            $stmt = $this->koneksi->prepare("
                UPDATE stok 
                SET sisa = GREATEST(sisa - ?, 0) 
                WHERE id_produk = ?
            ");
            $stmt->bind_param("di", $jumlah, $id_produk);

            if (!$stmt->execute()) {
                throw new Exception("Gagal mengurangi stok: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Metode untuk mendapatkan stok saat ini
    public function cekStok($id_produk) {
        try {
            $stmt = $this->koneksi->prepare("SELECT sisa FROM stok WHERE id_produk = ?");
            $stmt->bind_param("i", $id_produk);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['sisa'];
            }

            return 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // Metode untuk transfer stok antar produk
    public function transferStok($id_produk_asal, $id_produk_tujuan, $jumlah) {
        try {
            $this->koneksi->begin_transaction();

            // Kurangi stok produk asal
            $this->kurangiStok($id_produk_asal, $jumlah);

            // Tambah stok produk tujuan
            $this->tambahStok($id_produk_tujuan, $jumlah);

            $this->koneksi->commit();
            return true;
        } catch (Exception $e) {
            $this->koneksi->rollback();
            error_log($e->getMessage());
            return false;
        }
    }
}

// Contoh penggunaan
if (isset($_POST['aksi'])) {
    $kontrolStok = new KontrolStok($koneksi);

    switch ($_POST['aksi']) {
        case 'tambah':
            $result = $kontrolStok->tambahStok($_POST['id_produk'], $_POST['jumlah']);
            break;
        case 'kurangi':
            $result = $kontrolStok->kurangiStok($_POST['id_produk'], $_POST['jumlah']);
            break;
        case 'transfer':
            $result = $kontrolStok->transferStok(
                $_POST['id_produk_asal'], 
                $_POST['id_produk_tujuan'], 
                $_POST['jumlah']
            );
            break;
    }

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Operasi berhasil' : 'Operasi gagal'
    ]);
}