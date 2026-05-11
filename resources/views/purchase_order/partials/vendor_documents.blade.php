@php
    $dayNames = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];
@endphp

@foreach($vendorGroups as $groupIndex => $group)
    @php
        $vendor = $group['vendor'];
        $details = $group['details'];
        $poDate = $purchaseOrder->tanggal_po;
        $formattedPoDate = $poDate
            ? (($dayNames[(int) $poDate->format('w')] ?? '') . ', ' . $poDate->format('d-m-Y'))
            : '-';
        $vendorName = $vendor->nama ?? '-';
        $vendorAddress = $vendor->alamat ?? '-';
        $deliveryMethod = $vendor->metode_pengiriman ?? '-';
    @endphp

    <div class="po-document {{ !$loop->last ? 'with-page-break' : '' }}">
        @if(!empty($showVendorDownloadButtons) && !empty(optional($vendor)->id))
            <div class="po-document-toolbar text-right mb-2">
                <a href="{{ route('purchase_order.download_pdf_vendor', ['purchaseOrder' => $purchaseOrder->id, 'vendorId' => $vendor->id]) }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF Vendor
                </a>
            </div>
        @endif
        <table class="po-sheet">
            <tr>
                <td colspan="8" class="sheet-title">NOTA PESANAN BAHAN MAKANAN</td>
            </tr>
            <tr>
                <td colspan="8" class="sheet-subtitle">{{ $purchaseOrder->kode_po }}</td>
            </tr>
            <tr>
                <td class="meta-label-spacer"></td>
                <td class="meta-label">Dari</td>
                <td class="meta-separator">:</td>
                <td colspan="5" class="meta-value">{{ $purchaseOrder->sppg->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="meta-label-spacer"></td>
                <td class="meta-label">Kepada</td>
                <td class="meta-separator">:</td>
                <td colspan="5" class="meta-value">{{ $vendorName }}</td>
            </tr>
            <tr>
                <td class="meta-label-spacer"></td>
                <td class="meta-label">Alamat</td>
                <td class="meta-separator">:</td>
                <td colspan="5" class="meta-value">{{ $vendorAddress }}</td>
            </tr>
            <tr>
                <td class="meta-label-spacer"></td>
                <td class="meta-label">Waktu</td>
                <td class="meta-separator">:</td>
                <td colspan="5" class="meta-value">{{ $formattedPoDate }}</td>
            </tr>
            <tr>
                <th class="col-no">NO.</th>
                <th class="col-name">JENIS BAHAN</th>
                <th class="col-unit">SATUAN</th>
                <th class="col-qty text-center">QTY (EST)</th>
                <th class="col-qty text-center">QTY (REAL)</th>
                <th class="col-price text-right">HARGA (EST)</th>
                <th class="col-price text-right">HARGA (REAL)</th>
                <th class="col-price text-right">SUBTOTAL (REAL)</th>
            </tr>
            @forelse($details as $detailIndex => $detail)
                <tr>
                    <td class="text-center">{{ $detailIndex + 1 }}</td>
                    <td>{{ $detail->item_name }}</td>
                    <td>{{ $detail->item_satuan }}</td>
                    <td class="text-center">{{ rtrim(rtrim(number_format((float) $detail->qty, 2, ',', '.'), '0'), ',') }}</td>
                    <td class="text-center">{{ $detail->qty_diterima !== null ? rtrim(rtrim(number_format((float) $detail->qty_diterima, 2, ',', '.'), '0'), ',') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format((float) $detail->harga, 2, ',', '.') }}</td>
                    <td class="text-right">{{ $detail->harga_realisasi !== null ? 'Rp ' . number_format((float) $detail->harga_realisasi, 2, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $detail->subtotal_realisasi !== null ? 'Rp ' . number_format((float) $detail->subtotal_realisasi, 2, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-cell">Detail item tidak tersedia.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="7" class="total-label">TOTAL ESTIMASI</td>
                <td class="text-right total-value">Rp {{ number_format((float) $group['total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="7" class="total-label">GRAND TOTAL REALISASI</td>
                <td class="text-right total-value">Rp {{ number_format((float) $details->sum('subtotal_realisasi'), 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endforeach

