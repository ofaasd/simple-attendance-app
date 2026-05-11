@if(!empty($errorMessage))
    <div class="alert alert-danger">{{ $errorMessage }}</div>
@else
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Cash In</span>
                    <span class="info-box-number">Rp {{ number_format((float) $totalCashIn, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Cash Out</span>
                    <span class="info-box-number">Rp {{ number_format((float) $totalCashOut, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Net Cashflow</span>
                    <span class="info-box-number">Rp {{ number_format((float) $netCash, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Cash In</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover" id="cashflow-cash-in-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>SPPG</th>
                                <th>Sumber Dana</th>
                                <th>Jumlah Dana</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashIns as $row)
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                                    <td>{{ $row->sppg->nama ?? '-' }}</td>
                                    <td>{{ $row->sumber_dana }}</td>
                                    <td>Rp {{ number_format((float) $row->jumlah_dana, 2, ',', '.') }}</td>
                                    <td>{{ $row->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Cash Out</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover" id="cashflow-cash-out-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>SPPG</th>
                                <th>Jenis Cash Out</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashOuts as $row)
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                                    <td>{{ $row->sppg->nama ?? '-' }}</td>
                                    <td>{{ $row->jenisCashout->nama ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) $row->nominal, 2, ',', '.') }}</td>
                                    <td>{{ $row->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(function() {
        if ($.fn.DataTable.isDataTable('#cashflow-cash-in-table')) {
            $('#cashflow-cash-in-table').DataTable().clear().destroy();
        }
        if ($.fn.DataTable.isDataTable('#cashflow-cash-out-table')) {
            $('#cashflow-cash-out-table').DataTable().clear().destroy();
        }

        $('#cashflow-cash-in-table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [[1, 'desc']],
        });

        $('#cashflow-cash-out-table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [[1, 'desc']],
        });
    });
    </script>
@endif
