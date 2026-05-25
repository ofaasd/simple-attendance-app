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
                            @if(!auth()->user()->hasRole('admin yayasan'))
                            <a href="{{ route('item_vendor.create') }}" class="btn btn-primary">+ Add Harga Vendor</a>
                            @endif
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
        <form action="javascript:void(0)" method="post" id="formItemVendor" novalidate>
            <div class="modal-dialog modal-xl">
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

                        {{-- ======================== ADD MODE (multi-row) ======================== --}}
                        <div id="section-add-mode">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    @if(!empty($isEmployee) && $isEmployee)
                                        <input type="hidden" id="bulk_sppg_id" value="{{ optional($sppg->first())->id }}">
                                        <div class="form-group mb-0">
                                            <label>SPPG</label>
                                            <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                        </div>
                                    @else
                                        <div class="form-group mb-0">
                                            <label for="bulk_sppg_id">SPPG</label>
                                            <select id="bulk_sppg_id" class="form-control" required>
                                                <option value="">-- Pilih SPPG --</option>
                                                @foreach($sppg as $row)
                                                    <option value="{{$row->id}}">{{$row->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-2" id="bulk-rows-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width:40px">#</th>
                                            <th>Item</th>
                                            <th>Vendor</th>
                                            <th style="width:150px">Tanggal</th>
                                            <th style="width:140px">Harga</th>
                                            <th style="width:80px">Rank</th>
                                            <th style="width:90px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulk-rows-body"></tbody>
                                </table>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-secondary btn-sm" id="btn-add-row">
                                    + Tambah Baris
                                </button>
                                <small class="text-muted ml-2">Tekan <kbd>Alt</kbd>+<kbd>Enter</kbd> untuk menambah baris</small>
                            </div>
                        </div>

                        {{-- ======================== EDIT MODE (single-row) ======================== --}}
                        <div id="section-edit-mode" style="display:none">
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
    #bulk-rows-table td { vertical-align: middle; padding: 4px 6px; }
    #bulk-rows-table .select2-container { min-width: 160px; }
</style>

@php
    $allItemsJson = json_encode($items->map(function ($i) {
        return [
            'id'      => $i->id,
            'nama'    => $i->nama,
            'sppg_id' => $i->sppg_id,
            'uom'     => optional($i->uom)->nama ?? '',
        ];
    }));
    $allVendorsJson = json_encode($vendors->map(function ($v) {
        return ['id' => $v->id, 'text' => $v->kode_vendor . ' - ' . $v->nama];
    }));
@endphp

<script>
    // ── Data passed from PHP ──────────────────────────────────────────────────
    const allItems = {!! $allItemsJson !!};
    const allVendors = {!! $allVendorsJson !!};

    // ── Helpers ───────────────────────────────────────────────────────────────
    function getTodayDate() {
        const d = new Date();
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }

    function filterItemsBySppg() {
        const sppgId = $('#item_vendor_sppg_id').val();
        const selectedOpt = $('#item_vendor_item_id option:selected');
        if (selectedOpt.val() && sppgId && String(selectedOpt.data('sppg-id')) !== String(sppgId)) {
            $('#item_vendor_item_id').val(null).trigger('change');
        }
        updateUomHint();
    }

    function updateUomHint() {
        const uomName = $('#item_vendor_item_id option:selected').data('uom-name');
        $('#item_vendor_uom_hint').text('Harga per: ' + (uomName || '-'));
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

        if (filters.filter_tanggal_start && filters.filter_tanggal_end
                && filters.filter_tanggal_start > filters.filter_tanggal_end) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.' });
            $('#my-table').html('');
            return;
        }

        $.get(urlTable, filters, function (data) {
            $('#my-table').html(data);
        });
    }

    // ── Bulk (add-mode) rows ──────────────────────────────────────────────────
    let bulkRowCounter = 0;

    function buildItemOptions() {
        let html = '<option value="">-- Pilih Item --</option>';
        allItems.forEach(function (item) {
            html += `<option value="${item.id}" data-sppg-id="${item.sppg_id}" data-uom-name="${item.uom}">${item.nama}</option>`;
        });
        return html;
    }

    function buildVendorOptions() {
        let html = '<option value="">-- Pilih Vendor --</option>';
        allVendors.forEach(function (v) {
            html += `<option value="${v.id}">${v.text}</option>`;
        });
        return html;
    }

    function addBulkRow(prefill = null) {
        const idx = bulkRowCounter++;
        const today = getTodayDate();
        const row = `
            <tr data-row="${idx}">
                <td class="text-center row-number"></td>
                <td style="min-width:200px">
                    <select class="form-control form-control-sm row-item-select" data-row="${idx}" required>
                        ${buildItemOptions()}
                    </select>
                    <small class="text-muted row-uom-hint" style="font-size:11px">Harga per: -</small>
                </td>
                <td style="min-width:200px">
                    <select class="form-control form-control-sm row-vendor-select" data-row="${idx}" required>
                        ${buildVendorOptions()}
                    </select>
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm row-tanggal" value="${today}" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm row-harga" min="0" step="0.01" placeholder="0" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm row-rank" min="1" step="1" value="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-info btn-sm btn-duplicate-row" tabindex="-1" title="Duplikat baris">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" tabindex="-1" title="Hapus baris">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

        const $tr = $(row).appendTo('#bulk-rows-body');
        reNumberRows();

        // Init Select2 for item select (with SPPG matcher)
        $tr.find('.row-item-select').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Item --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
            matcher: function (params, data) {
                if (!data.id) return data;
                const sppgId = $('#bulk_sppg_id').val();
                const optSppgId = $(data.element).data('sppg-id');
                if (sppgId && String(optSppgId) !== String(sppgId)) return null;
                if (!params.term || params.term.trim() === '') return data;
                if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) return data;
                return null;
            },
        });

        // Init Select2 for vendor select
        $tr.find('.row-vendor-select').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Vendor --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
        });

        // Update UOM hint when item changes
        $tr.find('.row-item-select').on('change', function () {
            const uom = $(this).find('option:selected').data('uom-name');
            $(this).closest('tr').find('.row-uom-hint').text('Harga per: ' + (uom || '-'));
        });

        if (prefill) {
            $tr.find('.row-item-select').val(prefill.item_id || '').trigger('change');
            $tr.find('.row-vendor-select').val(prefill.vendor_id || '').trigger('change');
            $tr.find('.row-tanggal').val(prefill.tanggal || today);
            $tr.find('.row-harga').val(prefill.harga ?? '');
            $tr.find('.row-rank').val(prefill.rank || 1);
            return;
        }

        // Focus the item select of the new row (open Select2)
        $tr.find('.row-item-select').select2('open');
    }

    function reNumberRows() {
        $('#bulk-rows-body tr').each(function (i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    function clearBulkRows() {
        // Destroy Select2 instances before removing
        $('#bulk-rows-body .row-item-select').select2('destroy');
        $('#bulk-rows-body .row-vendor-select').select2('destroy');
        $('#bulk-rows-body').empty();
        bulkRowCounter = 0;
    }

    function collectBulkRows() {
        const rows = [];
        let valid = true;

        $('#bulk-rows-body tr').each(function () {
            const itemId   = $(this).find('.row-item-select').val();
            const vendorId = $(this).find('.row-vendor-select').val();
            const tanggal  = $(this).find('.row-tanggal').val();
            const harga    = $(this).find('.row-harga').val();
            const rank     = $(this).find('.row-rank').val();

            if (!itemId || !vendorId || !tanggal || harga === '' || !rank) {
                valid = false;
            }

            rows.push({ item_id: itemId, vendor_id: vendorId, tanggal, harga, rank });
        });

        return valid ? rows : null;
    }

    // ── Bootstrap & events ────────────────────────────────────────────────────
    $(function () {
        refresh_table();
        filterItemsBySppg();

        // Select2 — filter area
        $('#filter_item_vendor_item_id').select2({
            theme: 'bootstrap4', allowClear: true, placeholder: 'Semua Item', width: '100%',
        });
        $('#filter_item_vendor_vendor_id').select2({
            theme: 'bootstrap4', allowClear: true, placeholder: 'Semua Vendor', width: '100%',
        });

        // Select2 — edit-mode: item select
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

        // Select2 — edit-mode: vendor select
        $('#item_vendor_vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Vendor --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
        });

        // Select2 — add-mode: SPPG (only for non-employee)
        @if(empty($isEmployee) || !$isEmployee)
        $('#bulk_sppg_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih SPPG --',
            width: '100%',
            dropdownParent: $('#modal-item-vendor'),
        });
        @endif

        // Edit-mode SPPG change
        $('#item_vendor_sppg_id').on('change', function () { filterItemsBySppg(); });
        $('#item_vendor_item_id').on('change', function () { updateUomHint(); });

        // Add-mode SPPG change — re-open/refresh item selects in existing rows
        $('#bulk_sppg_id').on('change', function () {
            $('#bulk-rows-body .row-item-select').val(null).trigger('change');
        });

        // Add row button
        $('#btn-add-row').on('click', function () { addBulkRow(); });

        // Remove row button (delegated)
        $('#bulk-rows-body').on('click', '.btn-remove-row', function () {
            const $tr = $(this).closest('tr');
            $tr.find('.row-item-select').select2('destroy');
            $tr.find('.row-vendor-select').select2('destroy');
            $tr.remove();
            reNumberRows();
        });

        // Duplicate row button (delegated)
        $('#bulk-rows-body').on('click', '.btn-duplicate-row', function () {
            const $tr = $(this).closest('tr');
            const rowData = {
                item_id: $tr.find('.row-item-select').val(),
                vendor_id: $tr.find('.row-vendor-select').val(),
                tanggal: $tr.find('.row-tanggal').val(),
                harga: $tr.find('.row-harga').val(),
                rank: $tr.find('.row-rank').val(),
            };

            addBulkRow(rowData);
        });

        // Keyboard shortcut: Alt+Enter → add row (only in add mode)
        $('#modal-item-vendor').on('keydown', function (e) {
            if (e.altKey && (e.key === 'Enter' || e.keyCode === 13)) {
                if ($('#section-add-mode').is(':visible')) {
                    e.preventDefault();
                    addBulkRow();
                }
            }
        });

        // Filter buttons
        $('#btn-filter-item-vendor').on('click', function () { refresh_table(); });
        $('#btn-reset-filter-item-vendor').on('click', function () {
            $('#filter_item_vendor_item_id').val(null).trigger('change');
            $('#filter_item_vendor_vendor_id').val(null).trigger('change');
            $('#filter_item_vendor_tanggal_start').val('');
            $('#filter_item_vendor_tanggal_end').val('');
            refresh_table();
        });

        // Open modal for EDIT (called from table buttons)
        $(document).on('click', '.btn-edit-item-vendor', function () {
            const id = $(this).data('id');
            $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $('#modal-item-vendor').modal('show');

            $.get("{!! url('item-vendor') !!}/" + id + "/edit", function (res) {
                const iv = res.item_vendor;
                $('#item_vendor_id').val(iv.id);
                $('.modal-title').text('Edit Harga Vendor');
                $('#section-add-mode').hide();
                $('#section-edit-mode').show();

                // Populate edit fields
                $('#item_vendor_sppg_id').val(iv.sppg_id).trigger('change');
                setTimeout(function () {
                    $('#item_vendor_item_id').val(iv.item_id).trigger('change');
                    $('#item_vendor_vendor_id').val(iv.vendor_id).trigger('change');
                    $('#item_vendor_tanggal').val(iv.tanggal);
                    $('#item_vendor_harga').val(iv.harga);
                    $('#item_vendor_rank').val(iv.rank);
                    updateUomHint();
                    $('#overlay-place').html('');
                }, 100);
            }).fail(function () {
                $('#overlay-place').html('');
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memuat data.' });
            });
        });

        // Form submit
        $('#formItemVendor').on('submit', function (e) {
            e.preventDefault();
            const id = $('#item_vendor_id').val();

            if (!id) {
                // ── ADD MODE: bulk submit ──
                const sppgId = $('#bulk_sppg_id').val();
                if (!sppgId) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih SPPG terlebih dahulu.' });
                    return;
                }
                if ($('#bulk-rows-body tr').length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tambahkan minimal satu baris data.' });
                    return;
                }
                const rows = collectBulkRows();
                if (!rows) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Lengkapi semua kolom pada setiap baris.' });
                    return;
                }

                const payload = {
                    _token: $('input[name="_token"]').val(),
                    sppg_id: sppgId,
                    rows: rows,
                };

                $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
                $.ajax({
                    url: "{!! url('item-vendor/bulk') !!}",
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data harga vendor item berhasil disimpan.' });
                        $('#modal-item-vendor').modal('hide');
                        $('#overlay-place').html('');
                        refresh_table();
                    },
                    error: function (xhr) {
                        $('#overlay-place').html('');
                        let msg = 'Terjadi kesalahan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire({ icon: 'error', title: 'Gagal', html: msg });
                    }
                });
            } else {
                // ── EDIT MODE: single record update ──
                const data = $('#section-edit-mode').find('select, input').serialize()
                    + '&id=' + id
                    + '&_token=' + $('input[name="_token"]').val()
                    + '&_method=PUT';

                $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
                $.ajax({
                    url: "{!! url('item-vendor') !!}/" + id,
                    method: 'POST',
                    data: data,
                    success: function () {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data harga vendor item berhasil diperbarui.' });
                        $('#modal-item-vendor').modal('hide');
                        $('#overlay-place').html('');
                        refresh_table();
                    },
                    error: function (xhr) {
                        $('#overlay-place').html('');
                        let msg = 'Terjadi kesalahan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                    }
                });
            }
        });
    });
</script>

