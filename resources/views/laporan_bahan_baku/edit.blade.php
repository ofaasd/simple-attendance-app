<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('laporan_bahan_baku.index') }}">Laporan Bahan Baku Maker</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form method="POST" action="{{ route('laporan_bahan_baku.update', $reportAnchor->id) }}" id="form-edit-laporan-bahan-baku">
                    @csrf
                    @method('PUT')
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Header Laporan</h3>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nama_laporan">Nama Laporan Bahan Baku</label>
                                    <input type="text"
                                           id="nama_laporan"
                                           name="nama_laporan"
                                           class="form-control @error('nama_laporan') is-invalid @enderror"
                                           value="{{ old('nama_laporan', $reportAnchor->nama_laporan) }}"
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="tanggal_laporan">Tanggal Pembuatan</label>
                                    <input type="date"
                                           id="tanggal_laporan"
                                           name="tanggal_laporan"
                                           class="form-control @error('tanggal_laporan') is-invalid @enderror"
                                           value="{{ old('tanggal_laporan', optional($reportAnchor->tanggal_laporan)->toDateString()) }}"
                                           required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="estimasi_tanggal_bayar">Tanggal Estimasi Pembayaran</label>
                                    <input type="date"
                                           id="estimasi_tanggal_bayar"
                                           name="estimasi_tanggal_bayar"
                                           class="form-control @error('estimasi_tanggal_bayar') is-invalid @enderror"
                                           value="{{ old('estimasi_tanggal_bayar', optional($reportAnchor->estimasi_tanggal_bayar)->toDateString()) }}"
                                           required>
                                </div>
                            </div>

                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-1"></i>
                                Anda bisa meng-exclude baris PO receive untuk memisahkan pembayaran. PO receive yang sudah dipakai laporan lain tidak ditampilkan.
                            </div>
                        </div>
                    </div>
                    <div class="card card-info card-outline mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Detail PO Receive untuk Laporan Ini</h3>
                            <div>
                                <span class="badge badge-info">{{ $availableCount }} item tersedia</span>
                                <button type="button" class="btn btn-xs btn-outline-danger ml-2" id="btn-edit-exclude-checked">Exclude yang dipilih</button>
                                <button type="button" class="btn btn-xs btn-outline-success ml-1" id="btn-edit-include-all">Pilih Semua</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($previewByVendor->isEmpty())
                                <div class="p-3">
                                    <div class="alert alert-warning mb-0">
                                        Tidak ada data PO receive yang tersedia untuk laporan ini.
                                    </div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" style="width:45px">#</th>
                                                <th class="text-center" style="width:55px">Pilih</th>
                                                <th style="width:110px">Tanggal PO</th>
                                                <th style="width:120px">Kode PO</th>
                                                <th>Nama Bahan</th>
                                                <th class="text-right" style="width:120px">Volume</th>
                                                <th class="text-right" style="width:170px">Harga Realisasi (Rp)</th>
                                                <th class="text-right" style="width:160px">Total (Rp)</th>
                                                <th style="width:170px">Vendor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($previewByVendor as $group)
                                                <tr>
                                                    <td colspan="8" class="font-weight-bold pl-2 table-secondary">
                                                        <i class="fas fa-store mr-1"></i> {{ $group['vendor_label'] }}
                                                    </td>
                                                    <td class="text-right font-weight-bold table-secondary">
                                                        Rp {{ number_format((float) $group['vendor_total'], 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                                @foreach($group['rows'] as $row)
                                                    @php
                                                        $checked = in_array((int) $row->source_detail_id, $selectedDetailIds, true);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $no++ }}</td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="detail-selector" name="included_detail_ids[]" value="{{ $row->source_detail_id }}" {{ $checked ? 'checked' : '' }}>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-M-y') }}</td>
                                                        <td>{{ $row->kode_po }}</td>
                                                        <td>{{ $row->item_nama }}</td>
                                                        <td class="text-right">{{ number_format((float) $row->volume, 2, ',', '.') }} {{ $row->uom_nama }}</td>
                                                        <td class="text-right">Rp {{ number_format((float) $row->harga_satuan, 2, ',', '.') }}</td>
                                                        <td class="text-right">Rp {{ number_format((float) $row->total, 2, ',', '.') }}</td>
                                                        <td>{{ trim(($row->kode_vendor ?? '') . ' ' . ($row->vendor_nama ?? '')) ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold bg-light">
                                                <td colspan="7" class="text-right">Grand Total Dipilih</td>
                                                <td class="text-right">Rp {{ number_format((float) $grandTotal, 2, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('laporan_bahan_baku.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary" {{ $availableCount === 0 ? 'disabled' : '' }}>Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
    $('#btn-edit-exclude-checked').on('click', function () {
        $('.detail-selector:checked').prop('checked', false);
    });

    $('#btn-edit-include-all').on('click', function () {
        $('.detail-selector').prop('checked', true);
    });
</script>

