<table class="table table-bordered table-hover" id="uom-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>SPPG</th>
            <th>Nama UOM</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($uom as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$row->sppg->nama ?? '-'}}</td>
                <td>{{$row->nama}}</td>
                <td>
                    <div class="btn-group">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-uom" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-uom">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-uom" data-id="{{$row->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#uom-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    $(".btn-edit-uom").click(function () {
        $('#formUom').trigger("reset");
        $(".modal-title").text('Edit UOM');
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const id = $(this).data('id');
        const url = "{!! url('uom') !!}/" + id + '/edit';
        $.get(url, function (data) {
            const row = data[0];
            $("#uom_id").val(row.id);
            $("#uom_sppg_id").val(row.sppg_id).trigger('change');
            $("#uom_nama").val(row.nama);
            $("#overlay-place").html('');
        });
    });

    $(document).on('click', '.btn-delete-uom', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data UOM ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('uom') !!}/" + id;
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: "{!! csrf_token() !!}" },
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Data berhasil dihapus.', timer: 1200, showConfirmButton: false });
                        refresh_table();
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data gagal dihapus.' });
                    }
                });
            }
        });
    });
</script>
