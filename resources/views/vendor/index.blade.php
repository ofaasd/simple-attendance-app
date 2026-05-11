<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{$title}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{$title}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-md-12">
                        <div class="card-header">
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-vendor">+ Add Vendor</a>
                            <a href="javascript:void(0)" class="btn btn-success ml-2" data-toggle="modal" data-target="#modal-import-vendor">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-vendor" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formVendor">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Vendor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="vendor_id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_kode_vendor">Kode Vendor</label>
                                    <input type="text" name="kode_vendor" class="form-control" id="vendor_kode_vendor" placeholder="Contoh: VND-001" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_nama">Nama Vendor</label>
                                    <input type="text" name="nama" class="form-control" id="vendor_nama" placeholder="Masukkan nama vendor" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_no_telp">No. Telp</label>
                                    <input type="text" name="no_telp" class="form-control" id="vendor_no_telp" placeholder="Masukkan nomor telepon">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="vendor_email">Email</label>
                                    <input type="email" name="email" class="form-control" id="vendor_email" placeholder="Masukkan email vendor (opsional)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_status">Status</label>
                                    <select name="status" id="vendor_status" class="form-control">
                                        <option value="aktif">Aktif</option>
                                        <option value="tidak aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="vendor_alamat">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="vendor_alamat" rows="3" placeholder="Masukkan alamat vendor"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_pic_nama">PIC Nama</label>
                                    <input type="text" name="pic_nama" class="form-control" id="vendor_pic_nama" placeholder="Nama PIC">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_pic_jabatan">PIC Jabatan</label>
                                    <input type="text" name="pic_jabatan" class="form-control" id="vendor_pic_jabatan" placeholder="Jabatan PIC">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_pic_no_telp">PIC No. Telp</label>
                                    <input type="text" name="pic_no_telp" class="form-control" id="vendor_pic_no_telp" placeholder="Nomor PIC">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_termin_pembayaran">Termin Pembayaran</label>
                                    <input type="text" name="termin_pembayaran" class="form-control" id="vendor_termin_pembayaran" placeholder="Contoh: 30 hari / COD">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_metode_pengiriman">Metode Pengiriman</label>
                                    <input type="text" name="metode_pengiriman" class="form-control" id="vendor_metode_pengiriman" placeholder="Contoh: Diantar vendor">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="vendor_catatan">Catatan</label>
                                    <textarea name="catatan" class="form-control" id="vendor_catatan" rows="2" placeholder="Catatan tambahan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="modal-import-vendor" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formImportVendor" enctype="multipart/form-data">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-import-vendor-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Import Vendor dari Excel</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group mb-0">
                            <label for="import_vendor_file">File Excel</label>
                            <input type="file" name="file" id="import_vendor_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Format kolom (urutan):</strong><br>
                                Kolom 1: Kode Vendor <span class="text-danger">*</span> &nbsp;|
                                Kolom 2: Nama <span class="text-danger">*</span> &nbsp;|
                                Kolom 3: Alamat &nbsp;|
                                Kolom 4: No. Telp &nbsp;|
                                Kolom 5: Email &nbsp;|
                                Kolom 6: PIC Nama &nbsp;|
                                Kolom 7: PIC Jabatan &nbsp;|
                                Kolom 8: PIC No. Telp &nbsp;|
                                Kolom 9: Termin Pembayaran &nbsp;|
                                Kolom 10: Metode Pengiriman &nbsp;|
                                Kolom 11: Catatan &nbsp;|
                                Kolom 12: Status (aktif / tidak aktif)
                                <br><span class="text-danger">*</span> Wajib diisi. Vendor dengan kode yang sudah ada akan di-skip.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function refresh_table() {
        $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const urlTable = "{!! url('vendor/get_table') !!}";
        $.get(urlTable, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        refresh_table();

        $(".btn-create").click(function () {
            $('#formVendor').trigger("reset");
            $("#vendor_id").val('');
            $(".modal-title").text('Add New Vendor');
            $("#vendor_status").val('aktif');
        });

        $('#modal-import-vendor').on('shown.bs.modal', function () {
            $('#formImportVendor').trigger('reset');
        });

        $('#formImportVendor').submit(function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = "{!! route('vendor.import') !!}";

            $('#overlay-import-vendor-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    let msg = response.message || 'Import selesai.';
                    if (response.failed_rows && response.failed_rows.length) {
                        const preview = response.failed_rows
                            .map(r => 'Baris ' + r.row + ': ' + r.reason)
                            .join('\n');
                        msg += '\n\nDetail gagal (maks 20):\n' + preview;
                    }
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: msg });
                    $('#overlay-import-vendor-place').html('');
                    $('#modal-import-vendor').modal('hide');
                    refresh_table();
                },
                error: function (xhr) {
                    $('#overlay-import-vendor-place').html('');
                    let msg = 'Terjadi kesalahan saat import.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });

        $("#formVendor").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('vendor') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data vendor berhasil disimpan.' });
                    $("#modal-vendor").modal("hide");
                    $("#overlay-place").html('');
                    refresh_table();
                },
                error: function (xhr) {
                    $("#overlay-place").html('');
                    let msg = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });
    });
</script>

