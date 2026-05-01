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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-item-vendor">+ Add Harga Vendor</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="filter_item_vendor_item_id">Item</label>
                                        <select id="filter_item_vendor_item_id" class="form-control">
                                            <option value="">Semua Item</option>
                                            @foreach($items as $row)
                                                <option value="{{$row->id}}">{{$row->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="filter_item_vendor_vendor_id">Vendor</label>
                                        <select id="filter_item_vendor_vendor_id" class="form-control">
                                            <option value="">Semua Vendor</option>
                                            @foreach($vendors as $row)
                                                <option value="{{$row->id}}">{{$row->kode_vendor}} - {{$row->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label for="filter_item_vendor_tanggal_start">Tanggal Dari</label>
                                        <input type="date" id="filter_item_vendor_tanggal_start" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label for="filter_item_vendor_tanggal_end">Tanggal Sampai</label>
                                        <input type="date" id="filter_item_vendor_tanggal_end" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="button" class="btn btn-info mr-2" id="btn-filter-item-vendor">Terapkan Filter</button>
                                    <button type="button" class="btn btn-default" id="btn-reset-filter-item-vendor">Reset</button>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 mb-3">
                                Record paling baru untuk kombinasi Item dan Vendor dianggap sebagai harga terbaru.
                            </div>
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-item-vendor" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formItemVendor">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add Harga Vendor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="item_vendor_id">
                        <div class="row">
                            <div class="col-md-4">
                                @if(!empty($isEmployee) && $isEmployee)
                                    <input type="hidden" name="sppg_id" id="item_vendor_sppg_id" value="{{ optional($sppg->first())->id }}">
                                    <div class="form-group">
                                        <label>SPPG</label>
                                        <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="item_vendor_sppg_id">SPPG</label>
                                        <select name="sppg_id" id="item_vendor_sppg_id" class="form-control" required>
                                            <option value="">-- Pilih SPPG --</option>
                                            @foreach($sppg as $row)
                                                <option value="{{$row->id}}">{{$row->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_vendor_item_id">Item</label>
                                    <select name="item_id" id="item_vendor_item_id" class="form-control" required>
                                        <option value="">-- Pilih Item --</option>
                                        @foreach($items as $row)
                                            <option value="{{$row->id}}" data-sppg-id="{{$row->sppg_id}}" data-uom-name="{{ optional($row->uom)->nama }}">{{$row->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_vendor_vendor_id">Vendor</label>
                                    <select name="vendor_id" id="item_vendor_vendor_id" class="form-control" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach($vendors as $row)
                                            <option value="{{$row->id}}">{{$row->kode_vendor}} - {{$row->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_vendor_tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" id="item_vendor_tanggal" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_vendor_harga">Harga</label>
                                    <input type="number" name="harga" class="form-control" id="item_vendor_harga" min="0" step="0.01" placeholder="Masukkan harga" required>
                                    <small class="text-muted" id="item_vendor_uom_hint">Harga per: -</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_vendor_rank">Rank Prioritas</label>
                                    <input type="number" name="rank" class="form-control" id="item_vendor_rank" min="1" step="1" value="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<style>
    #modal-item-vendor .select2-container { width: 100% !important; }
    #modal-item-vendor .select2-container--bootstrap4 .select2-selection--single { height: calc(2.25rem + 2px); }
    #modal-item-vendor .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: calc(2.25rem + 2px); }
    #modal-item-vendor .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { height: calc(2.25rem + 2px); }
    #modal-item-vendor .select2-container--bootstrap4 .select2-dropdown { z-index: 9999; }
</style>

<script>
    function getTodayDate() {
        const date = new Date();
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function filterItemsBySppg() {
        const sppgId = $('#item_vendor_sppg_id').val();

        // Clear item selection if current item belongs to a different SPPG
        const selectedOpt = $('#item_vendor_item_id option:selected');
        const selectedSppgId = selectedOpt.data('sppg-id');
        if (selectedOpt.val() && sppgId && String(selectedSppgId) !== String(sppgId)) {
            $('#item_vendor_item_id').val(null).trigger('change');
        }

        updateUomHint();
    }

    function updateUomHint() {
        const selected = $('#item_vendor_item_id option:selected');
        const uomName = selected.data('uom-name');
        if (uomName) {
            $('#item_vendor_uom_hint').text('Harga per: ' + uomName);
            return;
        }

        $('#item_vendor_uom_hint').text('Harga per: -');
    }

    function refresh_table() {
        $('#my-table').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const urlTable = "{!! $tableUrl ?? url('item-vendor/get_table') !!}";
        const filters = {
            filter_item_id: $('#filter_item_vendor_item_id').val(),
            filter_vendor_id: $('#filter_item_vendor_vendor_id').val(),
            filter_tanggal_start: $('#filter_item_vendor_tanggal_start').val(),
            filter_tanggal_end: $('#filter_item_vendor_tanggal_end').val()
        };

        if (filters.filter_tanggal_start && filters.filter_tanggal_end && filters.filter_tanggal_start > filters.filter_tanggal_end) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.' });
            $('#my-table').html('');
            return;
        }

        $.get(urlTable, filters, function (data) {
            $('#my-table').html(data);
        });
    }

    $(function () {
        refresh_table();
        filterItemsBySppg();

        // Select2 — filter area
        $('#filter_item_vendor_item_id').select2({
            theme: 'bootstrap4',
            allowClear: true,
            placeholder: 'Semua Item',
            width: '100%',
        });
        $('#filter_item_vendor_vendor_id').select2({
            theme: 'bootstrap4',
            allowClear: true,
            placeholder: 'Semua Vendor',
            width: '100%',
        });

        // Select2 — modal item (with SPPG matcher)
        $('#item_vendor_item_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Item --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
            matcher: function (params, data) {
                if (!data.id) return data;
                const sppgId = $('#item_vendor_sppg_id').val();
                const optSppgId = $(data.element).data('sppg-id');
                if (sppgId && String(optSppgId) !== String(sppgId)) return null;
                if (!params.term || params.term.trim() === '') return data;
                if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) return data;
                return null;
            },
        });

        // Select2 — modal vendor
        $('#item_vendor_vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Vendor --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
        });

        $('#item_vendor_sppg_id').on('change', function () {
            filterItemsBySppg();
        });

        $('#item_vendor_item_id').on('change', function () {
            updateUomHint();
        });

        $('#btn-filter-item-vendor').on('click', function () {
            refresh_table();
        });

        $('#btn-reset-filter-item-vendor').on('click', function () {
            $('#filter_item_vendor_item_id').val(null).trigger('change');
            $('#filter_item_vendor_vendor_id').val(null).trigger('change');
            $('#filter_item_vendor_tanggal_start').val('');
            $('#filter_item_vendor_tanggal_end').val('');
            refresh_table();
        });

        $('.btn-create').click(function () {
            $('#formItemVendor').trigger('reset');
            $('#item_vendor_id').val('');
            $('.modal-title').text('Add Harga Vendor');
            $('#item_vendor_tanggal').val(getTodayDate());
            $('#item_vendor_rank').val(1);
            $('#item_vendor_item_id').val(null).trigger('change');
            $('#item_vendor_vendor_id').val(null).trigger('change');
            filterItemsBySppg();
            updateUomHint();
        });

        $('#formItemVendor').submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const id = $('#item_vendor_id').val();
            const url = id ? "{!! url('item-vendor') !!}/" + id : "{!! url('item-vendor') !!}";
            const method = id ? 'PUT' : 'POST';
            $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function () {
                    const successText = id ? 'Data harga vendor item berhasil diperbarui.' : 'Data harga vendor item berhasil disimpan.';
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: successText });
                    $('#modal-item-vendor').modal('hide');
                    $('#overlay-place').html('');
                    refresh_table();
                },
                error: function (xhr) {
                    $('#overlay-place').html('');
                    let msg = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });
    });
</script>
