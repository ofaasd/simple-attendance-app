<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Purchase Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('purchase_order')}}">Purchase Order</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{session('success')}}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{session('error')}}</div>
                @endif

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Informasi Purchase Order</h3>
                        <div class="card-tools">
                            @if(auth()->user()->hasRole('employee'))
                                <a href="{{ route('purchase_order.received', $purchaseOrder->id) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-box-open mr-1"></i> Penerimaan Barang
                                </a>
                            @endif
                            <a href="{{ route('purchase_order.download_pdf', $purchaseOrder->id) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf mr-1"></i> Download Semua PO
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <strong>Kode PO</strong>
                                <div>{{ $purchaseOrder->kode_po }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>Tanggal PO</strong>
                                <div>{{ optional($purchaseOrder->tanggal_po)->format('d M Y') ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>SPPG</strong>
                                <div>{{ $purchaseOrder->sppg->nama ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>Status</strong>
                                <div>
                                    <span class="badge {{ $purchaseOrder->status_badge_class }}">{{ $purchaseOrder->status_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 mb-2">
                                <strong>Periode Menu</strong>
                                <div>{{ optional($purchaseOrder->tanggal_menu_dari)->format('d M Y') ?? '-' }} s/d {{ optional($purchaseOrder->tanggal_menu_sampai)->format('d M Y') ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>Jumlah Item</strong>
                                <div>{{ $purchaseOrder->details->count() }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>Total Estimasi (PO)</strong>
                                <div>Rp {{ number_format((float) $purchaseOrder->total_bayar, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <strong>Grand Total Realisasi</strong>
                                <div>
                                    @php $grandTotalRealisasi = $purchaseOrder->details->sum('subtotal_realisasi'); @endphp
                                    Rp {{ number_format((float) $grandTotalRealisasi, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        @php
                            $reviewStage = null;

                            if (auth()->user()->hasRole('akuntan') && (int) $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED) {
                                $reviewStage = 'akuntan';
                            } elseif (auth()->user()->hasRole('verval') && (int) $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_APPROVED_AKUNTAN) {
                                $reviewStage = 'verval';
                            } elseif (auth()->user()->hasRole('head') && (int) $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_APPROVED_VERVAL) {
                                $reviewStage = 'head';
                            }
                        @endphp

                        @if($reviewStage)
                            <div class="mt-3">
                                <form action="{{route('purchase_order.review', ['purchaseOrder' => $purchaseOrder->id, 'stage' => $reviewStage])}}" method="POST" class="d-inline-block form-review-po" data-stage="{{$reviewStage}}">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="comment" value="">
                                    <button type="submit" class="btn btn-success">Setujui PO</button>
                                </form>
                                <form action="{{route('purchase_order.review', ['purchaseOrder' => $purchaseOrder->id, 'stage' => $reviewStage])}}" method="POST" class="d-inline-block form-review-po-reject" data-stage="{{$reviewStage}}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="comment" value="">
                                    <button type="submit" class="btn btn-danger">Tidak Setujui PO</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Dokumen PO Per Vendor</h3>
                    </div>
                    <div class="card-body po-document-preview">
                        @include('purchase_order.partials.vendor_documents', ['vendorGroups' => $vendorGroups, 'purchaseOrder' => $purchaseOrder, 'showVendorDownloadButtons' => true])
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('purchase_order') }}" class="btn btn-default">Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<style>
    .po-document-preview {
        background: #f4f6f9;
    }

    .po-document {
        background: #fff;
        padding: 10px;
        border: 1px solid #d8dee4;
        margin-bottom: 18px;
        overflow-x: auto;
    }

    .po-sheet {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Times New Roman', serif;
        font-size: 14px;
        color: #000;
    }

    .po-sheet td,
    .po-sheet th {
        border: 1px solid #333;
        padding: 4px 6px;
        vertical-align: middle;
    }

    .po-sheet .sheet-title {
        background: #ffef3a;
        font-weight: 700;
        text-align: center;
        font-size: 22px;
        letter-spacing: 0.4px;
    }

    .po-sheet .sheet-subtitle {
        text-align: center;
        font-weight: 700;
        font-size: 18px;
    }

    .po-sheet .meta-label-spacer {
        width: 7%;
    }

    .po-sheet .meta-label {
        width: 14%;
        font-weight: 700;
    }

    .po-sheet .meta-separator {
        width: 3%;
        text-align: center;
        font-weight: 700;
    }

    .po-sheet .meta-value {
        font-size: 15px;
    }

    .po-sheet .col-no,
    .po-sheet .col-name,
    .po-sheet .col-qty,
    .po-sheet .col-unit,
    .po-sheet .col-price,
    .po-sheet .col-delivery {
        background: #dce6f1;
        text-align: center;
        font-weight: 700;
    }

    .po-sheet .col-no {
        width: 7%;
    }

    .po-sheet .col-name {
        width: 23%;
    }

    .po-sheet .col-qty {
        width: 12%;
    }

    .po-sheet .col-unit {
        width: 10%;
    }

    .po-sheet .col-price {
        width: 24%;
    }

    .po-sheet .col-delivery {
        width: 24%;
    }

    .po-sheet .currency-cell {
        width: 6%;
        text-align: left;
        white-space: nowrap;
    }

    .po-sheet .text-center {
        text-align: center;
    }

    .po-sheet .text-right {
        text-align: right;
    }

    .po-sheet .delivery-cell {
        text-align: center;
        font-weight: 700;
    }

    .po-sheet .empty-cell,
    .po-sheet .total-label,
    .po-sheet .total-value {
        font-weight: 700;
    }

    .po-sheet .total-label {
        text-align: right;
    }

    @media (max-width: 768px) {
        .po-sheet {
            min-width: 900px;
        }
    }
</style>

<script>
    $('.form-review-po').on('submit', function (e) {
        e.preventDefault();
        const form = this;

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
