<table class="table table-bordered table-hover" id="sppg-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama SPPG</th>
            <th>Alamat</th>
            <th>Lokasi</th>
            <th>Lat</th>
            <th>Lng</th>
            <th>Penanggung Jawab</th>
            <th>Approver</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sppg as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$row->nama}}</td>
                <td>{{$row->alamat}}</td>
                <td>
                    @if($row->location)
                        <a href="{{$row->location}}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-map-marker-alt"></i> Lihat Lokasi
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{$row->lat ?? '-'}}</td>
                <td>{{$row->lng ?? '-'}}</td>
                <td>{{$row->user->name ?? '-'}}</td>
                <td>
                    @if(($row->users ?? collect())->isNotEmpty())
                        {{ $row->users->pluck('name')->implode(', ') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-sppg"
                            data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-sppg">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-sppg" data-id="{{$row->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#sppg-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#sppg-table_wrapper .col-md-6:eq(0)');

    $(".btn-edit-sppg").click(function () {
        $('#formSppg').trigger("reset");
        $(".modal-title").text('Edit SPPG');
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const id = $(this).data('id');
        const url = "{!! url('sppg') !!}/" + id + '/edit';
        $.get(url, function (data) {
            const row = data[0];
            $("#sppg_id").val(row.id);
            $("#sppg_nama").val(row.nama);
            $("#sppg_alamat").val(row.alamat);
            $("#sppg_location").val(row.location);
            $("#sppg_lat").val(row.lat);
            $("#sppg_lng").val(row.lng);
            $("#sppg_user_id").val(row.user_id).trigger('change');
            $("#sppg_approver_user_ids").val(row.approver_user_ids ?? []).trigger('change');
            $("#overlay-place").html('');
        });
    });

    $(document).on('click', '.btn-delete-sppg', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data SPPG ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-default' }
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('sppg') !!}/" + id;
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: "{!! csrf_token() !!}" },
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Data berhasil dihapus.', timer: 1500, showConfirmButton: false });
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
