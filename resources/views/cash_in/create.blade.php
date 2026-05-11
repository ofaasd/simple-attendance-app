<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Cash In</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('cash_in') }}">Cash In</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Data gagal disimpan.</strong>
                        <ul class="mb-0 mt-2 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Form Tambah Cash In</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">Harap hati-hati saat menginput cash in karena nominal yang masuk tidak bisa diubah-ubah dan jika ada nominal yang salah harus melakukan cash-out dengan alasan kesalahan nominal saat cash in</div>
                        <form action="{{ route('cash_in.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sppg_id">SPPG <span class="text-danger">*</span></label>
                                        <select name="sppg_id" id="sppg_id" class="form-control @error('sppg_id') is-invalid @enderror" required>
                                            <option value="">Pilih SPPG</option>
                                            @foreach($sppg as $s)
                                                <option value="{{ $s->id }}" {{ old('sppg_id', (count($sppg) == 1 ? $sppg->first()->id : null)) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('sppg_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                        @error('tanggal')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sumber_dana">Sumber Dana <span class="text-danger">*</span></label>
                                        <input type="text" name="sumber_dana" id="sumber_dana" class="form-control @error('sumber_dana') is-invalid @enderror" value="{{ old('sumber_dana') }}" placeholder="Contoh: Pemerintah" required>
                                        @error('sumber_dana')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jumlah_dana">Jumlah Dana <span class="text-danger">*</span></label>
                                        <input type="number" name="jumlah_dana" id="jumlah_dana" class="form-control @error('jumlah_dana') is-invalid @enderror" value="{{ old('jumlah_dana') }}" step="0.01" min="0.01" placeholder="0.00" required>
                                        @error('jumlah_dana')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('cash_in') }}" class="btn btn-secondary ml-2">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
