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

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Daftar Distribusi Menu</h3>
                        <div class="card-tools">
                            <a href="{{ route('distribusi_menu.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Distribusi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>SPPG</label>
                                <select id="filter_sppg_id" class="form-control form-control-sm">
                                    <option value="">Semua SPPG</option>
                                    @foreach($sppgList as $s)
                                        <option value="{{ $s->id }}" {{ count($sppgList) == 1 ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Tanggal Pengiriman Dari</label>
                                <input type="date" id="filter_tanggal_start" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Tanggal Pengiriman Sampai</label>
                                <input type="date" id="filter_tanggal_end" class="form-control form-control-sm" value="{{ date('Y-m-t') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="btn-filter" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>

                        <div id="table-container">
                            <!-- Table loaded via AJAX -->
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

        $.get('{{ route('distribusi_menu.get_table') }}', params, function(data) {
            $('#table-container').html(data);
        });
    }

    $(document).on('submit', '.form-delete', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data distribusi ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

