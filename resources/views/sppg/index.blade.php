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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-sppg">+ Add SPPG</a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Add/Edit SPPG -->
    <div class="modal fade" id="modal-sppg" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" method="post" id="formSppg">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New SPPG</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="sppg_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sppg_nama">Nama SPPG</label>
                                    <input type="text" name="nama" class="form-control" id="sppg_nama" placeholder="Masukkan nama SPPG" required>
                                </div>
                                <div class="form-group">
                                    <label for="sppg_user_id">Penanggung Jawab (User)</label>
                                    <select name="user_id" id="sppg_user_id" class="form-control" required>
                                        <option value="">-- Pilih User --</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}} ({{$user->nik ?? $user->email}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sppg_approver_user_ids">Approver (Akuntan/Verval/Head)</label>
                                    <select name="approver_user_ids[]" id="sppg_approver_user_ids" class="form-control" multiple>
                                        @foreach($approverUsers as $user)
                                            <option value="{{$user->id}}">{{$user->name}} ({{$user->email}})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Tahan Ctrl/Cmd untuk memilih lebih dari satu approver.</small>
                                </div>
                                <div class="form-group">
                                    <label for="sppg_location">Link Lokasi (URL)</label>
                                    <input type="url" name="location" class="form-control" id="sppg_location" placeholder="https://maps.google.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sppg_alamat">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="sppg_alamat" rows="3" placeholder="Masukkan alamat lengkap" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="sppg_lat">Latitude</label>
                                    <input type="number" step="any" name="lat" class="form-control" id="sppg_lat" placeholder="-6.2000000">
                                </div>
                                <div class="form-group">
                                    <label for="sppg_lng">Longitude</label>
                                    <input type="number" step="any" name="lng" class="form-control" id="sppg_lng" placeholder="106.8000000">
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
</div>
</x-app-layout>
<script>
    function refresh_table() {
        $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const url_table = "{!! url('sppg/get_table') !!}";
        $.get(url_table, function (data) {
            $("#my-table").html(data);
        });
    }

    $(function () {
        refresh_table();

        $(".btn-create").click(function () {
            $('#formSppg').trigger("reset");
            $("#sppg_id").val('');
            $("#sppg_approver_user_ids").val([]).trigger('change');
            $(".modal-title").text('Add New SPPG');
        });

        $("#formSppg").submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('sppg') !!}";
            $("#overlay-place").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data SPPG berhasil disimpan.',
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                    $("#modal-sppg").modal("hide");
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

