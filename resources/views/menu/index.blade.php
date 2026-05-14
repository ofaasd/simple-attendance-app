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
                            <a href="{{ route('item_menu.create') }}" class="btn btn-primary">+ {{$addButtonLabel ?? 'Add Menu'}}</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="filter_sppg_id">SPPG</label>
                                        @if(!empty($isEmployee) && $isEmployee)
                                            <input type="hidden" id="filter_sppg_id" value="{{ optional($sppg->first())->id }}">
                                            <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                        @else
                                            <select id="filter_sppg_id" class="form-control">
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
                                        <label for="filter_tanggal_start">Filter Tanggal Dari</label>
                                        <input type="date" id="filter_tanggal_start" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="filter_tanggal_end">Filter Tanggal Sampai</label>
                                        <input type="date" id="filter_tanggal_end" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-info mr-2" id="btn-filter-menu">Terapkan Filter</button>
                                    <button type="button" class="btn btn-default" id="btn-reset-filter-menu">Reset</button>
                                </div>
                            </div>
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-menu" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formMenu">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Menu</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="menu_id">
                        <div class="row">
                            
                            <div class="col-md-4">
                                @if(!empty($isEmployee) && $isEmployee)
                                    <input type="hidden" name="sppg_id" id="menu_sppg_id" value="{{ optional($sppg->first())->id }}">
                                    <div class="form-group">
                                        <label>SPPG</label>
                                        <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="menu_sppg_id">SPPG</label>
                                        <select name="sppg_id" id="menu_sppg_id" class="form-control" required>
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
                                    <label for="menu_tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" id="menu_tanggal" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="menu_nama">Nama Menu</label>
                                    <input type="text" name="nama" class="form-control" id="menu_nama" placeholder="Masukkan nama menu" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="border rounded p-3 mb-3 bg-light" id="menu-copy-section">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Copy Dari Menu Sebelumnya</h6>
                                        <small class="text-muted">Pilih tanggal sumber dan menu yang ingin dicopy</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-md-0">
                                                <label for="menu_copy_source_tanggal">Copy Dari Tanggal</label>
                                                <input type="date" class="form-control" id="menu_copy_source_tanggal">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group mb-md-0">
                                                <label for="menu_copy_source_id">Pilih Menu Sumber</label>
                                                <select id="menu_copy_source_id" class="form-control">
                                                    <option value="">-- Pilih menu dari tanggal sumber --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-warning btn-block" id="btn-copy-to-add-form">Copy ke Form Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="menu_item_ids">Items</label>
                                    <select name="item_ids[]" id="menu_item_ids" class="form-control" multiple required>
                                        @foreach($items as $row)
                                            <option value="{{$row->id}}" data-sppg-id="{{$row->sppg_id}}">{{$row->nama}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih item yang menjadi bagian dari menu.</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="mb-0">Detail Menu</label>
                                        <button type="button" class="btn btn-success btn-sm" id="btn-add-detail-menu">+ Tambah Detail</button>
                                    </div>
                                    <div id="detail-menu-wrapper" class="mt-2"></div>
                                    <small class="text-muted">Isi detail menu sesuai kebutuhan, bisa tambah atau kurangi baris.</small>
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
    /* Keep Select2 dropdown above modal content and make spacing consistent. */
    #modal-menu .select2-container {
        width: 100% !important;
    }

    #modal-menu .select2-container--bootstrap4 .select2-selection {
        min-height: calc(2.25rem + 2px);
    }

    #modal-menu .select2-container--bootstrap4 .select2-selection--multiple {
        min-height: calc(2.25rem + 2px);
        padding-top: 2px;
    }

    #modal-menu .select2-container--bootstrap4 .select2-dropdown {
        z-index: 2060;
    }
</style>

<script>
    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return year + '-' + month + '-' + day;
    }

    function getFirstDayOfCurrentMonth() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        return formatLocalDate(firstDay);
    }

    function normalizeDateInputValue(value) {
        if (!value) {
            return '';
        }

        return String(value).slice(0, 10);
    }

    // function applyDateBounds() {
    //     const minDate = getFirstDayOfCurrentMonth();
    //     const maxDate = getTodayDate();

    //     $('#menu_tanggal').attr('min', minDate).attr('max', maxDate);
    //     $('#menu_copy_source_tanggal').attr('min', minDate).attr('max', maxDate);
    //     $('#filter_tanggal_start').attr('min', minDate).attr('max', maxDate);
    //     $('#filter_tanggal_end').attr('min', minDate).attr('max', maxDate);
    // }

    function initializeListDateFilters() {
        $('#filter_tanggal_start').val(getFirstDayOfCurrentMonth());
        $('#filter_tanggal_end').val(getTodayDate());
        $('#filter_sppg_id').val(getSppg());
    }

    function initializeMenuSelect2() {
        if (typeof $.fn.select2 !== 'function') {
            return;
        }

        const sourceSelect = $('#menu_copy_source_id');
        if (sourceSelect.length) {
            if (sourceSelect.hasClass('select2-hidden-accessible')) {
                sourceSelect.select2('destroy');
            }

            sourceSelect.select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Pilih menu dari tanggal sumber',
                dropdownParent: $('#modal-menu'),
                allowClear: true
            });
        }

        const itemSelect = $('#menu_item_ids');
        if (itemSelect.length) {
            if (itemSelect.hasClass('select2-hidden-accessible')) {
                itemSelect.select2('destroy');
            }

            itemSelect.select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Pilih item',
                dropdownParent: $('#modal-menu'),
                allowClear: true
            });
        }
    }

    function getTodayDate() {
        return formatLocalDate(new Date());
    }
    function getSppg() {
        return $('#filter_sppg_id').val() || $('#menu_sppg_id').val() || '';
    }

    function buildDetailMenuRow(value = '') {
        return `
            <div class="input-group mb-2 detail-menu-row">
                <input type="text" name="detail_menu_names[]" class="form-control" maxlength="255" placeholder="Masukkan nama detail menu" value="${value}" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-remove-detail-menu">-</button>
                </div>
            </div>
        `;
    }

    function setDetailMenuInputs(detailMenuNames = []) {
        const wrapper = $('#detail-menu-wrapper');
        wrapper.html('');

        const normalized = (detailMenuNames || []).filter(function (name) {
            return String(name || '').trim() !== '';
        });

        if (!normalized.length) {
            wrapper.append(buildDetailMenuRow(''));
            return;
        }

        normalized.forEach(function (name) {
            wrapper.append(buildDetailMenuRow(name));
        });
    }

    function initializeDetailMenuInputs() {
        setDetailMenuInputs([]);
    }

    function initializeMenuDate() {
        $('#menu_tanggal').val(getTodayDate());
    }

    function initializeCopySourceDate() {
        const prev = new Date();
        prev.setDate(prev.getDate() - 1);
        const minDate = getFirstDayOfCurrentMonth();
        const prevValue = formatLocalDate(prev);
        $('#menu_copy_source_tanggal').val(prevValue < minDate ? minDate : prevValue);
    }

    function resetCopySourceOptions() {
        $('#menu_copy_source_id').html('<option value="">-- Pilih menu dari tanggal sumber --</option>');
        $('#menu_copy_source_id').trigger('change.select2');
    }

    function loadCopySourceMenuOptions() {
        const sppgId = $('#menu_sppg_id').val();
        const sourceTanggal = $('#menu_copy_source_tanggal').val();

        resetCopySourceOptions();

        if (!sppgId || !sourceTanggal) {
            return;
        }

        const url = "{!! url('menu-item/by-date') !!}";
        $.get(url, { sppg_id: sppgId, tanggal: sourceTanggal }, function (res) {
            const menus = (res && res.menus) ? res.menus : [];
            (menus || []).forEach(function (menu) {
                $('#menu_copy_source_id').append('<option value="' + menu.id + '">' + menu.nama + '</option>');
            });
            $('#menu_copy_source_id').trigger('change.select2');
        });
    }

    function copySelectedMenuToAddForm() {
        const selectedMenuId = $('#menu_copy_source_id').val();
        if (!selectedMenuId) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih menu sumber terlebih dahulu.' });
            return;
        }

        const targetTanggal = $('#menu_tanggal').val();
        const url = "{!! url('menu-item') !!}/" + selectedMenuId + '/edit';

        $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        $.get(url, function (data) {
            const row = data.menu;
            const itemIds = data.item_ids || [];
            const detailMenuNames = data.detail_menu_names || [];

            $('#menu_id').val('');
            $('#menu_nama').val(row.nama);
            $('#menu_item_ids option').prop('selected', false);
            (itemIds || []).forEach(function (itemId) {
                $('#menu_item_ids option[value="' + itemId + '"]').prop('selected', true);
            });
            $('#menu_item_ids').trigger('change');
            setDetailMenuInputs(detailMenuNames);

            // Keep target date from Add form, do not override with source menu date.
            if (targetTanggal) {
                $('#menu_tanggal').val(targetTanggal);
            }

            $('#overlay-place').html('');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Form berhasil diisi dari menu sumber.' });
        }).fail(function (xhr) {
            $('#overlay-place').html('');
            let msg = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
        });
    }

    function filterMenuMasterBySppg() {
        const sppgId = $("#menu_sppg_id").val();

        $('#menu_item_ids option').each(function () {
            const optSppg = $(this).data('sppg-id');
            $(this).toggle(String(optSppg) === String(sppgId));
        });

        $('#menu_item_ids option:selected').each(function () {
            if ($(this).is(':hidden')) {
                $(this).prop('selected', false);
            }
        });

        $('#menu_item_ids').trigger('change.select2');
    }

    function refresh_table() {
        const startDate = $('#filter_tanggal_start').val();
        const endDate = $('#filter_tanggal_end').val();
        const sppgId = $('#filter_sppg_id').val();

        if (startDate && endDate && startDate > endDate) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.' });
            return;
        }

        $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const url_table = "{!! $tableUrl ?? url('menu-item/get_table') !!}";
        const filters = {
            filter_tanggal_start: startDate,
            filter_tanggal_end: endDate,
            filter_sppg_id: sppgId
        };
        $.get(url_table, filters, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        initializeMenuSelect2();
        // applyDateBounds();
        initializeListDateFilters();
        refresh_table();
        filterMenuMasterBySppg();
        initializeDetailMenuInputs();
        initializeMenuDate();
        initializeCopySourceDate();
        loadCopySourceMenuOptions();

        $('#btn-filter-menu').on('click', function () {
            refresh_table();
        });

        $('#btn-reset-filter-menu').on('click', function () {
            initializeListDateFilters();
            refresh_table();
        });

        $(document).on('click', '#btn-add-detail-menu', function () {
            $('#detail-menu-wrapper').append(buildDetailMenuRow(''));
        });

        $(document).on('click', '.btn-remove-detail-menu', function () {
            const rows = $('#detail-menu-wrapper .detail-menu-row');
            if (rows.length <= 1) {
                rows.find('input[name="detail_menu_names[]"]').val('');
                return;
            }

            $(this).closest('.detail-menu-row').remove();
        });

        $('#menu_sppg_id').on('change', function() {
            filterMenuMasterBySppg();
            loadCopySourceMenuOptions();
        });

        $('#menu_copy_source_tanggal').on('change', function() {
            loadCopySourceMenuOptions();
        });

        $('#btn-copy-to-add-form').on('click', function () {
            copySelectedMenuToAddForm();
        });

        $("#formMenu").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('menu-item') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data menu berhasil disimpan.' });
                    $("#modal-menu").modal("hide");
                    $("#overlay-place").html('');
                    refresh_table();
                },
                error: function (xhr) {
                    $("#overlay-place").html('');
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

