<?php
session_start();
include '../../../config.php';

$sql = "SELECT * FROM pemasok";
$result = $koneksi->query($sql);

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
                        <h3 class="page-title">Tabel Pemasok</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="color: #6c7293;">ID Pemasok</th>
                                                    <th style="color: #6c7293;">Nama Pemasok</th>
                                                    <th style="color: #6c7293;">Alamat</th>
                                                    <th style="color: #6c7293;">Nomor Telepon</th>
                                                    <th style="color: #6c7293;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        ?>
                                                        <tr id="row_<?php echo $row['id_pemasok']; ?>">
                                                            <td style="color: #6c7293;"><?php echo $row['id_pemasok']; ?></td>
                                                            <td style="color: #6c7293;">
                                                            <span id="nama_<?php echo $row['id_pemasok']; ?>"><?php echo $row['nama_pemasok']; ?></span>
                                                            <input type="text" class="form-control edit-input" 
                                                                id="nama_input_<?php echo $row['id_pemasok']; ?>" 
                                                                value="<?php echo $row['nama_pemasok']; ?>" 
                                                                style="display: none; color: #ffffff;">
                                                        </td>
                                                        <td style="color: #6c7293;">
                                                            <span id="alamat_<?php echo $row['id_pemasok']; ?>"><?php echo $row['alamat']; ?></span>
                                                            <input type="text" class="form-control edit-input" 
                                                                id="alamat_input_<?php echo $row['id_pemasok']; ?>" 
                                                                value="<?php echo $row['alamat']; ?>" 
                                                                style="display: none; color: #ffffff;">
                                                        </td>
                                                        <td style="color: #6c7293;">
                                                            <span id="telepon_<?php echo $row['id_pemasok']; ?>"><?php echo $row['nomor_telepon']; ?></span>
                                                            <input type="text" class="form-control edit-input" 
                                                                id="telepon_input_<?php echo $row['id_pemasok']; ?>" 
                                                                value="<?php echo $row['nomor_telepon']; ?>" 
                                                                style="display: none; color: #ffffff;">
                                                        </td>
                                                            <td>
                                                                <button onclick="toggleEdit('<?php echo $row['id_pemasok']; ?>')" 
                                                                        class="btn btn-dark btn-sm edit-btn-<?php echo $row['id_pemasok']; ?>">Edit</button>
                                                                <button onclick="saveChanges('<?php echo $row['id_pemasok']; ?>')" 
                                                                        class="btn btn-success btn-sm save-btn-<?php echo $row['id_pemasok']; ?>" 
                                                                        style="display: none;">Simpan</button>
                                                                <button onclick="cancelEdit('<?php echo $row['id_pemasok']; ?>')" 
                                                                        class="btn btn-light btn-sm cancel-btn-<?php echo $row['id_pemasok']; ?>" 
                                                                        style="display: none;">Batal</button>
                                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
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

    <!-- JavaScript untuk menangani edit inline -->
    <script>
        function toggleEdit(id) {
            // Sembunyikan text dan tampilkan input
            document.getElementById('nama_' + id).style.display = 'none';
            document.getElementById('alamat_' + id).style.display = 'none';
            document.getElementById('telepon_' + id).style.display = 'none';
            
            document.getElementById('nama_input_' + id).style.display = 'block';
            document.getElementById('alamat_input_' + id).style.display = 'block';
            document.getElementById('telepon_input_' + id).style.display = 'block';
            
            // Sembunyikan tombol edit dan tampilkan tombol simpan & batal
            document.querySelector('.edit-btn-' + id).style.display = 'none';
            document.querySelector('.save-btn-' + id).style.display = 'inline-block';
            document.querySelector('.cancel-btn-' + id).style.display = 'inline-block';
        }

        function saveChanges(id) {
            const nama = document.getElementById('nama_input_' + id).value;
            const alamat = document.getElementById('alamat_input_' + id).value;
            const telepon = document.getElementById('telepon_input_' + id).value;

            // Kirim data ke server menggunakan AJAX
            fetch('../control/updatePemasok.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_pemasok=${id}&nama_pemasok=${nama}&alamat=${alamat}&nomor_telepon=${telepon}`
            })
            .then(response => response.text())
            .then(data => {
                // Update tampilan
                document.getElementById('nama_' + id).textContent = nama;
                document.getElementById('alamat_' + id).textContent = alamat;
                document.getElementById('telepon_' + id).textContent = telepon;
                
                // Kembalikan ke mode tampilan
                cancelEdit(id);
            })
            .catch(error => console.error('Error:', error));
        }

        function cancelEdit(id) {
            // Tampilkan text dan sembunyikan input
            document.getElementById('nama_' + id).style.display = 'block';
            document.getElementById('alamat_' + id).style.display = 'block';
            document.getElementById('telepon_' + id).style.display = 'block';
            
            document.getElementById('nama_input_' + id).style.display = 'none';
            document.getElementById('alamat_input_' + id).style.display = 'none';
            document.getElementById('telepon_input_' + id).style.display = 'none';
            
            // Tampilkan tombol edit dan sembunyikan tombol simpan & batal
            document.querySelector('.edit-btn-' + id).style.display = 'inline-block';
            document.querySelector('.save-btn-' + id).style.display = 'none';
            document.querySelector('.cancel-btn-' + id).style.display = 'none';
        }
    </script>
</body>
</html>