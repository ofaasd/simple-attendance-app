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
                            <li class="breadcrumb-item"><a href="{{ route('item_vendor') }}">Harga Vendor Item</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Form Add Harga Vendor</h3>
                                <a href="{{ route('item_vendor') }}" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                            <div class="card-body">
                                @csrf

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

                                <div class="d-flex align-items-center mb-3">
                                    <button type="button" class="btn btn-secondary btn-sm" id="btn-add-row">+ Tambah Baris</button>
                                    <small class="text-muted ml-2">Tekan <kbd>Alt</kbd>+<kbd>Enter</kbd> untuk menambah baris</small>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('item_vendor') }}" class="btn btn-default">Batal</a>
                                    <button type="button" class="btn btn-primary" id="btn-save-bulk">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<style>
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
    const allItems = {!! $allItemsJson !!};
    const allVendors = {!! $allVendorsJson !!};
    let bulkRowCounter = 0;

    function getTodayDate() {
        const d = new Date();
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }

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

        $tr.find('.row-item-select').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Item --',
            width: '100%',
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

        $tr.find('.row-vendor-select').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Vendor --',
            width: '100%',
        });

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

        $tr.find('.row-item-select').select2('open');
    }

    function reNumberRows() {
        $('#bulk-rows-body tr').each(function (i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    function collectBulkRows() {
        const rows = [];
        let valid = true;

        $('#bulk-rows-body tr').each(function () {
            const itemId = $(this).find('.row-item-select').val();
            const vendorId = $(this).find('.row-vendor-select').val();
            const tanggal = $(this).find('.row-tanggal').val();
            const harga = $(this).find('.row-harga').val();
            const rank = $(this).find('.row-rank').val();

            if (!itemId || !vendorId || !tanggal || harga === '' || !rank) {
                valid = false;
            }

            rows.push({ item_id: itemId, vendor_id: vendorId, tanggal, harga, rank });
        });

        return valid ? rows : null;
    }

    $(function () {
        @if(empty($isEmployee) || !$isEmployee)
        $('#bulk_sppg_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih SPPG --',
            width: '100%',
        });
        @endif

        addBulkRow();

        $('#bulk_sppg_id').on('change', function () {
            $('#bulk-rows-body .row-item-select').val(null).trigger('change');
        });

        $('#btn-add-row').on('click', function () {
            addBulkRow();
        });

        $('#bulk-rows-body').on('click', '.btn-remove-row', function () {
            const $tr = $(this).closest('tr');
            $tr.find('.row-item-select').select2('destroy');
            $tr.find('.row-vendor-select').select2('destroy');
            $tr.remove();
            reNumberRows();
        });

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

        $(document).on('keydown', function (e) {
            if (e.altKey && (e.key === 'Enter' || e.keyCode === 13)) {
                e.preventDefault();
                addBulkRow();
            }
        });

        $('#btn-save-bulk').on('click', function () {
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

            $.ajax({
                url: "{!! url('item-vendor/bulk') !!}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    _token: $('input[name="_token"]').val(),
                    sppg_id: sppgId,
                    rows: rows,
                }),
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data harga vendor item berhasil disimpan.' })
                        .then(function () {
                            window.location.href = "{{ route('item_vendor') }}";
                        });
                },
                error: function (xhr) {
                    let msg = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', html: msg });
                }
            });
        });
    });
</script>
