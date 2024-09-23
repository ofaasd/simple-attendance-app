<table class="table" id="example2">
    <thead>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>NIK</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($user as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$row->name}}</td>
                <td>{{$row->nik}}</td>
                <td>{{$row->email}}</td>
                <td>
                    @foreach($row->getRoleNames() as $value)
                        <span class="badge badge-primary text-uppercase">{{$value ?? ''}}</span>
                    @endforeach
                </td>
                <td>
                    <div class="btn-group">
                        <a href="javascript:void(0)"class="btn btn-primary btn-sm btn-edit" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-add"><i class="fas fa-pencil-alt"></i></a>
                        <a href="javascript:void(0)"class="btn btn-info btn-sm btn-password" data-id="{{$row->id}}" data-toggle="modal" data-target="#modal-password"><i class="fas fa-key"></i></a>
                        <a href="javascript:void(0)"class="btn btn-danger btn-sm delete-record" data-id="{{$row->id}}"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
$('#example2').DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
$(".btn-edit").click(function(){
    $('#formUser').trigger("reset");
    $("#overlay-place").html(`<div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>`);
    $(".password-field").hide();
    const id = $(this).data('id');
    const url_table = "{!! url('user') !!}/" + id +'/edit';
    $.get(url_table, function (data){
        Object.keys(data[0]).forEach(key => {
            //console.log(key);
            $('#' + key)
                .val(data[0][key])
                .trigger('change');
        });
        Object.keys(data[1]).forEach(key => {
            //console.log(key);
            $('#role')
                .val(data[1][key])
                .trigger('change');
        });
        $("#overlay-place").html('');
    });
});
$(".btn-password").click(function(){
    $(".password-field").show();
    $('#formUser').trigger("reset");
    $("#overlay-place2").html(`<div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>`);
    const id = $(this).data('id');
    const url_table = "{!! url('user') !!}/" + id +'/edit';
    $.get(url_table, function (data){
        $("#id_password").val(id);
        $("#overlay-place").html('');
    });
});



$(document).on('click', '.delete-record', function () {
    const id = $(this).data('id');
    // sweetalert for confirmation of delete
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    }).then(function (result) {
    if (result.isConfirmed) {
        // delete the data
        $.ajax({
        type: 'DELETE',
        url: "{!! url('user') !!}/" + id ,
        data:{
            'id': id,
            '_token': '{{ csrf_token() }}',
        },
        success: function success() {
            // success sweetalert
            Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The Record has been deleted!',
            customClass: {
                confirmButton: 'btn btn-success'
            }
            });
            refresh_table();
        },
        error: function error(_error) {
            console.log(_error);
            Swal.fire({
                title: 'Error',
                text: 'The record is not deleted!',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-success'
                }
            });
        }
        });


    } else {
        Swal.fire({
        title: 'Cancelled',
        text: 'The record is not deleted!',
        icon: 'error',
        customClass: {
            confirmButton: 'btn btn-success'
        }
        });
    }
    });
});
</script>
