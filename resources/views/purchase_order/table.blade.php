<table class="table table-bordered table-hover" id="purchase-order-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal PO</th>
            <th>Kode PO</th>
            <th>SPPG</th>
            <th>Periode Menu</th>
            <th>Jumlah Item</th>
            <th>Total Bayar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseOrders as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{optional($row->tanggal_po)->format('Y-m-d') ?? '-'}}</td>
                <td>{{$row->kode_po}}</td>
                <td>{{$row->sppg->nama ?? '-'}}</td>
                <td>{{optional($row->tanggal_menu_dari)->format('Y-m-d')}} s/d {{optional($row->tanggal_menu_sampai)->format('Y-m-d')}}</td>
                <td>{{count($row->details ?? [])}}</td>
                <td>Rp {{number_format((float) $row->total_bayar, 2, ',', '.')}}</td>
                <td>
                    <span class="badge {{$row->status_badge_class}}">{{$row->status_label}}</span>
                    @if(!empty($row->last_rejection_comment))
                        <small class="d-block text-danger mt-1">
                            Rejected by {{$row->last_rejected_by_role_label ?? ucfirst($row->last_rejected_by_role)}}: {{$row->last_rejection_comment}}
                        </small>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{route('purchase_order.show', $row->id)}}" class="btn btn-sm btn-secondary" title="Detail"><i class="fas fa-eye"></i></a>

                        @if(auth()->user()->hasRole('employee') && (int) $row->status !== 4)
                            <a href="{{route('purchase_order.received', $row->id)}}" class="btn btn-sm btn-warning" title="Penerimaan Barang"><i class="fas fa-box-open"></i></a>
                        @endif

                        @if(auth()->user()->hasRole('employee') && (int) $row->status === \App\Models\PurchaseOrder::STATUS_DRAFTED)
                            <a href="{{route('purchase_order.edit', $row->id)}}" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>

                            <form action="{{route('purchase_order.destroy', $row->id)}}" method="POST" class="form-delete-po">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        @elseif(auth()->user()->hasRole('akuntan') && (int) $row->status === \App\Models\PurchaseOrder::STATUS_RECEIVED)
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'akuntan'])}}" method="POST" class="form-review-po" data-stage="akuntan">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-success" title="Setujui Akuntan">Setujui</button>
                            </form>
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'akuntan'])}}" method="POST" class="form-review-po-reject" data-stage="akuntan">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-danger" title="Tidak Setujui Akuntan">Tidak Setujui</button>
                            </form>
                        @elseif(auth()->user()->hasRole('verval') && (int) $row->status === \App\Models\PurchaseOrder::STATUS_APPROVED_AKUNTAN)
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'verval'])}}" method="POST" class="form-review-po" data-stage="verval">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-success" title="Setujui Verval">Setujui</button>
                            </form>
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'verval'])}}" method="POST" class="form-review-po-reject" data-stage="verval">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-danger" title="Tidak Setujui Verval">Tidak Setujui</button>
                            </form>
                        @elseif(auth()->user()->hasRole('head') && (int) $row->status === \App\Models\PurchaseOrder::STATUS_APPROVED_VERVAL)
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'head'])}}" method="POST" class="form-review-po" data-stage="head">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-success" title="Setujui Head">Setujui</button>
                            </form>
                            <form action="{{route('purchase_order.review', ['purchaseOrder' => $row->id, 'stage' => 'head'])}}" method="POST" class="form-review-po-reject" data-stage="head">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="comment" value="">
                                <button type="submit" class="btn btn-sm btn-danger" title="Tidak Setujui Head">Tidak Setujui</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-secondary" disabled>-</button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#purchase-order-table').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        order: [[1, 'desc']]
    });

    $('.form-delete-po').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Purchase Order?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.form-request-po').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            icon: 'question',
            title: 'Ajukan ke Akuntan?',
            text: 'Setelah diajukan, Purchase Order tidak dapat diedit lagi.',
            showCancelButton: true,
            confirmButtonText: 'Ya, ajukan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.form-review-po').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const stage = $(this).data('stage');

        Swal.fire({
            icon: 'question',
            title: 'Setujui PO?',
            text: 'Purchase Order akan lanjut ke tahap berikutnya.',
            input: 'textarea',
            inputPlaceholder: 'Komentar (opsional)',
            showCancelButton: true,
            confirmButtonText: 'Ya, setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $(form).find('input[name="comment"]').val((result.value || '').trim());
                form.submit();
            }
        });
    });

    $('.form-review-po-reject').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const stage = $(this).data('stage');

        Swal.fire({
            icon: 'warning',
            title: 'Tidak setujui PO?',
            text: 'Purchase Order akan dikembalikan ke status Draft.',
            input: 'textarea',
            inputPlaceholder: 'Komentar wajib diisi',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Komentar wajib diisi.';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, tidak setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $(form).find('input[name="comment"]').val((result.value || '').trim());
                form.submit();
            }
        });
    });
</script>
