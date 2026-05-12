<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
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
                        Welcome to Dashboard
                    </div>
                    <div class="card-content">

                    </div>
                </div>
                <div class="card card-outline card-primary col-md-12">
                    <div class="card-header">
                        <h3 class="card-title">List SPPG & PO Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($sppgList as $sppg)
                                @php
                                    $po = $sppg->latestPurchaseOrder;
                                    $distribusi = $latestDistribusi[$sppg->id] ?? null;
                                    $statusText = 'Belum ada PO';
                                    $statusClass = 'badge-secondary';

                                    if ($po) {
                                        $statusText = $po->status_label;

                                        if ((int) $po->status === \App\Models\PurchaseOrder::STATUS_DRAFTED) {
                                            $statusText = 'Draft';
                                            $statusClass = 'badge-secondary';
                                        } elseif ((int) $po->status === \App\Models\PurchaseOrder::STATUS_APPROVED_HEAD) {
                                            $statusText = 'Approved by Kepala';
                                            $statusClass = 'badge-success';
                                        } else {
                                            $statusClass = 'badge-primary';
                                        }
                                    }
                                @endphp
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                    <div class="card h-100 shadow-sm border border-primary">
                                        <div class="card-header p-2">
                                            <strong>{{ $sppg->nama }}</strong>
                                        </div>
                                        <div class="card-body p-2">
                                            <!-- Saldo Saat Ini -->
                                            <div class="small text-muted mb-1">Saldo Saat Ini</div>
                                            <div class="mb-2"><strong class="text-success">Rp {{ number_format((float) $sppg->saldo, 0, ',', '.') }}</strong></div>

                                            @if($po)
                                                <div class="small text-muted mb-1">PO Terbaru</div>
                                                <div><strong>{{ $po->kode_po ?? '-' }}</strong></div>
                                                <div class="small mb-1">Tanggal: {{ optional($po->tanggal_po)->format('d M Y') ?? '-' }}</div>
                                                <div class="small mb-2">Total: Rp {{ number_format((float) $po->total_bayar, 0, ',', '.') }}</div>
                                            @else
                                                <div class="small text-muted mb-2">Belum ada purchase order.</div>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                @if($po)
                                                    <a href="{{ route('purchase_order.show', $po->id) }}" class="btn btn-sm btn-outline-primary">
                                                        Lihat Detail PO
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                        Lihat Detail PO
                                                    </button>
                                                @endif
                                            </div>
                                            @if($distribusi)
                                                <div class="small text-muted mb-1">Distribusi Terbaru</div>
                                                <div><strong>{{ optional($distribusi->menu)->nama ?? '-' }}</strong></div>
                                                <div class="small mb-1">Tanggal: {{ optional($distribusi->tanggal_pengiriman)->format('d M Y') ?? '-' }}</div>
                                                <div class="small mb-2">
                                                    Status:
                                                    @php
                                                        $distStatus = $distribusi->status;
                                                        $distStatusClasses = [
                                                            'on progress' => 'badge-secondary',
                                                            'on delivery' => 'badge-info',
                                                            'done' => 'badge-success',
                                                        ];
                                                        $menuPhoto = $distribusi->foto_menu ?? $distribusi->foto_menu ?? null;
                                                        $suhuPhoto = $distribusi->foto_suhu ?? $distribusi->suhu_foto ?? null;
                                                    @endphp
                                                    <span class="badge {{ $distStatusClasses[$distStatus] ?? 'badge-dark' }}">
                                                        {{ ucfirst($distStatus) }}
                                                    </span>
                                                </div>
                                                <div class="btn-group" role="group" aria-label="Distribusi Actions">
                                                    @if($menuPhoto)
                                                        <a href="{{ Storage::url(trim($menuPhoto, '/')) }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Menu</a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Lihat Menu</button>
                                                    @endif
                                                    @if($suhuPhoto)
                                                        <a href="{{ Storage::url(trim($suhuPhoto, '/')) }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Foto</a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Lihat Foto</button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="small text-muted mb-2">Belum ada distribusi.</div>
                                            @endif

                                            
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">Data SPPG belum tersedia.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="card card-primary col-md-12">
                    <div class="card-header">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-md-6">
                                    Daily Report
                                </div>
                                <div class="col-md-6 text-right">
                                    <input type="date" name="daily_date" class="form-control" id="daily_date" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                    </div>
                    <div class="card-body" id="daily-content">

                    </div>
                </div>
                <div class="card card-info col-md-12">
                    <div class="card-header">
                        <div class="card-title">
                            Weekly Report

                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                    </div>
                    <div class="card-body" id="weekly-content">

                    </div>
                </div>
                <div class="card card-success col-md-12">
                    <div class="card-header">

                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-inline">
                                    <span class="mr-sm-2">Monthly Report</span>
                                    <select name="status" class="form-control mb-2 mr-sm-2" id="status">
                                        <option value="1">On Time</option>
                                        <option value="2">Late</option>
                                        <option value="3">Not Absence</option>
                                    </select>
                                    <select name="month" class="form-control mb-2 mr-sm-2" id="month">
                                        @foreach($month as $key=>$value)
                                        <option value="{{$key}}" {{(date('m') == $key)?"selected":""}}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <select name="year" class="form-control mb-2 mr-sm-2" id="year">
                                        @for($i=date('Y');  $i >= (date('Y')-5); $i--)
                                            <option value="{{$i}}" {{(date('Y') == $i)?"selected":""}}>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                    </button>
                                  </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-body" id="monthly-content">

                    </div>
                </div>
              </div>
            </div>
        </section>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $("#daily_date").change(function(){
                get_daily();
            });
            $("#status").change(function(){
                get_monthly();
            });
            $("#month").change(function(){
                get_monthly();
            });
            $("#year").change(function(){
                get_monthly();
            });
            get_daily();
            get_weekly();
            get_monthly();
        });
        function get_daily(){
            $("#overlay-place").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const tanggal = $("#daily_date").val();
            const url = "{!! url("dashboard/get_daily") !!}";
            $.get(url,{tanggal:tanggal},(data)=>{
                $("#daily-content").html(data);
            });
        }
        function get_weekly(){
            $("#weekly-content").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const url = "{!! url("dashboard/get_weekly") !!}";
            $.get(url,(data)=>{
                $("#weekly-content").html(data);
            });
        }
        function get_monthly(){
            $("#monthly-content").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const url = "{!! url("dashboard/get_monthly") !!}";
            const status = $("#status").val();
            const month = $("#month").val();
            const year = $("#year").val();
            $.get(url,{status:status,month:month,year:year},(data)=>{
                $("#monthly-content").html(data);
            });
        }
    </script>
</x-app-layout>

