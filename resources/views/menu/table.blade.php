<table class="table table-bordered table-hover" id="menu-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>SPPG</th>
            <th>Nama Menu</th>
            <th>Jumlah Item</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($menu as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{optional($row->tanggal)->format('Y-m-d') ?? '-'}}</td>
                <td>{{$row->sppg->nama ?? '-'}}</td>
                <td>{{$row->nama}}</td>
                <td>{{count($row->items ?? [])}}</td>
                <td>
                    <div class="btn-group">
                        @if(!auth()->user()->hasRole('admin yayasan'))
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-menu" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-menu">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-menu" data-id="{{$row->id}}">
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
    $('#menu-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            
            {
                extend: 'pdf',
                title: 'Laporan Rekap Menu Maker', // Judul di dalam PDF
                filename: 'Data_Menu_Maker_PDF' // Nama file PDF
            },
            {
                extend: 'print',
                title: 'Cetak Laporan Menu Maker' // Judul halaman saat print
            }
        ],
    }).buttons().container().appendTo('#menu-table_wrapper .col-md-6:eq(0)');

    $(".btn-edit-menu").click(function () {
        $('#formMenu').trigger("reset");
        $(".modal-title").text('Edit Menu');
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const id = $(this).data('id');
        const url = "{!! url('menu-item') !!}/" + id + '/edit';
        $.get(url, function (data) {
            const row = data.menu;
            const itemIds = data.item_ids || [];
            const detailMenuNames = data.detail_menu_names || [];
            $("#menu_id").val(row.id);
            $("#menu_sppg_id").val(row.sppg_id).trigger('change');
            filterMenuMasterBySppg();
            $("#menu_tanggal").val(normalizeDateInputValue(row.tanggal));
            $("#menu_nama").val(row.nama);
            $("#menu_item_ids option").prop('selected', false);
            (itemIds || []).forEach(function(itemId) {
                $("#menu_item_ids option[value='" + itemId + "']").prop('selected', true);
            });
            $("#menu_item_ids").trigger('change');
            setDetailMenuInputs(detailMenuNames);
            $("#menu-copy-section").hide();
            $("#overlay-place").html('');
        });
    });

    $(document).on('click', '.btn-delete-menu', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data menu ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('menu-item') !!}/" + id;
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

