<?php
include '../../../config.php';

// Query untuk mendapatkan data penjualan dengan join ke produk
$sql = "SELECT pj.id_penjualan, pr.nama_produk, pr.harga_jual AS harga_jual, pj.jumlah_jual, pj.id_produk
        FROM penjualan pj
        JOIN produk pr ON pj.id_produk = pr.id_produk
        ORDER BY pj.id_penjualan ASC";
$result = $koneksi->query($sql);

// Query untuk produk
$sql_produk = "SELECT id_produk, nama_produk, harga_jual FROM produk"; // Menambahkan harga_jual untuk digunakan di modal
$result_produk = $koneksi->query($sql_produk);

$produk_options = [];
if ($result_produk->num_rows > 0) {
    while ($row = $result_produk->fetch_assoc()) {
        $produk_options[] = $row;
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
                        <h3 class="page-title">Tabel Penjualan</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Tabel Penjualan</h4>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahPenjualan">
                                        Tambah Penjualan
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Penjualan</th>
                                                    <th>Produk</th>
                                                    <th>Harga Jual</th>
                                                    <th>Jumlah Jual</th>
                                                    <th>Total</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $total = $row['harga_jual'] * $row['jumlah_jual'];
                                                        ?>
                                                        <tr id='row_<?php echo $row['id_penjualan']; ?>'>
                                                            <td><?php echo $row['id_penjualan']; ?></td>
                                                            <td>
                                                                <span id='produk_<?php echo $row['id_penjualan']; ?>'><?php echo htmlspecialchars($row['nama_produk']); ?></span>
                                                                <input type='text' class='form-control edit-input' id='produk_input_<?php echo $row['id_penjualan']; ?>' 
                                                                    value='<?php echo htmlspecialchars($row['nama_produk']); ?>' style='display: none;'>
                                                            </td>
                                                            <td>
                                                                <span id='harga_jual_<?php echo $row['id_penjualan']; ?>'>Rp <?php echo number_format($row['harga_jual'], 2, ',', '.'); ?></span>
                                                                <input type='text' class='form-control edit-input' id='harga_jual_input_<?php echo $row['id_penjualan']; ?>' 
                                                                    value='Rp <?php echo number_format($row['harga_jual'], 2, ',', '.'); ?>' style='display: none;' readonly>
                                                            </td>
                                                            <td>
                                                                <span id='jumlah_jual_<?php echo $row['id_penjualan']; ?>'><?php echo $row['jumlah_jual']; ?></span>
                                                                <input type='number' class='form-control edit-input' id='jumlah_jual_input_<?php echo $row['id_penjualan']; ?>' 
                                                                    value='<?php echo $row['jumlah_jual']; ?>' style='display: none;'>
                                                            </td>
                                                            <td>
                                                                <span id='total_<?php echo $row['id_penjualan']; ?>'>Rp <?php echo number_format($total, 2, ',', '.'); ?></span>
                                                            </td>
                                                            <td>
                                                                <button onclick='toggleEdit(<?php echo $row['id_penjualan']; ?>)' class='btn btn-dark btn-sm edit-btn-<?php echo $row['id_penjualan']; ?>'>Edit</button>
                                                                <button onclick='saveChanges(<?php echo $row['id_penjualan']; ?>)' class='btn btn-success btn-sm save-btn-<?php echo $row['id_penjualan']; ?>' style='display: none;'>Simpan</button>
                                                                <button onclick='cancelEdit(<?php echo $row['id_penjualan']; ?>)' class='btn btn-light btn-sm cancel-btn-<?php echo $row['id_penjualan']; ?>' style='display: none;'>Batal</button>
                                                                <a href='hapusPenjualan.php?id=<?php echo $row['id_penjualan'];                                                                 ?>' class='btn btn-danger btn-sm'>Hapus</a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='6'>Tidak ada data</td></tr>";
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

    <!-- Modal Tambah Penjualan -->
    <div class="modal fade" id="modalTambahPenjualan" tabindex="-1" role="dialog" aria-labelledby="modalTambahPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPenjualanLabel">Tambah Penjualan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahPenjualan">
                        <div class="form-group">
                            <label for="id_produk">Nama Produk</label>
                            <select class="form-control select2" id="id_produk" name="id_produk" required onchange="updateHargaJual()">
                                <option value="" disabled selected>Pilih Produk</option>
                                <?php
                                foreach ($produk_options as $produk) {
                                    echo "<option value='" . htmlspecialchars($produk['id_produk']) . "' data-harga='" . htmlspecialchars($produk['harga_jual']) . "'>" 
                                        . htmlspecialchars($produk['nama_produk']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga_jual">Harga Jual</label>
                            <input type="text" class="form-control" id="harga_jual" name="harga_jual" readonly>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_jual">Jumlah Jual</label>
                            <input type="number" class="form-control" id="jumlah_jual" name="jumlah_jual" required min="1">
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
        function updateHargaJual() {
            const select = document.getElementById('id_produk');
            const selectedOption = select.options[select.selectedIndex];
            const hargaJual = selectedOption.getAttribute('data-harga');
            
            // Update harga_jual input
            document.getElementById('harga_jual').value = 'Rp ' + parseFloat(hargaJual).toLocaleString('id-ID', { minimumFractionDigits: 2 });
        }

        function toggleEdit(id) {
            // Sembunyikan elemen jumlah jual dan tampilkan input
            const jumlahJualElem = document.getElementById('jumlah_jual_' + id);
            
            if (jumlahJualElem) {
                // Sembunyikan elemen jumlah jual
                jumlahJualElem.style.display = 'none';

                // Tampilkan input jumlah jual
                document.getElementById('jumlah_jual_input_' + id).style.display = 'block';

                // Sembunyikan tombol hapus
                const hapusBtns = document.querySelectorAll('.btn-danger');
                hapusBtns.forEach(btn => btn.style.display = 'none');

                // Sembunyikan tombol edit dan tampilkan tombol simpan & batal
                document.querySelector('.edit-btn-' + id).style.display = 'none';
                document.querySelector('.save-btn-' + id).style.display = 'inline-block';
                document.querySelector('.cancel-btn-' + id).style.display = 'inline-block';
            }
        }

        function cancelEdit(id) {
            // Tampilkan kembali elemen jumlah jual
            document.getElementById('jumlah_jual_' + id).style.display = 'block';

            // Sembunyikan input jumlah jual
            document.getElementById('jumlah_jual_input_' + id).style.display = 'none';

            // Tampilkan kembali tombol hapus
            const hapusBtns = document.querySelectorAll('.btn-danger');
            hapusBtns.forEach(btn => btn.style.display = 'inline-block');

            // Kembalikan tampilan tombol
            document.querySelector('.edit-btn-' + id).style.display = 'inline-block';
            document.querySelector('.save-btn-' + id).style.display = 'none';
            document.querySelector('.cancel-btn-' + id).style.display = 'none';
        }

        function saveChanges(id) {
            const formData = new FormData();
            formData.append('id_penjualan', id);
            formData.append('jumlah_jual', document.getElementById('jumlah_jual_input_' + id).value);

            fetch('../control/updatePenjualan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Perbaiki di sini
            .then(data => {
                if (data && data.status === 'success') { // Cek apakah data terdefinisi
                    alert(data.message);
                    location.reload(); // Memuat ulang halaman
                } else {
                    alert(data.message || 'Terjadi kesalahan saat memperbarui penjualan'); // Tambahkan pesan default
                }
            })
            .catch(error => {
                console.error('Terjadi masalah saat menyimpan perubahan:', error);
                alert('Terjadi kesalahan saat menyimpan perubahan. Silakan coba lagi.'); // Tambahkan alert untuk kesalahan
            });
        }

        function submitAddForm() {
            const formData = new FormData(document.getElementById('formTambahPenjualan'));

            fetch('../control/tambahPenjualan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    // Lakukan tindakan lain setelah sukses
                    location.reload(); // Memuat ulang halaman
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Terjadi masalah saat menambahkan penjualan:', error);
            });
        }
    </script>
</body>
</html>
