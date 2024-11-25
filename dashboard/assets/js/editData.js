$(document).ready(function() {
    // Ketika tombol Edit diklik
    $('.edit-btn').click(function() {
        var row = $(this).closest('tr');

        // Mengubah sel menjadi input
        row.find('.editable').each(function() {
            var cell = $(this);
            var currentValue = cell.text().trim(); // Menghapus spasi di awal dan akhir
            if (currentValue) {
                cell.html('<input type="text" class="form-control" value="' + currentValue + '"/>');
            } else {
                console.warn("Current value is empty for cell:", cell); // Peringatan jika nilai kosong
                cell.html('<input type="text" class="form-control" value=""/>'); // Atau bisa memberikan nilai default
            }
        });

        // Mengubah tombol Edit menjadi tombol Simpan
        $(this).text('Simpan').removeClass('btn-dark').addClass('btn-success').addClass('save-btn');
    });

    // Ketika tombol Simpan diklik
    $(document).on('click', '.save-btn', function(event) {
        var row = $(this).closest('tr');
        var data = {};

        // Mengambil nilai dari input
        row.find('.editable').each(function() {
            var field = $(this).data('field');
            var value = $(this).find('input').val().trim(); // Menghapus spasi di awal dan akhir
            console.log("Field:", field, "Value:", value); // Log field dan value
            data[field] = value;
        });

        // Pastikan untuk menambahkan id_pemasok ke data
        data.id_pemasok = row.data('id');

        // Validasi data sebelum mengirim
        if (!data.nama_pemasok || !data.nama_produk || !data.alamat || !data.nomor_telepon) {
            alert('Semua field harus diisi.');
            return; // Hentikan eksekusi jika ada field yang kosong
        }

        // Mengirim data ke server menggunakan AJAX
        $.ajax({
            url: '../control/updatePemasok.php',
            type: 'POST',
            data: data,
            success: function(response) {
                var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;

                if (jsonResponse.status === 'success') {
                    // Mengubah input kembali menjadi teks
                    row.find('.editable').each(function() {
                        var cell = $(this);
                        var newValue = data[cell.data('field')];
                        cell.html(newValue);
                    });

                    // Mengubah tombol Simpan kembali menjadi tombol Edit
                    $(this).text('Edit').removeClass('btn-success').addClass('btn-dark').removeClass('save-btn');
                } else {
                    alert('Error: ' + jsonResponse.message);
                }
            }.bind(this),
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Terjadi kesalahan saat menyimpan data. Lihat konsol untuk detail.');
            }
        });
    });
    
});