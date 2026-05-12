<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard Employee</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Rp {{ number_format($currentBalance, 0, ',', '.') }}</h3>
                                <p>Saldo Saat Ini</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <a href="{{ route('sppg') }}" class="small-box-footer">Detail Saldo <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $attendanceHistory->count() }}</h3>
                                <p>Riwayat Absensi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <a href="{{ route('attendance') }}" class="small-box-footer">Lihat Absensi <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $completedDistribusiCount }}</h3>
                                <p>Distribusi Selesai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <a href="{{ route('distribusi_menu.index') }}" class="small-box-footer">Lihat Distribusi <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $completedPoCount }}</h3>
                                <p>PO Selesai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-check"></i>
                            </div>
                            <a href="{{ route('purchase_order') }}" class="small-box-footer">Lihat PO <i class="fas fa-arrow-circle-right"></i></a>
                            <a href="{{ route('distribusi_menu.index') }}" class="small-box-footer">Distribusi Menu Terbaru <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Riwayat Absensi</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Start</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($attendanceHistory as $presence)
                                                <tr>
                                                    <td>{{ optional($presence->day)->format('d M Y') }}</td>
                                                    <td>
                                                        @if($presence->start_late === 0)
                                                            <span class="badge badge-success">On Time</span>
                                                        @else
                                                            <span class="badge badge-danger">Late</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $presence->start ? gmdate('H:i', $presence->start) : '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada riwayat absensi.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">5 Menu Terbaru</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Menu</th>
                                                <th>SPPG</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentDistribusi as $distribusi)
                                                <tr>
                                                    <td>{{ optional($distribusi->menu)->nama ?? '—' }}</td>
                                                    <td>{{ optional(optional($distribusi->menu)->sppg)->nama ?? '—' }}</td>
                                                    <td>
                                                        @php
                                                            $status = $distribusi->status;
                                                            $statusClasses = [
                                                                'on progress' => 'badge-secondary',
                                                                'on delivery' => 'badge-info',
                                                                'done' => 'badge-success',
                                                            ];
                                                        @endphp
                                                        <span class="badge {{ $statusClasses[$status] ?? 'badge-dark' }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada distribusi menu.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">5 PO Terbaru</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>PO</th>
                                                <th>SPPG</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentPurchaseOrders as $po)
                                                <tr>
                                                    <td>{{ $po->kode_po }}</td>
                                                    <td>{{ optional($po->sppg)->nama ?? '—' }}</td>
                                                    <td><span class="badge {{ $po->status_badge_class }}">{{ $po->status_label }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada purchase order.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

