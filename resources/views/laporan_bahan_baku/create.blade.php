<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Generate Laporan Bahan Baku Maker</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('laporan_bahan_baku.index') }}">Laporan Bahan Baku Maker</a></li>
                            <li class="breadcrumb-item active">Generate</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form method="POST" action="{{ route('laporan_bahan_baku.generate') }}" id="form-generate-laporan-bahan-baku">
                    @csrf

                    {{-- Form Header Laporan --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Laporan</h3>
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
                                    <label for="nama_laporan">Nama Laporan <span class="text-danger">*</span></label>
                                    <input type="text"
                                           id="nama_laporan"
                                           name="nama_laporan"
                                           class="form-control @error('nama_laporan') is-invalid @enderror"
                                           value="{{ old('nama_laporan') }}"
                                           placeholder="Contoh: Laporan Bahan Baku Mei 2026"
                                           required>
                                    @error('nama_laporan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="tanggal_laporan">Tanggal Laporan <span class="text-danger">*</span></label>
                                    <input type="date"
                                           id="tanggal_laporan"
                                           name="tanggal_laporan"
                                           class="form-control @error('tanggal_laporan') is-invalid @enderror"
                                           value="{{ old('tanggal_laporan', $today) }}"
                                           required>
                                    @error('tanggal_laporan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="estimasi_tanggal_bayar">Estimasi Tanggal Bayar <span class="text-danger">*</span></label>
                                    <input type="date"
                                           id="estimasi_tanggal_bayar"
                                           name="estimasi_tanggal_bayar"
                                           class="form-control @error('estimasi_tanggal_bayar') is-invalid @enderror"
                                           value="{{ old('estimasi_tanggal_bayar') }}"
                                           required>
                                    @error('estimasi_tanggal_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Data PO receive yang sudah pernah dipakai laporan lain <strong>tidak ditampilkan</strong> di halaman ini.
                            </div>
                        </div>
                    </div>

                    {{-- Preview Data yang akan di-generate --}}
                    <div class="card card-info card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Preview Data PO Receive (yang akan dimasukkan ke laporan)</h3>
                            <div>
                                <span class="badge badge-info">{{ $availableCount }} item tersedia</span>
                                <button type="button" class="btn btn-xs btn-outline-danger ml-2" id="btn-exclude-checked">Exclude yang dipilih</button>
                                <button type="button" class="btn btn-xs btn-outline-success ml-1" id="btn-include-all">Pilih Semua</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($previewByVendor->isEmpty())
                                <div class="p-3">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Tidak ada PO receive yang bisa dipakai. Semua data mungkin sudah masuk ke laporan bahan baku maker lain.
                                    </div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" style="width:45px">#</th>
                                                <th class="text-center" style="width:55px">Pilih</th>
                                                <th style="width:110px">Tanggal PO</th>
                                                <th style="width:120px">Kode PO</th>
                                                <th>Nama Bahan</th>
                                                <th class="text-right" style="width:120px">Volume</th>
                                                <th class="text-right" style="width:140px">Harga Satuan (Rp)</th>
                                                <th class="text-right" style="width:150px">Total (Rp)</th>
                                                <th style="width:180px">Penyuplai</th>
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
                                                        <td class="text-right">
                                                            {{ number_format((float) $row->volume, 2, ',', '.') }} {{ $row->uom_nama }}
                                                        </td>
                                                        <td class="text-right">Rp {{ number_format((float) $row->harga_satuan, 2, ',', '.') }}</td>
                                                        <td class="text-right">Rp {{ number_format((float) $row->total, 2, ',', '.') }}</td>
                                                        <td class="text-muted small">{{ $group['vendor_label'] }}</td>
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
                            <a href="{{ route('laporan_bahan_baku.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" {{ $availableCount === 0 ? 'disabled' : '' }}>
                                <i class="fas fa-cog mr-1"></i> Generate Laporan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>
</x-app-layout>

<script>
    $('#btn-exclude-checked').on('click', function () {
        $('.detail-selector:checked').prop('checked', false);
    });

    $('#btn-include-all').on('click', function () {
        $('.detail-selector').prop('checked', true);
    });
</script>

