<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$title}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                    </ol>
                </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <section class="content">
            <div class="container-fluid">
              <!-- Small boxes (Stat box) -->
              <div class="row">
                <div class="card col-md-12">
                    <div class="card-header">
                        <a href="javascript:void(0)" class="btn btn-primary btn-create" data-toggle="modal" data-target="#modal-add">+ Add Employee</a>

                    <div class="card-body">
                        <div id="my-table">

                        </div>
                    </div>
                </div>
              </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="modal-add" aria-hidden="true" style="display: none;">
        <form action="javascript:void(0)" method="post" id="formUser">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-place">

                    </div>
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Employee</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group name-field">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter name" required>
                        </div>
                        <div class="form-group name-field">
                            <label for="nik">NIK</label>
                            <input type="text" name="nik" class="form-control" id="nik" placeholder="Enter NIK | ex : 33712367123" required>
                        </div>
                        <div class="form-group email-field">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                        </div>
                        <div class="form-group password-field">
                            <label for="passwords">Password</label>
                            <input type="password" name="password" class="form-control" id="passwords" placeholder="Enter Password">
                        </div>
                        <div class="form-group  role-field">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control">
                                @foreach($role as $row)
                                <option value="{{$row->name}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-save">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
        </form>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-password" aria-hidden="true" style="display: none;">
        <form action="javascript:void(0)" method="post" id="formPassword">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="overlay-place">

                    </div>
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id_password">
                        <div class="form-group password-field">
                            <label for="passwords">New Password</label>
                            <input type="password" name="password" class="form-control" id="passwords" placeholder="Enter Password">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-save">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
        </form>
        <!-- /.modal-dialog -->
    </div>
</div>
</x-app-layout>
<script>
  function refresh_table(){
    $("#my-table").html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
    const url_table = "{!! url('user/get_table') !!}";
    $.get(url_table, function (data){
        $("#my-table").html(data);
    });
  }
  $(function () {
    refresh_table();
    $('#example2').DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $("#formUser").submit(function(e){
        e.preventDefault();
        const data = $(this).serialize();
        const url = "{!! url('user') !!}";
        $("#overlay-place").html(`<div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>`);
        $.ajax({
            url : url,
            method : "POST",
            data :data,
            success : function(data){
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully',
                    text: 'Saved Successfully.',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
                $("#modal-add").modal("hide");
                $(this).trigger("reset");
                $("#overlay-place").html('');
                refresh_table();
            }
        });
    });
    $("#formPassword").submit(function(e){
        e.preventDefault();
        const data = $(this).serialize();
        const url = "{!! url('user') !!}";
        $("#overlay-place").html(`<div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>`);
        $.ajax({
            url : url,
            method : "POST",
            data :data,
            success : function(data){
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully',
                    text: 'Saved Successfully.',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
                $("#modal-password").modal("hide");
                $(this).trigger("reset");
                $("#overlay-place").html('');
                refresh_table();
            }
        });
    });
    $(".btn-create").click(function(){
        $('#formUser').trigger("reset");
        $("#id").val('');
        $(".password-field").show();
    });
  });

</script>
