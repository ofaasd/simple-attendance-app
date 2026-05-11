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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Rekap Cashflow</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="filter_sppg_id">SPPG</label>
                                <select id="filter_sppg_id" class="form-control form-control-sm" {{ isset($disableSppgFilter) && $disableSppgFilter ? 'disabled' : '' }}>
                                    @if(empty($disableSppgFilter))
                                        <option value="">Semua SPPG</option>
                                    @endif
                                    @foreach($sppg as $s)
                                        <option value="{{ $s->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                                @if(isset($disableSppgFilter) && $disableSppgFilter)
                                    <small class="form-text text-muted">Filter SPPG dinonaktifkan; hanya SPPG Anda yang ditampilkan.</small>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label for="filter_tanggal_start">Tanggal Dari</label>
                                <input type="date" id="filter_tanggal_start" class="form-control form-control-sm" value="{{ date('Y-m') . '-01' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="filter_tanggal_end">Tanggal Sampai</label>
                                <input type="date" id="filter_tanggal_end" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="btn-filter" class="btn btn-primary btn-sm">Filter</button>
                            </div>
                        </div>

                        <div id="table-container"></div>
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

    function isValidDateRange(start, end) {
        if (!start || !end) {
            return true;
        }

        const startDate = new Date(start);
        const endDate = new Date(end);
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = diffTime / (1000 * 60 * 60 * 24);
        return diffDays <= 31;
    }

    function loadTable() {
        const start = $('#filter_tanggal_start').val();
        const end = $('#filter_tanggal_end').val();

        if (!isValidDateRange(start, end)) {
            alert('Rentang tanggal maksimal 1 bulan. Silakan pilih tanggal yang lebih pendek.');
            return;
        }

        const params = {
            filter_sppg_id: $('#filter_sppg_id').val(),
            filter_tanggal_start: start,
            filter_tanggal_end: end,
        };

        $.get('{{ $tableUrl }}', params, function(data) {
            $('#table-container').html(data);
        });
    }
});
</script>
