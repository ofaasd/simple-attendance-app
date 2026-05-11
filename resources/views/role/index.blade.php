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
                            <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-add">+ Add Role</a>
                        </div>
                        <div class="card-body">
                            <div id="my-table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-add" aria-hidden="true" style="display: none;">
        <form action="javascript:void(0)" method="post" id="formRole">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-place"></div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Role</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter role name" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-save">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function refresh_table() {
        $('#my-table').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        const url_table = "{!! url('user/role/get_table') !!}";
        $.get(url_table, function (data) {
            $('#my-table').html(data);
        });
    }

    $(function () {
        refresh_table();

        $('#formRole').submit(function (e) {
            e.preventDefault();
            const data = $(this).serialize();
            const url = "{!! url('user/role') !!}";
            $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successfully',
                        text: 'Saved Successfully.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                    $('#modal-add').modal('hide');
                    $('#formRole')[0].reset();
                    $('#overlay-place').html('');
                    refresh_table();
                },
                error: function (response) {
                    $('#overlay-place').html('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.responseJSON?.message || 'Could not save role.',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            });
        });

        $('.btn-create').click(function () {
            $('#formRole').trigger('reset');
            $('#id').val('');
        });
    });
</script>

