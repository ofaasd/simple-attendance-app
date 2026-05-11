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
                    <div class="card-header">
                        <h3 class="card-title mb-0">Daftar History Cash Out</h3>
                    </div>
                    <div class="card-body">
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

