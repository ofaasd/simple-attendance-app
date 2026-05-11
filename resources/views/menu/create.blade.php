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
                            <li class="breadcrumb-item"><a href="{{ route('item_menu') }}">Menu</a></li>
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
                                <h3 class="card-title mb-0">Form Add Menu</h3>
                                <a href="{{ route('item_menu') }}" class="btn btn-default btn-sm">Kembali</a>
                            </div>
                            <div class="card-body">
                                <form action="javascript:void(0)" method="post" id="formMenuCreate">
                                    @csrf
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
                                                <label for="menu_item_ids">Bahan baku / Items</label>
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

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('item_menu') }}" class="btn btn-default">Batal</a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return year + '-' + month + '-' + day;
    }

    function getTodayDate() {
        return formatLocalDate(new Date());
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
                allowClear: true
            });
        }
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

        $.get("{!! url('menu-item/by-date') !!}", { sppg_id: sppgId, tanggal: sourceTanggal }, function (res) {
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

        $.get(url, function (data) {
            const row = data.menu;
            const itemIds = data.item_ids || [];
            const detailMenuNames = data.detail_menu_names || [];

            $('#menu_nama').val(row.nama);
            $('#menu_item_ids option').prop('selected', false);
            (itemIds || []).forEach(function (itemId) {
                $('#menu_item_ids option[value="' + itemId + '"]').prop('selected', true);
            });
            $('#menu_item_ids').trigger('change');
            setDetailMenuInputs(detailMenuNames);

            if (targetTanggal) {
                $('#menu_tanggal').val(targetTanggal);
            }

            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Form berhasil diisi dari menu sumber.' });
        }).fail(function (xhr) {
            let msg = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
        });
    }

    function filterMenuMasterBySppg() {
        const sppgId = $('#menu_sppg_id').val();

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

    $(function () {
        initializeMenuSelect2();
        filterMenuMasterBySppg();
        initializeDetailMenuInputs();
        initializeMenuDate();
        initializeCopySourceDate();
        loadCopySourceMenuOptions();

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

        $('#menu_sppg_id').on('change', function () {
            filterMenuMasterBySppg();
            loadCopySourceMenuOptions();
        });

        $('#menu_copy_source_tanggal').on('change', function () {
            loadCopySourceMenuOptions();
        });

        $('#btn-copy-to-add-form').on('click', function () {
            copySelectedMenuToAddForm();
        });

        $('#formMenuCreate').on('submit', function (e) {
            e.preventDefault();
            const data = $(this).serialize();

            $.ajax({
                url: "{!! url('menu-item') !!}",
                method: 'POST',
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data menu berhasil disimpan.' })
                        .then(function () {
                            window.location.href = "{{ route('item_menu') }}";
                        });
                },
                error: function (xhr) {
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

