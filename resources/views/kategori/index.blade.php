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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-kategori">+ Add Kategori</a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-kategori" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formKategori">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Kategori</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="kategori_id">
                        @if(!empty($isEmployee) && $isEmployee)
                            <input type="hidden" name="sppg_id" id="kategori_sppg_id" value="{{ optional($sppg->first())->id }}">
                            <div class="form-group">
                                <label>SPPG</label>
                                <input type="text" class="form-control" value="{{ optional($sppg->first())->nama ?? 'SPPG belum di-assign' }}" readonly>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="kategori_sppg_id">SPPG</label>
                                <select name="sppg_id" id="kategori_sppg_id" class="form-control" required>
                                    <option value="">-- Pilih SPPG --</option>
                                    @foreach($sppg as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="kategori_nama">Nama Kategori</label>
                            <input type="text" name="nama" class="form-control" id="kategori_nama" placeholder="Masukkan nama kategori" required>
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
        const url_table = "{!! url('kategori/get_table') !!}";
        $.get(url_table, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        refresh_table();

        $(".btn-create").click(function () {
            $('#formKategori').trigger("reset");
            $("#kategori_id").val('');
            $(".modal-title").text('Add New Kategori');
        });

        $("#formKategori").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('kategori') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data kategori berhasil disimpan.' });
                    $("#modal-kategori").modal("hide");
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

