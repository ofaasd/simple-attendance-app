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
                <th class="col-qty">JUMLAH</th>
                <th class="col-unit">SATUAN</th>
                <th colspan="2" class="col-price">ESTIMASI HARGA</th>
                <th colspan="2" class="col-delivery">KIRIM</th>
            </tr>
            @forelse($details as $detailIndex => $detail)
                <tr>
                    <td class="text-center">{{ $detailIndex + 1 }}</td>
                    <td>{{ $detail->item_name }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format((float) $detail->qty, 2, ',', '.'), '0'), ',') }}</td>
                    <td>{{ $detail->item_satuan }}</td>
                    <td class="currency-cell">Rp</td>
                    <td class="text-right">{{ number_format((float) $detail->harga, 2, ',', '.') }}</td>
                    <td colspan="2" class="delivery-cell">{{ $deliveryMethod }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-cell">Detail item tidak tersedia.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="4" class="total-label">TOTAL</td>
                <td class="currency-cell total-value">Rp</td>
                <td class="text-right total-value">{{ number_format((float) $group['total'], 2, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
@endforeach
