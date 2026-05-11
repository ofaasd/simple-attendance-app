<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{$title}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{$title}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-md-12">
                        <div class="card-header">
                            @if(auth()->user()->hasRole\('perwakilan\ yayasan'\))
                                <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#modal-generate-po">+ Generate Purchase Order</a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{session('error')}}</div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="filter_po_sppg_id">SPPG</label>
                                        @if(!empty($isEmployee) && $isEmployee)
                                            <input type="hidden" id="filter_po_sppg_id" value="{{ optional($sppg->first())->id }}">
                                            <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                        @else
                                            <select id="filter_po_sppg_id" class="form-control">
                                                <option value="">Semua SPPG</option>
                                                @foreach($sppg as $row)
                                                    <option value="{{$row->id}}">{{$row->nama}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="filter_po_tanggal_start">Tanggal PO Dari</label>
                                        <input type="date" id="filter_po_tanggal_start" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="filter_po_tanggal_end">Tanggal PO Sampai</label>
                                        <input type="date" id="filter_po_tanggal_end" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-info mr-2" id="btn-filter-po">Filter</button>
                                    <button type="button" class="btn btn-default" id="btn-reset-filter-po">Reset</button>
                                </div>
                            </div>

                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-generate-po" aria-hidden="true" style="display:none;">
        <form action="{{route('purchase_order.generate')}}" method="GET" id="formGeneratePo">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Generate Purchase Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="po_sppg_id">SPPG</label>
                            @if(!empty($isEmployee) && $isEmployee)
                                <input type="hidden" name="sppg_id" id="po_sppg_id" value="{{ optional($sppg->first())->id }}">
                                <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                            @else
                                <select name="sppg_id" id="po_sppg_id" class="form-control" required>
                                    <option value="">-- Pilih SPPG --</option>
                                    @foreach($sppg as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="tanggal_menu_dari">Tanggal Menu Dari</label>
                            <input type="date" name="tanggal_menu_dari" id="tanggal_menu_dari" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="tanggal_menu_sampai">Tanggal Menu Sampai</label>
                            <input type="date" name="tanggal_menu_sampai" id="tanggal_menu_sampai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function formatDateLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function refresh_table() {
        const startDate = $('#filter_po_tanggal_start').val();
        const endDate = $('#filter_po_tanggal_end').val();

        if (startDate && endDate && startDate > endDate) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.' });
            return;
        }

        $('#my-table').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);

        $.get("{!! $tableUrl !!}", {
            filter_sppg_id: $('#filter_po_sppg_id').val(),
            filter_tanggal_start: startDate,
            filter_tanggal_end: endDate
        }, function (data) {
            $('#my-table').html(data);
        });
    }

    $(function () {
        const today = formatDateLocal(new Date());

        $('#tanggal_menu_dari').val(today);
        $('#tanggal_menu_sampai').val(today);
        $('#filter_po_tanggal_start').val(today.slice(0, 8) + '01');
        $('#filter_po_tanggal_end').val(today);

        refresh_table();

        $('#btn-filter-po').on('click', function () {
            refresh_table();
        });

        $('#btn-reset-filter-po').on('click', function () {
            if ($('#filter_po_sppg_id').is('select')) {
                $('#filter_po_sppg_id').val('');
            }
            $('#filter_po_tanggal_start').val('');
            $('#filter_po_tanggal_end').val('');
            refresh_table();
        });

        $('#formGeneratePo').on('submit', function (e) {
            const mulai = $('#tanggal_menu_dari').val();
            const sampai = $('#tanggal_menu_sampai').val();

            if (mulai && sampai && mulai > sampai) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal menu dari tidak boleh lebih besar dari tanggal menu sampai.' });
            }
        });
    });
</script>

