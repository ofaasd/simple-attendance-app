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
                            <li class="breadcrumb-item active">{{ $title }}</li>
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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Daftar History Cash Out</h3>
                        @if(auth()->user()->hasRole('perwakilan yayasan') || auth()->user()->hasRole('admin'))
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-cashout">
                                Tambah Cash Out
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            Harap hati-hati saat menginput karena data yang sudah di input tidak dapat di edit atau delete.
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="filter_sppg_id">SPPG</label>
                                <select id="filter_sppg_id" class="form-control form-control-sm">
                                    <option value="">Semua SPPG</option>
                                    @foreach($sppg as $s)
                                        <option value="{{ $s->id }}" {{ count($sppg) == 1 ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_tanggal_start">Tanggal Dari</label>
                                <input type="date" id="filter_tanggal_start" class="form-control form-control-sm" value="{{date('Y-m') . "-" . "01"}}">
                            </div>
                            <div class="col-md-3">
                                <label for="filter_tanggal_end">Tanggal Sampai</label>
                                <input type="date" id="filter_tanggal_end" class="form-control form-control-sm" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="btn-filter" class="btn btn-secondary btn-sm">Filter</button>
                            </div>
                        </div>
                        <div id="table-container">
                            <!-- Table will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
$(document).ready(function() {
    loadTable();

    $('#btn-filter').on('click', function() {
        loadTable();
    });

    function loadTable() {
        const params = {
            filter_sppg_id: $('#filter_sppg_id').val(),
            filter_tanggal_start: $('#filter_tanggal_start').val(),
            filter_tanggal_end: $('#filter_tanggal_end').val(),
        };

        $.get('{{ $tableUrl }}', params, function(data) {
            $('#table-container').html(data);
        });
    }
});
</script>

<div class="modal fade" id="modal-add-cashout" tabindex="-1" role="dialog" aria-labelledby="modal-add-cashout-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-cashout-label">Tambah Cash Out</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cash_out.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sppg_id">SPPG</label>
                        <select name="sppg_id" id="sppg_id" class="form-control" required>
                            <option value="">Pilih SPPG</option>
                            @foreach($sppg as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jenis_cashout_id">Jenis Cash Out</label>
                        <select name="jenis_cashout_id" id="jenis_cashout_id" class="form-control" required>
                            <option value="">Pilih Jenis Cash Out</option>
                            @foreach($jenisCashouts as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" name="nominal" id="nominal" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

