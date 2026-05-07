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
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        @if(auth()->user()->hasRole('akuntan'))
                            <a href="{{ route('laporan_bahan_baku.create') }}" class="btn btn-primary">
                                <i class="fas fa-cog mr-1"></i> Generate Laporan
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @php
                            $draftCount = $reports->where('approval_status', \App\Models\LaporanBahanBaku::APPROVAL_DRAFT)->count();
                            $requestedCount = $reports->where('approval_status', \App\Models\LaporanBahanBaku::APPROVAL_REQUESTED)->count();
                            $approvedCount = $reports->whereIn('approval_status', [
                                \App\Models\LaporanBahanBaku::APPROVAL_APPROVED_VERVAL,
                                \App\Models\LaporanBahanBaku::APPROVAL_APPROVED_HEAD,
                            ])->count();
                        @endphp

                        <div class="mb-3 d-flex flex-wrap align-items-center">
                            <span class="mr-2 font-weight-bold">Filter Status:</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-1 btn-filter-status active" data-status="all">
                                Semua <span class="badge badge-light ml-1">{{ $reports->count() }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-1 btn-filter-status" data-status="draft">
                                Draft <span class="badge badge-secondary ml-1">{{ $draftCount }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-1 btn-filter-status" data-status="requested">
                                Requested <span class="badge badge-warning ml-1">{{ $requestedCount }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-1 btn-filter-status" data-status="approved">
                                Approved <span class="badge badge-success ml-1">{{ $approvedCount }}</span>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="laporan-bahan-baku-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width:60px">No</th>
                                        <th>Nama Laporan Bahan Baku</th>
                                        <th style="width:190px">Tanggal Pembuatan</th>
                                        <th style="width:220px">Tanggal Estimasi Pembayaran</th>
                                        <th class="text-center" style="width:170px">Status Approval</th>
                                        <th class="text-right" style="width:170px">Grand Total</th>
                                        <th class="text-center" style="width:280px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $index => $report)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $report->nama_laporan ?: '-' }}</td>
                                            <td>{{ $report->tanggal_laporan ? \Carbon\Carbon::parse($report->tanggal_laporan)->format('d M Y') : '-' }}</td>
                                            <td>{{ $report->estimasi_tanggal_bayar ? \Carbon\Carbon::parse($report->estimasi_tanggal_bayar)->format('d M Y') : '-' }}</td>
                                            @php
                                                $status = (int) $report->approval_status;
                                            @endphp
                                            <td class="text-center" data-order="{{ $status }}">
                                                @php
                                                    $statusLabel = \App\Models\LaporanBahanBaku::approvalLabels()[$status] ?? 'Unknown';
                                                    $statusBadge = match ($status) {
                                                        \App\Models\LaporanBahanBaku::APPROVAL_DRAFT => 'badge-secondary',
                                                        \App\Models\LaporanBahanBaku::APPROVAL_REQUESTED => 'badge-warning',
                                                        \App\Models\LaporanBahanBaku::APPROVAL_APPROVED_VERVAL => 'badge-info',
                                                        \App\Models\LaporanBahanBaku::APPROVAL_APPROVED_HEAD => 'badge-success',
                                                        default => 'badge-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td class="text-right">Rp {{ number_format((float) $report->grand_total, 2, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if(auth()->user()->hasRole('akuntan'))
                                                    @if($status === \App\Models\LaporanBahanBaku::APPROVAL_DRAFT)
                                                        <a href="{{ route('laporan_bahan_baku.edit', $report->id) }}" class="btn btn-sm btn-info" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('laporan_bahan_baku.destroy', $report->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('laporan_bahan_baku.request', $report->id) }}" method="POST" class="d-inline-block form-request-lbb">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Request Approval">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">Menunggu proses approval</span>
                                                    @endif
                                                @elseif(auth()->user()->hasRole('verval') && $status === \App\Models\LaporanBahanBaku::APPROVAL_REQUESTED)
                                                    <form action="{{ route('laporan_bahan_baku.review', ['id' => $report->id, 'stage' => 'verval']) }}" method="POST" class="d-inline-block form-review-lbb" data-stage="verval">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui Verval">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('laporan_bahan_baku.review', ['id' => $report->id, 'stage' => 'verval']) }}" method="POST" class="d-inline-block form-review-lbb-reject" data-stage="verval">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak Verval">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @elseif(auth()->user()->hasRole('head') && $status === \App\Models\LaporanBahanBaku::APPROVAL_APPROVED_VERVAL)
                                                    <form action="{{ route('laporan_bahan_baku.review', ['id' => $report->id, 'stage' => 'head']) }}" method="POST" class="d-inline-block form-review-lbb" data-stage="head">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui Head">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('laporan_bahan_baku.review', ['id' => $report->id, 'stage' => 'head']) }}" method="POST" class="d-inline-block form-review-lbb-reject" data-stage="head">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak Head">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($reports->isEmpty())
                            <div class="text-center text-muted mt-3">Belum ada laporan bahan baku maker.</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
    const laporanTable = $('#laporan-bahan-baku-table').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        pageLength: 10,
        order: [[4, 'asc'], [2, 'desc']],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
            emptyTable: 'Belum ada laporan bahan baku maker.',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }
        }
    });

    function setStatusFilterButtonActive($button) {
        $('.btn-filter-status').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $button.removeClass('btn-outline-secondary').addClass('active btn-primary');
    }

    $('.btn-filter-status').on('click', function () {
        const $btn = $(this);
        const status = $btn.data('status');

        if (status === 'all') {
            laporanTable.column(4).search('', true, false).draw();
        } else if (status === 'draft') {
            laporanTable.column(4).search('^Draft$', true, false).draw();
        } else if (status === 'requested') {
            laporanTable.column(4).search('^Requested$', true, false).draw();
        } else if (status === 'approved') {
            laporanTable.column(4).search('Approved by Verval|Approved by Head', true, false).draw();
        }

        setStatusFilterButtonActive($btn);
    });

    $('.form-request-lbb').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            icon: 'question',
            title: 'Request Approval?',
            text: 'Laporan akan dikirim ke verval dan tidak bisa diedit.',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.form-review-lbb').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            icon: 'question',
            title: 'Setujui laporan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.form-review-lbb-reject').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            icon: 'warning',
            title: 'Tolak laporan?',
            text: 'Status akan kembali ke Draft dan akuntan harus request ulang.',
            showCancelButton: true,
            confirmButtonText: 'Ya, tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
