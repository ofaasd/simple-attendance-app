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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('distribusi_menu.index') }}">Distribusi Menu</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah Distribusi</h3>
                    </div>
                    <form action="{{ route('distribusi_menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Menu <span class="text-danger">*</span></label>
                                        <select name="id_menu" class="form-control select2" required>
                                            <option value="">-- Pilih Menu --</option>
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu->id }}" {{ old('id_menu') == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->nama }} ({{ optional($menu->tanggal)->format('d M Y') ?? '-' }}) - {{ $menu->sppg->nama ?? '-' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Waktu Pengiriman <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="tanggal_pengiriman" class="form-control" value="{{ old('tanggal_pengiriman', date('Y-m-d\TH:i')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Waktu Diterima</label>
                                        <input type="datetime-local" name="tanggal_diterima" class="form-control" value="{{ old('tanggal_diterima') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="on progress" {{ old('status') == 'on progress' ? 'selected' : '' }}>On Progress</option>
                                            <option value="on delivery" {{ old('status') == 'on delivery' ? 'selected' : '' }}>On Delivery</option>
                                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Jumlah (Terisi Otomatis) <span class="text-danger">*</span></label>
                                        <input type="number" name="jumlah" id="total_jumlah" class="form-control" value="{{ old('jumlah', 0) }}" min="0" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Foto Menu</label>
                                        <input type="file" name="foto_menu" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Maksimal 5MB.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Foto Suhu</label>
                                        <input type="file" name="foto_suhu" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Maksimal 5MB.</small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h4 class="mb-3">Detail Penerima Manfaat</h4>

                            @if($penerimaManfaatSekolah->count() > 0)
                            <div class="table-responsive mb-4">
                                <h5>Kategori Sekolah</h5>
                                <table class="table table-bordered table-sm table-penerima">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama</th>
                                            <th class="text-center" width="100">KECIL</th>
                                            <th class="text-center" width="100">BESAR</th>
                                            <th class="text-center" width="100">ORCIL</th>
                                            <th class="text-center" width="100">ORBES</th>
                                            <th class="text-center" width="100">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penerimaManfaatSekolah as $pm)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $pm->nama }}</td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_kecil]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_besar]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_orcil]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_orbes_sekolah]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm row-total" readonly value="0"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            @if($penerimaManfaatB3->count() > 0)
                            <div class="table-responsive mb-4">
                                <h5>Kategori B3</h5>
                                <table class="table table-bordered table-sm table-penerima">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama</th>
                                            <th class="text-center" width="100">BUMIL</th>
                                            <th class="text-center" width="100">BUSUI</th>
                                            <th class="text-center" width="100">BALITA</th>
                                            <th class="text-center" width="100">ORBES</th>
                                            <th class="text-center" width="100">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penerimaManfaatB3 as $pm)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $pm->nama }}</td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_bumil]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_busui]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_balita]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm input-calc" name="details[{{ $pm->id }}][jml_orbes_b3]" min="0" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm row-total" readonly value="0"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            @if($penerimaManfaatLainnya->count() > 0)
                            <div class="table-responsive mb-4">
                                <h5>Kategori Lainnya</h5>
                                <table class="table table-bordered table-sm table-penerima">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama</th>
                                            <th class="text-center" width="150">JUMLAH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penerimaManfaatLainnya as $pm)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $pm->nama }}</td>
                                            <td><input type="number" class="form-control form-control-sm input-calc row-total" name="details[{{ $pm->id }}][jml_lainnya]" min="0" value="0"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                                </div>

                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('distribusi_menu.index') }}" class="btn btn-default mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Auto-calculate totals
    $('.input-calc').on('input', function() {
        let grandTotal = 0;

        $('.table-penerima tbody tr').each(function() {
            let rowTotal = 0;
            $(this).find('.input-calc').each(function() {
                let val = parseInt($(this).val()) || 0;
                rowTotal += val;
            });
            // if this row has a .row-total that is readonly, update it
            let $rowTotalInput = $(this).find('.row-total[readonly]');
            if($rowTotalInput.length > 0) {
                $rowTotalInput.val(rowTotal);
            }
        });

        // calculate grand total
        $('.input-calc').each(function() {
            grandTotal += parseInt($(this).val()) || 0;
        });

        $('#total_jumlah').val(grandTotal);
    });
});
</script>
