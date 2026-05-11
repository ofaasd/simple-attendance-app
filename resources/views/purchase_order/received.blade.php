<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Penerimaan Barang</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('purchase_order')}}">Purchase Order</a></li>
                            <li class="breadcrumb-item"><a href="{{route('purchase_order.show', $purchaseOrder->id)}}">Detail PO</a></li>
                            <li class="breadcrumb-item active">Penerimaan Barang</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Data penerimaan gagal disimpan.</strong>
                        <ul class="mb-0 mt-2 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('purchase_order.store_received', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data" id="form-received">
                    @csrf
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Informasi Purchase Order</h3>
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
                            <div class="col-md-4 mb-2">
                                <strong>Periode Menu</strong>
                                <div>{{ optional($purchaseOrder->tanggal_menu_dari)->format('d M Y') ?? '-' }} s/d {{ optional($purchaseOrder->tanggal_menu_sampai)->format('d M Y') ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Total PO</strong>
                                <div>Rp {{ number_format((float) $purchaseOrder->total_bayar, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Jumlah Item</strong>
                                <div>{{ $purchaseOrder->details->count() }}</div>
                            </div>
                        </div>
                        @php
                            $selectedStatus = old('status') ?: (string) $purchaseOrder->status;
                        @endphp
                        <div class="row mt-3">
                            <div class="col-md-4 mb-2">
                                <strong>Status PO</strong>
                                @if($isEditable)
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="0" {{ $selectedStatus === '0' ? 'selected' : '' }}>Draft</option>
                                        <option value="1" {{ $selectedStatus === '1' ? 'selected' : '' }}>Penerimaan Barang</option>
                                        <option value="2" {{ $selectedStatus === '2' ? 'selected' : '' }}>Approved by Akuntan</option>
                                        <option value="3" {{ $selectedStatus === '3' ? 'selected' : '' }}>Approved by Perwakilan Yayasan</option>
                                        <option value="4" {{ $selectedStatus === '4' ? 'selected' : '' }}>Approved by Kepala SPPG</option>
                                    </select>
                                @else
                                    <div>{{ \App\Models\PurchaseOrder::statusLabels()[$purchaseOrder->status] ?? 'Unknown' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                

                    @foreach($vendorGroups as $group)
                        @php
                            $vendor = $group['vendor'];
                            $details = $group['details'];
                            $receipt = $group['receipt'];
                            $vendorId = optional($vendor)->id;
                        @endphp

                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-store mr-2"></i>
                                    {{ $group['vendor_label'] ?: 'Vendor Tidak Diketahui' }}
                                    @if($vendor && $vendor->alamat)
                                        <small class="text-muted ml-2">— {{ $vendor->alamat }}</small>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" style="width:40px">No</th>
                                                <th>Item</th>
                                                <th class="text-center" style="width:80px">Satuan</th>
                                                <th class="text-right" style="width:100px">QTY PO</th>
                                                <th class="text-right" style="width:120px">QTY Diterima</th>
                                                <th class="text-right" style="width:130px">Harga Satuan</th>
                                                <th class="text-right" style="width:140px">Harga Realisasi</th>
                                                <th class="text-right" style="width:140px">Subtotal PO</th>
                                                <th class="text-right" style="width:150px">Subtotal Realisasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $i => $detail)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td>
                                                        {{ $detail->item->nama ?? $detail->custom_item_name ?? '-' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $detail->item->uom->nama ?? $detail->customUom->nama ?? $detail->custom_item_satuan ?? '-' }}
                                                    </td>
                                                    <td class="text-right">{{ number_format((float) $detail->qty, 2, ',', '.') }}</td>
                                                    <td class="text-right">
                                                        @if($isEditable)
                                                            <input type="number"
                                                                   name="received_qtys[{{ $detail->id }}]"
                                                                   class="form-control form-control-sm text-right qty-diterima"
                                                                   data-detail-id="{{ $detail->id }}"
                                                                   value="{{ old('received_qtys.'.$detail->id, $detail->qty_diterima) }}"
                                                                   min="0"
                                                                   step="0.01"
                                                                   placeholder="0">
                                                        @else
                                                            {{ $detail->qty_diterima !== null ? number_format((float) $detail->qty_diterima, 2, ',', '.') : '-' }}
                                                        @endif
                                                    </td>
                                                    <td class="text-right">Rp {{ number_format((float) $detail->harga, 2, ',', '.') }}</td>
                                                    <td class="text-right">
                                                        @if($isEditable)
                                                            <input type="number"
                                                                   name="realized_prices[{{ $detail->id }}]"
                                                                   class="form-control form-control-sm text-right harga-realisasi"
                                                                   data-detail-id="{{ $detail->id }}"
                                                                   value="{{ old('realized_prices.'.$detail->id, $detail->harga_realisasi) }}"
                                                                   min="0"
                                                                   step="0.01"
                                                                   placeholder="0">
                                                        @else
                                                            {{ $detail->harga_realisasi !== null ? 'Rp '.number_format((float) $detail->harga_realisasi, 2, ',', '.') : '-' }}
                                                        @endif
                                                    </td>
                                                    <td class="text-right">Rp {{ number_format((float) $detail->subtotal, 2, ',', '.') }}</td>
                                                    <td class="text-right">
                                                        <span class="subtotal-realisasi" id="subtotal-{{ $detail->id }}">
                                                            @if($detail->subtotal_realisasi !== null)
                                                                Rp {{ number_format((float) $detail->subtotal_realisasi, 2, ',', '.') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold bg-light">
                                                <td colspan="7" class="text-right">Total</td>
                                                <td class="text-right">Rp {{ number_format($group['total'], 2, ',', '.') }}</td>
                                                <td class="text-right">
                                                    <span class="vendor-total-realisasi" data-vendor="{{ $vendorId }}">
                                                        @php
                                                            $totalRealisasi = $details->sum(fn($d) => (float) ($d->subtotal_realisasi ?? 0));
                                                        @endphp
                                                        @if($details->every(fn($d) => $d->subtotal_realisasi === null))
                                                            -
                                                        @else
                                                            Rp {{ number_format($totalRealisasi, 2, ',', '.') }}
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="px-3 py-3 border-top">
                                    <strong>Nota / Bukti Penerimaan Vendor ini:</strong>
                                    @if($receipt && $receipt->nota_path)
                                        <div class="mt-1 mb-2">
                                                          <a href="{{ asset('storage/' . ltrim($receipt->nota_path, '/')) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-paperclip mr-1"></i> Lihat Nota Tersimpan
                                            </a>
                                        </div>
                                    @endif
                                    @if($isEditable)
                                        <div class="custom-file mt-1" style="max-width:400px">
                                            <input type="file"
                                                   class="custom-file-input @error('vendor_receipts.'.$vendorId) is-invalid @enderror"
                                                   name="vendor_receipts[{{ $vendorId }}]"
                                                   id="nota-{{ $vendorId }}"
                                                   accept=".pdf,.jpg,.jpeg,.png">
                                            <label class="custom-file-label" for="nota-{{ $vendorId }}">
                                                {{ $receipt && $receipt->nota_path ? 'Ganti nota...' : 'Pilih file nota...' }}
                                            </label>
                                        </div>
                                        @error('vendor_receipts.'.$vendorId)
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Format: PDF, JPG, PNG. Maks 5MB.</small>
                                    @else
                                        @if(!$receipt || !$receipt->nota_path)
                                            <span class="text-muted">Belum ada nota diunggah.</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 mb-5">
                        <a href="{{ route('purchase_order.show', $purchaseOrder->id) }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail PO
                        </a>
                        @if($isEditable)
                            <button type="submit" class="btn btn-success ml-2">
                                <i class="fas fa-save mr-1"></i> Simpan Penerimaan
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </section>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function formatRp(value) {
        if (value === null || value === '' || isNaN(value)) return '-';
        return 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function recalcSubtotal(detailId) {
        var qtyInput = document.querySelector('.qty-diterima[data-detail-id="' + detailId + '"]');
        var hargaInput = document.querySelector('.harga-realisasi[data-detail-id="' + detailId + '"]');
        var subtotalEl = document.getElementById('subtotal-' + detailId);

        if (!qtyInput || !hargaInput || !subtotalEl) return;

        var qty = parseFloat(qtyInput.value);
        var harga = parseFloat(hargaInput.value);

        if (!isNaN(qty) && !isNaN(harga) && qtyInput.value !== '' && hargaInput.value !== '') {
            subtotalEl.textContent = formatRp(qty * harga);
        } else {
            subtotalEl.textContent = '-';
        }
    }

    document.querySelectorAll('.qty-diterima, .harga-realisasi').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcSubtotal(this.dataset.detailId);
        });
    });

    // Custom file input label
    document.querySelectorAll('.custom-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var label = this.nextElementSibling;
            if (this.files && this.files.length > 0) {
                label.textContent = this.files[0].name;
            }
        });
    });
});
</script>
