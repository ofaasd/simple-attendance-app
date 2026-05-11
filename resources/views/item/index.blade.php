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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-item">+ {{$addButtonLabel ?? 'Add Item'}}</a>
                            <a href="javascript:void(0)" class="btn btn-success ml-2" data-toggle="modal" data-target="#modal-import-item">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-item" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formItem">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="item_id">
                        <div class="row">
                            <div class="col-md-4">
                                @if(!empty($isEmployee) && $isEmployee)
                                    <input type="hidden" name="sppg_id" id="item_sppg_id" value="{{ optional($sppg->first())->id }}">
                                    <div class="form-group">
                                        <label>SPPG</label>
                                        <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="item_sppg_id">SPPG</label>
                                        <select name="sppg_id" id="item_sppg_id" class="form-control" required>
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
                                    <label for="item_nama">Nama Item</label>
                                    <input type="text" name="nama" class="form-control" id="item_nama" placeholder="Masukkan nama item" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_kategori_id">Kategori</label>
                                    <select name="kategori_id" id="item_kategori_id" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($kategori as $row)
                                            <option value="{{$row->id}}" data-sppg-id="{{$row->sppg_id}}">{{$row->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_uom_id">UOM</label>
                                    <select name="uom_id" id="item_uom_id" class="form-control" required>
                                        <option value="">-- Pilih UOM --</option>
                                        @foreach($uom as $row)
                                            <option value="{{$row->id}}" data-sppg-id="{{$row->sppg_id}}">{{$row->nama}}</option>
                                        @endforeach
                                    </select>
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

    <div class="modal fade" id="modal-import-item" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formImportItem" enctype="multipart/form-data">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-import-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Import Item dari Excel</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="import_kategori_id">Kategori</label>
                            <select name="kategori_id" id="import_kategori_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $row)
                                    <option value="{{$row->id}}">{{$row->nama}}{{ optional($row->sppg)->nama ? ' ('.$row->sppg->nama.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="import_file">File Excel</label>
                            <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Format kolom: Nama Item dan UOM.</small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function filterItemMasterBySppg() {
        const sppgId = $("#item_sppg_id").val();

        $('#item_kategori_id option').each(function () {
            const optSppg = $(this).data('sppg-id');
            if (!$(this).val()) {
                $(this).show();
                return;
            }
            $(this).toggle(String(optSppg) === String(sppgId));
        });

        $('#item_uom_id option').each(function () {
            const optSppg = $(this).data('sppg-id');
            if (!$(this).val()) {
                $(this).show();
                return;
            }
            $(this).toggle(String(optSppg) === String(sppgId));
        });

        const kategoriValue = $('#item_kategori_id').val();
        if (kategoriValue && $('#item_kategori_id option:selected').is(':hidden')) {
            $('#item_kategori_id').val('');
        }

        const uomValue = $('#item_uom_id').val();
        if (uomValue && $('#item_uom_id option:selected').is(':hidden')) {
            $('#item_uom_id').val('');
        }

    }

    function refresh_table() {
        $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const url_table = "{!! $tableUrl ?? url('item/get_table') !!}";
        $.get(url_table, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        refresh_table();
        filterItemMasterBySppg();

        $('#item_sppg_id').on('change', function() {
            filterItemMasterBySppg();
        });

        $(".btn-create").click(function () {
            $('#formItem').trigger("reset");
            $("#item_id").val('');
            $("#modal-item .modal-title").text('Add New {{$addButtonLabel ?? "Item"}}');
            filterItemMasterBySppg();
        });

        $('#modal-import-item').on('shown.bs.modal', function () {
            $('#formImportItem').trigger('reset');
            $('#import_kategori_id').val('');
        });

        $("#formItem").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('item') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data item berhasil disimpan.' });
                    $("#modal-item").modal("hide");
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

        $('#formImportItem').submit(function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = "{!! route('item.import') !!}";

            $('#overlay-import-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    let msg = response.message || 'Import selesai.';
                    if (response.failed_rows && response.failed_rows.length) {
                        const preview = response.failed_rows
                            .map(function (row) {
                                return 'Baris ' + row.row + ': ' + row.reason;
                            })
                            .join('\n');
                        msg += '\n\nDetail gagal (maks 20):\n' + preview;
                    }

                    Swal.fire({ icon: 'success', title: 'Berhasil', text: msg });
                    $('#overlay-import-place').html('');
                    $('#modal-import-item').modal('hide');
                    refresh_table();
                },
                error: function (xhr) {
                    $('#overlay-import-place').html('');
                    let msg = 'Terjadi kesalahan saat import.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });
    });
</script>

