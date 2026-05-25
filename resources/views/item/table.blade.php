<table class="table table-bordered table-hover" id="item-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>SPPG</th>
            <th>Nama Item</th>
            <th>Kategori</th>
            <th>UOM</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($item as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$row->sppg->nama ?? '-'}}</td>
                <td>{{$row->nama}}</td>
                <td>{{$row->kategori->nama ?? '-'}}</td>
                <td>{{$row->uom->nama ?? '-'}}</td>
                <td>
                    <div class="btn-group">
                        @if(!auth()->user()->hasRole('admin yayasan'))
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-item">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-item" data-id="{{$row->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#item-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    $(".btn-edit-item").click(function () {
        $('#formItem').trigger("reset");
        $(".modal-title").text('Edit Item');
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const id = $(this).data('id');
        const url = "{!! url('item') !!}/" + id + '/edit';
        $.get(url, function (data) {
            const row = data.item;
            $("#item_id").val(row.id);
            $("#item_sppg_id").val(row.sppg_id).trigger('change');
            filterItemMasterBySppg();
            $("#item_nama").val(row.nama);
            $("#item_kategori_id").val(row.kategori_id).trigger('change');
            $("#item_uom_id").val(row.uom_id).trigger('change');
            $("#overlay-place").html('');
        });
    });

    $(document).on('click', '.btn-delete-item', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data item ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('item') !!}/" + id;
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

