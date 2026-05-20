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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-uom">+ Add Satuan Barang</a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-uom" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formUom">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Satuan Barang</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="uom_id">
                        @if(!empty($isEmployee) && $isEmployee)
                            <input type="hidden" name="sppg_id" id="uom_sppg_id" value="{{ optional($sppg->first())->id }}">
                            <div class="form-group">
                                <label>SPPG</label>
                                <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="uom_sppg_id">SPPG</label>
                                <select name="sppg_id" id="uom_sppg_id" class="form-control" required>
                                    <option value="">-- Pilih SPPG --</option>
                                    @foreach($sppg as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="uom_nama">Nama Satuan Barang</label>
                            <input type="text" name="nama" class="form-control" id="uom_nama" placeholder="Contoh : KG, DRG, dll" required>
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

<script>
    function refresh_table() {
        $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const url_table = "{!! url('uom/get_table') !!}";
        $.get(url_table, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        refresh_table();

        $(".btn-create").click(function () {
            $('#formUom').trigger("reset");
            $("#uom_id").val('');
            $(".modal-title").text('Add New Satuan Barang');
        });

        $("#formUom").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('uom') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data Satuan Barang berhasil disimpan.' });
                    $("#modal-uom").modal("hide");
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

