<table class="table table-bordered table-hover" id="vendor-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode</th>
            <th>Nama Vendor</th>
            <th>PIC</th>
            <th>Alamat</th>
            <th>Termin</th>
            <th>Metode Kirim</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendor as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$row->kode_vendor ?? '-'}}</td>
                <td>{{$row->nama}}</td>
                <td>
                    {{$row->pic_nama ?? '-'}}
                    @if(!empty($row->pic_jabatan))
                        <br><small class="text-muted">{{$row->pic_jabatan}}</small>
                    @endif
                    @if(!empty($row->pic_no_telp))
                        <br><small class="text-muted">{{$row->pic_no_telp}}</small>
                    @endif
                </td>
                <td>{{$row->email ?? '-'}}</td>
                <td>{{$row->termin_pembayaran ?? '-'}}</td>
                <td>{{$row->metode_pengiriman ?? '-'}}</td>
                <td>
                    @if($row->status === 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-vendor" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-vendor">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-vendor" data-id="{{$row->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#vendor-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    $(".btn-edit-vendor").click(function () {
        $('#formVendor').trigger("reset");
        $(".modal-title").text('Edit Vendor');
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const id = $(this).data('id');
        const url = "{!! url('vendor') !!}/" + id + '/edit';

        $.get(url, function (data) {
            const row = data[0];
            $("#vendor_id").val(row.id);
            $("#vendor_kode_vendor").val(row.kode_vendor);
            $("#vendor_nama").val(row.nama);
            $("#vendor_alamat").val(row.alamat);
            $("#vendor_no_telp").val(row.no_telp);
            $("#vendor_email").val(row.email);
            $("#vendor_pic_nama").val(row.pic_nama);
            $("#vendor_pic_jabatan").val(row.pic_jabatan);
            $("#vendor_pic_no_telp").val(row.pic_no_telp);
            $("#vendor_termin_pembayaran").val(row.termin_pembayaran);
            $("#vendor_metode_pengiriman").val(row.metode_pengiriman);
            $("#vendor_catatan").val(row.catatan);
            $("#vendor_status").val(row.status).trigger('change');
            $("#overlay-place").html('');
        });
    });

    $(document).on('click', '.btn-delete-vendor', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data vendor ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('vendor') !!}/" + id;
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: "{!! csrf_token() !!}" },
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Data berhasil dihapus.', timer: 1200, showConfirmButton: false });
                        refresh_table();
                    },
                    error: function (xhr) {
                        let msg = 'Data gagal dihapus.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                    }
                });
            }
        });
    });
</script>

