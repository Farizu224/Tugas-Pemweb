<?php
include '../../../config.php';

include 'stok.php'; // Pastikan untuk menginclude file stok.php

// Setelah melakukan pembelian
sinkronStok($koneksi);

// Query untuk mendapatkan data pembelian dengan join ke produk dan pemasok
$sql = "SELECT pb.id_pembelian, p.nama_pemasok, pr.nama_produk, pb.harga_beli, pb.jumlah,
        pb.id_pemasok, pb.id_produk, pr.id_produk
        FROM pembelian pb
        JOIN pemasok p ON pb.id_pemasok = p.id_pemasok
        JOIN produk pr ON pb.id_produk = pr.id_produk
        ORDER BY pb.id_pembelian ASC";

error_log("Query: " . $sql);
$result = $koneksi->query($sql);

// Query untuk pemasok dan produk
$sql_pemasok = "SELECT id_pemasok, nama_pemasok FROM pemasok";
$result_pemasok = $koneksi->query($sql_pemasok);

$sql_produk = "SELECT id_produk, nama_produk FROM produk";
$result_produk = $koneksi->query($sql_produk);

$pemasok_options = [];
if ($result_pemasok->num_rows > 0) {
    while ($row = $result_pemasok->fetch_assoc()) {
        $pemasok_options[] = $row;
    }
}

$produk_options = [];
if ($result_produk->num_rows > 0) {
    while ($row = $result_produk->fetch_assoc()) {
        $produk_options[] = $row;
    }
}

$sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori";
$result_kategori = $koneksi->query($sql_kategori);

$kategori_options = [];
if ($result_kategori->num_rows > 0) {
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_options[] = $row;
    }
}

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
                        <h3 class="page-title">Tabel Pembelian</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Tabel Pembelian</h4>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahPembelian">
                                    Tambah Pembelian
                                </button>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Pembelian</th>
                                                <th>Pemasok</th>
                                                <th>Produk</th>
                                                <th>Harga Beli</th>
                                                <th>Jumlah</th>
                                                <th>Total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $total = $row['harga_beli'] * $row['jumlah'];
                                                    ?>
                                                    <tr id='row_<?php echo $row['id_pembelian']; ?>'>
                                                        <td><?php echo $row['id_pembelian']; ?></td>
                                                        <td>
                                                            <span id='pemasok_<?php echo $row['id_pembelian']; ?>'><?php echo htmlspecialchars($row['nama_pemasok']); ?></span>
                                                            <select class="form-control select2" 
                                                                    id="pemasok_select_<?php echo $row['id_pembelian']; ?>" 
                                                                    name="id_pemasok" 
                                                                    required 
                                                                    style="display: none;">
                                                                <option value="" disabled selected>Pilih Pemasok</option>
                                                                <?php
                                                                if ($result_pemasok->num_rows > 0) {
                                                                    $result_pemasok->data_seek(0);
                                                                    while ($row_pemasok = $result_pemasok->fetch_assoc()) {
                                                                        $selected = ($row_pemasok['id_pemasok'] == $row['id_pemasok']) ? 'selected' : '';
                                                                        echo "<option value='" . htmlspecialchars($row_pemasok['id_pemasok']) . "' $selected>" 
                                                                            . htmlspecialchars($row_pemasok['nama_pemasok']) . "</option>";
                                                                    }
                                                                } else {
                                                                    echo "<option value=''>Tidak ada pemasok tersedia</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <span id='produk_<?php echo $row['id_pembelian']; ?>'><?php echo htmlspecialchars($row['nama_produk']); ?></span>
                                                            <input type='text' class='form-control edit-input' id='produk_input_<?php echo $row['id_pembelian']; ?>' 
                                                                value='<?php echo htmlspecialchars($row['nama_produk']); ?>' style='display: none;'>
                                                            <input type='hidden' id='id_produk_<?php echo $row['id_pembelian']; ?>' value='<?php echo $row['id_produk']; ?>'>
                                                        </td>
                                                        <td>
                                                            <span id='harga_beli_<?php echo $row['id_pembelian']; ?>'>Rp <?php echo number_format($row['harga_beli'], 2, ',', '.'); ?></span>
                                                            <input type='number' class='form-control edit-input' id='harga_beli_input_<?php echo $row['id_pembelian']; ?>' 
                                                                value='<?php echo $row['harga_beli']; ?>' style='display: none;'>
                                                        </td>
                                                        <td>
                                                            <span id='jumlah_<?php echo $row['id_pembelian']; ?>'><?php echo $row['jumlah']; ?></span>
                                                            <input type='number' class='form-control edit-input' id='jumlah_input_<?php echo $row['id_pembelian']; ?>' 
                                                                value='<?php echo $row['jumlah']; ?>' style='display: none;'>
                                                        </td>
                                                        <td>
                                                            <span id='total_<?php echo $row['id_pembelian']; ?>'>Rp <?php echo number_format($total, 2, ',', '.'); ?></span>
                                                        </td>
                                                        <td>
                                                            <button onclick='toggleEdit(<?php echo $row['id_pembelian']; ?>)' class='btn btn-dark btn-sm edit-btn-<?php echo $row['id_pembelian']; ?>'>Edit</button>
                                                            <button onclick='saveChanges(<?php echo $row['id_pembelian']; ?>)' class='btn btn-success btn-sm save-btn-<?php echo $row['id_pembelian']; ?>' style='display: none;'>Simpan</button>
                                                            <button onclick='cancelEdit(<?php echo $row['id_pembelian']; ?>)' class='btn btn-light btn-sm cancel-btn-<?php echo $row['id_pembelian']; ?>' style='display: none;'>Batal</button>
                                                            <a href="javascript:void(0);" onclick="hapusPembelian(<?php echo $row['id_pembelian']; ?>)" class="btn btn-danger btn-sm">Hapus</a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
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
    <!-- Modal Tambah Pembelian -->
    <div class="modal fade" id="modalTambahPembelian" tabindex="-1" role="dialog" aria-labelledby="modalTambahPembelianLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPembelianLabel">Tambah Pembelian Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahPembelian">
                        <div class="form-group">
                            <label for="pemasok">Pemasok</label>
                            <select class="form-control select2" id="pemasok" name="id_pemasok" required>
                                <option value="" disabled selected>Pilih Pemasok</option>
                                <?php
                                if ($result_pemasok->num_rows > 0) {
                                    // Reset pointer result set jika sudah pernah di-loop
                                    $result_pemasok->data_seek(0);
                                    
                                    while ($row_pemasok = $result_pemasok->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row_pemasok['id_pemasok']) . "'>" 
                                            . htmlspecialchars($row_pemasok['nama_pemasok']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Tidak ada pemasok tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_produk">Nama Produk</label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                        </div>
                        <div class="form-group">
                            <label for="harga_beli">Harga Beli</label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1">
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

    <!-- JavaScript untuk menangani edit inline -->
    <script>
        function toggleEdit(id) {
            // Sembunyikan text dan tampilkan input
            document.getElementById('pemasok_' + id).style.display = 'none';
            document.getElementById('produk_' + id).style.display = 'none';
            document.getElementById('harga_beli_' + id).style.display = 'none';
            document.getElementById('jumlah_' + id).style.display = 'none';

            // Tampilkan select pemasok
            document.getElementById('pemasok_select_' + id).style.display = 'block';
            
            document.getElementById('produk_input_' + id).style.display = 'block';
            document.getElementById('harga_beli_input_' + id).style.display = 'block';
            document.getElementById('jumlah_input_' + id).style.display = 'block';

            // Sembunyikan tombol edit dan tampilkan tombol simpan & batal
            document.querySelector('.edit-btn-' + id).style.display = 'none';
            document.querySelector('.save-btn-' + id).style.display = 'inline-block';
            document.querySelector('.cancel-btn-' + id).style.display = 'inline-block';
            
            const hapusTombol = document.querySelector('a.btn-danger[href*="javascript:void(0);' + id + '"]');
            if (hapusTombol) {
                hapusTombol.style.display = 'none';
            }
        }

        function saveChanges(id) {
            const nama_produk = document.getElementById('produk_input_' + id).value;
            const harga_beli = document.getElementById('harga_beli_input_' + id).value;
            const jumlah = document.getElementById('jumlah_input_' + id).value;
            const id_produk = document.getElementById('id_produk_' + id).value;
            
            // Ambil nilai id_pemasok dari select
            const id_pemasok = document.getElementById('pemasok_select_' + id).value;

            const formData = new FormData();
            formData.append('id_pembelian', id);
            formData.append('id_produk', id_produk);
            formData.append('id_pemasok', id_pemasok);
            formData.append('harga_beli', harga_beli);
            formData.append('jumlah', jumlah);

            fetch('../control/updatePembelian.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response dari server:', data);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Data berhasil diperbarui!'
                    }).then(() => {
                        // Refresh halaman untuk menampilkan data terbaru
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal memperbarui data'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan saat menghubungi server.'
                });
            });
        }

        function cancelEdit(id) {
            // Tampilkan kembali elemen teks
            document.getElementById('pemasok_' + id).style.display = 'block';
            document.getElementById('produk_' + id).style.display = 'block';
            document.getElementById('harga_beli_' + id).style.display = 'block';
            document.getElementById('jumlah_' + id).style.display = 'block';

            // Sembunyikan input
            document.getElementById('pemasok_select_' + id).style.display = 'none';
            document.getElementById('produk_input_' + id).style.display = 'none';
            document.getElementById('harga_beli_input_' + id).style.display = 'none';
            document.getElementById('jumlah_input_' + id).style.display = 'none';

            // Kembalikan tampilan tombol
            document.querySelector('.edit-btn-' + id).style.display = 'inline-block';
            document.querySelector('.save-btn-' + id).style.display = 'none';
            document.querySelector('.cancel-btn-' + id).style.display = 'none';

            const hapusTombol = document.querySelector('a.btn-danger[href*="javascript:void(0);' + id + '"]');
            if (hapusTombol) {
                hapusTombol.style.display = 'inline-block';
            }
        }

        // Fungsi opsional untuk update tabel produk secara dinamis
        function updateProdukTable(produkData) {
            // Cari baris yang sesuai dengan id_produk di tabel produk
            const existingRow = document.querySelector(`#row_produk_${produkData.id_produk}`);
            
            if (existingRow) {
                // Update kolom-kolom yang diperlukan
                const namaCell = existingRow.querySelector('.nama-produk');
                if (namaCell) {
                    namaCell.textContent = produkData.nama_produk;
                }
                
                // Tambahkan update untuk kolom lain sesuai kebutuhan
                // Misalnya: harga, stok, dll
            }
        }

        // Fungsi updateUIElements yang sudah ada sebelumnya
        function updateUIElements(id, nama_produk, harga_beli, jumlah, nama_pemasok) {
            // Format harga dengan rupiah
            function formatRupiah(angka) {
                let number_string = angka.toString();
                let split = number_string.split('.');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return 'Rp ' + rupiah;
            }

            // Hitung total
            let total = parseFloat(harga_beli) * parseInt(jumlah);

            // Update elemen-elemen teks
            document.getElementById('produk_' + id).textContent = nama_produk;
            document.getElementById('harga_beli_' + id).textContent = formatRupiah(harga_beli);
            document.getElementById('jumlah_' + id).textContent = jumlah;
            document.getElementById('total_' + id).textContent = formatRupiah(total);
            
            // Update nama pemasok
            if (nama_pemasok) {
                document.getElementById('pemasok_' + id).textContent = nama_pemasok;
            }

            // Reset input values
            document.getElementById('produk_input_' + id).value = nama_produk;
            document.getElementById('harga_beli_input_' + id).value = harga_beli;
            document.getElementById('jumlah_input_' + id).value = jumlah;
        }

        function showAddForm() {
            document.getElementById('addForm').style.display = 'block';
        }

        function hideAddForm() {
            document.getElementById('addForm').style.display = 'none';
        }

        function submitAddForm() {
            const formData = new FormData(document.getElementById('formTambahPembelian'));

            $.ajax({
                url: '../control/tambahPembelian.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Pembelian berhasil ditambahkan!'
                        }).then(() => {
                            location.reload(); // Refresh halaman untuk menampilkan data terbaru
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal menambah pembelian'
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

        
        function hapusPembelian(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Pembelian akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id_pembelian', id);

                    fetch('../control/hapusPembelian.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Hapus baris dari tabel
                            const row = document.getElementById('row_' + id);
                            if (row) {
                                row.remove();
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Pembelian berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal menghapus pembelian'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan saat menghapus data'
                        });
                    });
                }
            });
        }
        
        function updatePembelian(formData) {
            $.ajax({
                url: '../control/updatePembelian.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Cek apakah perlu update stok
                        if (response.data.id_produk !== response.data.old_produk_id || 
                            response.data.jumlah !== response.data.old_jumlah) {
                            
                            // Panggil fungsi update stok
                            updateStok({
                                id_pembelian: response.data.id_pembelian,
                                old_produk_id: response.data.old_produk_id,
                                new_produk_id: response.data.id_produk,
                                old_jumlah: response.data.old_jumlah,
                                new_jumlah: response.data.jumlah
                            });
                        }

                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        });

                        // Refresh tabel atau lakukan aksi lanjutan
                        $('#tablePembelian').DataTable().ajax.reload();
                        $('#modalEditPembelian').modal('hide');
                    } else {
                        // Tampilkan pesan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Tangani error ajax
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        }

        function updateStok(data) {
            $.ajax({
                url: '../control/updateStok.php',
                method: 'POST',
                data: {
                    id_pembelian: data.id_pembelian,
                    old_produk_id: data.old_produk_id,
                    new_produk_id: data.new_produk_id,
                    old_jumlah: data.old_jumlah,
                    new_jumlah: data.new_jumlah
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('Stok berhasil diupdate');
                        // Optional: Tambahkan aksi tambahan jika diperlukan
                    } else {
                        console.error('Gagal update stok:', response.message);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Stok gagal diupdate: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error update stok:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat update stok: ' + error
                    });
                }
            });
        }

        // Contoh penggunaan pada form submit
        $('#formEditPembelian').on('submit', function(e) {
            e.preventDefault();
            
            // Siapkan FormData
            var formData = new FormData(this);
            
            // Panggil fungsi update
            updatePembelian(formData);
        });
    </script>
</body>
</html>