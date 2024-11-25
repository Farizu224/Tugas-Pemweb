<?php
session_start();
include '../../../config.php';

// Query untuk mendapatkan data produk
$sql = "SELECT 
            p.id_produk, 
            p.nama_produk, 
            k.nama_kategori, 
            p.id_kategori, 
            p.harga_jual, 
            COALESCE(   
                (SELECT SUM(jumlah) FROM pembelian WHERE id_produk = p.id_produk) - 
                COALESCE((SELECT SUM(jumlah_jual) FROM penjualan WHERE id_produk = p.id_produk), 0),
                0
            ) as stock, 
            COALESCE(pb.harga_beli, 0) as harga_beli  -- Ambil harga_beli dari tabel pembelian
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.ID_Kategori
        LEFT JOIN pembelian pb ON p.id_produk = pb.id_produk";  // Menggabungkan dengan tabel pembelian
$result = $koneksi->query($sql);

// Ambil data kategori
// Ambil data kategori
$sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori";
$result_kategori = $koneksi->query($sql_kategori);

$kategori_options = [];
if ($result_kategori->num_rows > 0) {
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_options[] = $row;
    }
}

// Memasukkan header
include '../template/header.php';
?>
<body>
    <div class="container-scroller">
        <?php include '../template/sidebar.php'; ?>
        
        <div class="container-fluid page-body-wrapper">
            <?php include '../template/navbar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Tabel Produk</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Tabel Produk</h4>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahProduk">
                                        Tambah Produk
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Produk</th>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Harga Beli</th>
                                                    <th>Harga Jual</th>
                                                    <th>Stok</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Tampilkan produk dari tabel produk
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        ?>
                                                        <tr id='row_<?php echo $row['id_produk']; ?>'>
                                                            <td><?php echo $row['id_produk']; ?></td>
                                                            <td>
                                                                <span id='nama_<?php echo $row['id_produk']; ?>'><?php echo htmlspecialchars($row['nama_produk']); ?></span>
                                                                <select class='form-control edit-input' 
                                                                        id='nama_input_<?php echo $row['id_produk']; ?>' 
                                                                        style='display: none;'>
                                                                    <?php foreach ($produk_options as $produk): ?>
                                                                        <option value='<?php echo $produk['id_produk']; ?>' 
                                                                            <?php echo ($produk['id_produk'] == $row['id_produk']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <span id='kategori_<?php echo $row['id_produk']; ?>'>
                                                                    <?php echo htmlspecialchars($row['nama_kategori'] ? $row['nama_kategori'] : 'Tidak ada kategori'); ?>
                                                                </span>
                                                                <select class='form-control edit-input' id='kategori_input_<?php echo $row['id_produk']; ?>' style='display: none;'>
                                                                    <?php foreach ($kategori_options as $kategori): ?>
                                                                        <option value='<?php echo $kategori['id_kategori']; ?>' <?php echo ($kategori['id_kategori'] == $row['id_kategori']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <span id='harga_beli_<?php echo $row['id_produk']; ?>'><?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></span>
                                                                <input type='number' class='form-control edit-input' id='harga_beli_input_<?php echo $row['id_produk']; ?>' 
                                                                    value='<?php echo $row['harga_beli']; ?>' style='display: none;'>
                                                            </td>
                                                            <td>
                                                                <span id='harga_jual_<?php echo $row['id_produk']; ?>'><?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></span>
                                                                <input type='number' class='form-control edit-input' id='harga_jual_input_<?php echo $row['id_produk']; ?>' 
                                                                    value='<?php echo $row['harga_jual']; ?>' style='display: none;'>
                                                            </td>
                                                            <td>
                                                                <span id='stok_<?php echo $row['id_produk']; ?>'><?php echo $row['stock']; ?></span>
                                                                <input type='number' class='form-control edit-input' id='stok_input_<?php echo $row['id_produk']; ?>' 
                                                                    value='<?php echo $row['stock']; ?>' style='display: none;'>
                                                            </td>
                                                            <td>
                                                                <button onclick='toggleEdit(<?php echo $row['id_produk']; ?>)' class='btn btn-dark btn-sm edit-btn-<?php echo $row['id_produk']; ?>'>Edit</button>
                                                                <button onclick='saveChanges(<?php echo $row['id_produk']; ?>)' class='btn btn-success btn-sm save-btn-<?php echo $row['id_produk']; ?>' style='display:none;'>Simpan</button>
                                                                <button onclick='cancelEdit(<?php echo $row['id_produk']; ?>)' class='btn btn-light btn-sm cancel-btn-<?php echo $row['id_produk']; ?>' style='display:none;'>Batal</button>
                                                                <button onclick='hapusProduk(<?php echo $row['id_produk']; ?>)' class='btn btn-danger btn-sm'>Hapus</button>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7'>Tidak ada data produk</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../template/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Produk -->
    <div class="modal fade" id="modalTambahProduk" tabindex="-1" role="dialog" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahProdukLabel">Tambah Produk Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahProduk">
                        <div class="form-group">
                            <label for="id_pembelian">Pilih Pembelian</label>
                            <select class="form-control" id="id_pembelian" name="id_pembelian" required>
                                <option value="" disabled selected>Pilih Pembelian</option>
                                <?php 
                                $sql_pembelian = "SELECT 
                                    pb.id_pembelian, 
                                    pr.nama_produk, 
                                    pb.harga_beli,
                                    pb.id_produk
                                    FROM pembelian pb
                                    JOIN produk pr ON pb.id_produk = pr.id_produk
                                                                        WHERE pb.id_pembelian NOT IN (
                                        SELECT COALESCE(id_pembelian, 0) 
                                        FROM produk 
                                        WHERE id_pembelian IS NOT NULL
                                    )";
                                $result_pembelian = $koneksi->query($sql_pembelian);
                                
                                if ($result_pembelian->num_rows > 0) {
                                    while ($row = $result_pembelian->fetch_assoc()) {
                                        echo "<option value='{$row['id_pembelian']}' data-id-produk='{$row['id_produk']}' data-nama-produk='{$row['nama_produk']}' data-harga-beli='{$row['harga_beli']}'>";
                                        echo htmlspecialchars($row['nama_produk']) . " - Rp " . 
                                            number_format($row['harga_beli'], 0, ',', '.');
                                        echo "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>Tidak ada data pembelian tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select class="form-control" id="id_kategori" name="id_kategori" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                <?php 
                                foreach ($kategori_options as $kategori) {
                                    echo "<option value='{$kategori['id_kategori']}'>" . 
                                        htmlspecialchars($kategori['nama_kategori']) . 
                                        "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga_jual">Harga Jual</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddForm()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan form edit
        function toggleEdit(id) {
            $('#kategori_' + id).hide();
            $('#kategori_input_' + id).show();

            $('#harga_jual_' + id).hide();
            $('#harga_jual_input_' + id).show();

            $('#nama_' + id).show();
            $('#nama_input_' + id).hide();

            $('#harga_beli_' + id).show();
            $('#harga_beli_input_' + id).hide();

            $('#stok_' + id).show();
            $('#stok_input_' + id).hide();

            $('.edit-btn-' + id).hide();
            $('.save-btn-' + id).show();
            $('.cancel-btn-' + id).show();
            
            $('button.btn-danger[onclick="hapusProduk(' + id + ')"]').hide();
        }

        // Fungsi untuk membatalkan perubahan
        function cancelEdit(id) {
            $('#kategori_' + id).show();
            $('#kategori_input_' + id).hide();

            $('#harga_jual_' + id).show();
            $('#harga_jual_input_' + id).hide();

            $('#nama_' + id).show();
            $('#nama_input_' + id).hide();

            $('#harga_beli_' + id).show();
            $('#harga_beli_input_' + id).hide();

            $('#stok_' + id).show();
            $('#stok_input_' + id).hide();

            $('.edit-btn-' + id).show();
            $('.save-btn-' + id).hide();
            $('.cancel-btn-' + id).hide();

            $('button.btn-danger[onclick="hapusProduk(' + id + ')"]').show();
        }

        // Fungsi untuk menyimpan perubahan
        function saveChanges(id) {
            var id_kategori = $('#kategori_input_' + id).val();
            var harga_jual = $('#harga_jual_input_' + id).val();

            $.ajax({
                url: '../control/updateProduk.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    id_produk: id,
                    id_kategori: id_kategori,
                    harga_jual: harga_jual
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload(); // Refresh halaman untuk menampilkan data terbaru
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal mengupdate produk'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan saat menghubungi server'
                    });
                }
            });
        }

        // Fungsi untuk menghapus produk
        function hapusProduk(id) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                $.ajax({
                    url: '../control/hapusProduk.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { id_produk: id },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                location.reload(); // Refresh halaman untuk menampilkan data terbaru
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus produk'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            }
        }

        // Fungsi untuk mengirim form tambah produk
        function submitAddForm() {
            var formData = $('#formTambahProduk').serialize();

            $.ajax({
                url: '../control/tambahProduk.php',
                method: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload(); // Refresh halaman untuk menampilkan data terbaru
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal menambah produk'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan saat menghubungi server'
                    });
                }
            });
        }
    </script>
</body>
</html>