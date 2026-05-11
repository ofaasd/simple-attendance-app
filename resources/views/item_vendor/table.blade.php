<table class="table table-bordered table-hover" id="item-vendor-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>SPPG</th>
            <th>Item</th>
            <th>Vendor</th>
            <th>Harga</th>
            <th>Rank</th>
            <th>Status Harga</th>
            @if(!empty($isHr) && $isHr)
                <th>Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($itemVendor as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{optional($row->tanggal)->format('Y-m-d') ?? '-'}}</td>
                <td>{{$row->sppg->nama ?? '-'}}</td>
                <td>{{$row->item->nama ?? '-'}}</td>
                <td>{{$row->vendor->kode_vendor ?? '-'}} - {{$row->vendor->nama ?? '-'}}</td>
                <td>Rp {{number_format((float) $row->harga, 2, ',', '.')}}</td>
                <td>{{$row->rank}}</td>
                <td>
                    @if(in_array($row->id, $latestIds))
                        <span class="badge badge-success">Terbaru</span>
                    @else
                        <span class="badge badge-secondary">Riwayat</span>
                    @endif
                </td>
                @if(!empty($isHr) && $isHr)
                    <td>
                        <div class="btn-group">
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-item-vendor" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-item-vendor">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-delete-item-vendor" data-id="{{$row->id}}">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#item-vendor-table').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[1, 'desc']]
    });

    function normalizeDateInputValue(value) {
        if (!value) {
            return '';
        }

        return String(value).slice(0, 10);
    }

    @if(!empty($isHr) && $isHr)
    $(".btn-edit-item-vendor").click(function () {
        $('#formItemVendor').trigger("reset");
        $(".modal-title").text('Edit Harga Vendor');
        $('#section-add-mode').hide();
        $('#section-edit-mode').show();
        $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);

        const id = $(this).data('id');
        const url = "{!! url('item-vendor') !!}/" + id + '/edit';
        $.get(url, function (data) {
            const row = data.item_vendor;
            $('#item_vendor_id').val(row.id);
            $('#item_vendor_sppg_id').val(row.sppg_id).trigger('change');
            filterItemsBySppg();
            $('#item_vendor_item_id').val(row.item_id).trigger('change');
            $('#item_vendor_vendor_id').val(row.vendor_id).trigger('change');
            $('#item_vendor_tanggal').val(normalizeDateInputValue(row.tanggal));
            $('#item_vendor_harga').val(row.harga);
            $('#item_vendor_rank').val(row.rank);
            updateUomHint();
            $("#overlay-place").html('');
        }).fail(function (xhr) {
            $("#overlay-place").html('');
            let msg = 'Gagal mengambil data.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
        });
    });

    $(document).on('click', '.btn-delete-item-vendor', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data harga vendor item ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{!! url('item-vendor') !!}/" + id;
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
    @endif
</script>

