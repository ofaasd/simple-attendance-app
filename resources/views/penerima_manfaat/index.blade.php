<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Daftar Penerima Manfaat</h3>
                        @if(!auth()->user()->hasRole('admin yayasan'))
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#modal-import">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </button>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover" id="pm-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Alamat</th>
                                    <th>No Telp</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penerimaManfaat as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->nama }}</td>
                                    <td>{{ $row->kategori }}</td>
                                    <td>{{ $row->alamat }}</td>
                                    <td>{{ $row->no_telp }}</td>
                                    <td>{{ $row->pic }}</td>
                                    <td>
                                        @if(!auth()->user()->hasRole('admin yayasan'))
                                        <button class="btn btn-warning btn-sm btn-edit" 
                                            data-id="{{ $row->id }}"
                                            data-nama="{{ $row->nama }}"
                                            data-kategori="{{ $row->kategori }}"
                                            data-alamat="{{ $row->alamat }}"
                                            data-telp="{{ $row->no_telp }}"
                                            data-pic="{{ $row->pic }}"
                                            data-toggle="modal" 
                                            data-target="#modal-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('penerima_manfaat.destroy', $row->id) }}" method="POST" class="d-inline-block form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('penerima_manfaat.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Import Data Penerima Manfaat</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                            Pastikan format file Excel Anda memiliki urutan kolom (Header di baris 1, Data dimulai dari baris 2) sebagai berikut:
                            <ol class="mb-0">
                                <li>Nama</li>
                                <li>Kategori (Sekolah, B3, dll)</li>
                                <li>Alamat</li>
                                <li>No. Telp</li>
                                <li>PIC</li>
                            </ol>
                        </div>
                        <div class="form-group">
                            <label>Pilih File Excel/CSV <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="modal-add">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('penerima_manfaat.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Penerima Manfaat</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
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
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="B3">B3</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No Telp</label>
                            <input type="text" name="no_telp" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>PIC</label>
                            <input type="text" name="pic" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="form-edit">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Penerima Manfaat</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" id="edit-kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="B3">B3</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" id="edit-alamat" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No Telp</label>
                            <input type="text" name="no_telp" id="edit-telp" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>PIC</label>
                            <input type="text" name="pic" id="edit-pic" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
$(document).ready(function() {
    $('#pm-table').DataTable({
        responsive: true,
        autoWidth: false,
    });

    $('.btn-edit').on('click', function() {
        const id = $(this).data('id');
        $('#form-edit').attr('action', '{{ url('penerima-manfaat') }}/' + id);
        $('#edit-nama').val($(this).data('nama'));
        $('#edit-kategori').val($(this).data('kategori'));
        $('#edit-alamat').val($(this).data('alamat'));
        $('#edit-telp').val($(this).data('telp'));
        $('#edit-pic').val($(this).data('pic'));
    });

    $('.form-delete').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

