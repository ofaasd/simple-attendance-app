<table class="table" id="example2">
    <thead>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <div class="btn-group">
                        @if(!auth()->user()->hasRole('admin yayasan'))
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit" data-id="{{ $role->id }}" data-toggle="modal" data-target="#modal-add"><i class="fas fa-pencil-alt"></i></a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-record" data-id="{{ $role->id }}"><i class="fas fa-trash"></i></a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('#example2').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('.btn-edit').click(function () {
        const id = $(this).data('id');
        const url = "{!! url('user/role') !!}/" + id + '/edit';
        $('#overlay-place').html(`<div class="overlay"><i class="fas fa-2x fa-sync fa-spin"></i></div>`);
        $.get(url, function (data) {
            $('#id').val(data.id);
            $('#name').val(data.name).trigger('change');
            $('#overlay-place').html('');
        });
    });

    $(document).on('click', '.delete-record', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: "{!! url('user/role') !!}/" + id,
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'The role has been deleted!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        refresh_table();
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'The role is not deleted!',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                });
            }
        });
    });
</script>

