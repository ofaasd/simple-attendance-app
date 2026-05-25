<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Cashflow</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155; /* slate-700 */
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a; /* slate-900 */
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .header .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .header .meta-table td.label {
            width: 100px;
            color: #64748b; /* slate-500 */
            font-weight: 500;
        }

        .header .meta-table td.separator {
            width: 15px;
            color: #64748b;
        }

        .header .meta-table td.value {
            font-weight: bold;
            color: #1e293b; /* slate-800 */
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-table td {
            width: 33.33%;
            padding: 0 8px;
        }

        .summary-table td:first-child {
            padding-left: 0;
        }

        .summary-table td:last-child {
            padding-right: 0;
        }

        .card {
            border-radius: 6px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }

        .card-success {
            background-color: #f0fdf4; /* emerald-50 */
            border-color: #bbf7d0; /* emerald-200 */
        }

        .card-danger {
            background-color: #fff1f2; /* rose-50 */
            border-color: #fecdd3; /* rose-200 */
        }

        .card-info {
            background-color: #f0f9ff; /* sky-50 */
            border-color: #bae6fd; /* sky-200 */
        }

        .card-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b; /* slate-500 */
            margin-bottom: 4px;
            font-weight: bold;
        }

        .card-value {
            font-size: 15px;
            font-weight: bold;
        }

        .text-success {
            color: #16a34a; /* emerald-600 */
        }

        .text-danger {
            color: #e11d48; /* rose-600 */
        }

        .text-info {
            color: #0284c7; /* sky-600 */
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin: 20px 0 10px 0;
            padding-left: 8px;
        }

        .section-title.title-success {
            border-left: 3px solid #16a34a;
        }

        .section-title.title-danger {
            border-left: 3px solid #e11d48;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table th {
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table th.th-success {
            background-color: #10b981; /* emerald-500 */
            color: #ffffff;
        }

        .data-table th.th-danger {
            background-color: #f43f5e; /* rose-500 */
            color: #ffffff;
        }

        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc; /* slate-50 */
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Rekap Cashflow</h1>
        <table class="meta-table">
            <tr>
                <td class="label">SPPG</td>
                <td class="separator">:</td>
                <td class="value">{{ $sppgName }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td class="separator">:</td>
                <td class="value">{{ $periode }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Unduh</td>
                <td class="separator">:</td>
                <td class="value">{{ date('d M Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Box -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="card card-success">
                    <div class="card-label">Total Cash In</div>
                    <div class="card-value text-success">Rp {{ number_format((float) $totalCashIn, 2, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card card-danger">
                    <div class="card-label">Total Cash Out</div>
                    <div class="card-value text-danger">Rp {{ number_format((float) $totalCashOut, 2, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="card card-info">
                    <div class="card-label">Net Cashflow</div>
                    <div class="card-value text-info">Rp {{ number_format((float) $netCash, 2, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Cash In Section -->
    <div class="section-title title-success">CASH IN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="th-success text-center" style="width: 5%;">No.</th>
                <th class="th-success" style="width: 15%;">Tanggal</th>
                <th class="th-success" style="width: 15%;">SPPG</th>
                <th class="th-success" style="width: 20%;">Sumber Dana</th>
                <th class="th-success text-right" style="width: 20%;">Jumlah Dana</th>
                <th class="th-success" style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashIns as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="nowrap">{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $row->sppg->nama ?? '-' }}</td>
                    <td>{{ $row->sumber_dana }}</td>
                    <td class="text-right nowrap">Rp {{ number_format((float) $row->jumlah_dana, 2, ',', '.') }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #64748b; padding: 15px;">Tidak ada data cash in.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Cash Out Section -->
    <div class="section-title title-danger">CASH OUT</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="th-danger text-center" style="width: 5%;">No.</th>
                <th class="th-danger" style="width: 15%;">Tanggal</th>
                <th class="th-danger" style="width: 15%;">SPPG</th>
                <th class="th-danger" style="width: 20%;">Jenis Cash Out</th>
                <th class="th-danger text-right" style="width: 20%;">Nominal</th>
                <th class="th-danger" style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashOuts as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="nowrap">{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $row->sppg->nama ?? '-' }}</td>
                    <td>{{ $row->jenisCashout->nama ?? '-' }}</td>
                    <td class="text-right nowrap">Rp {{ number_format((float) $row->nominal, 2, ',', '.') }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #64748b; padding: 15px;">Tidak ada data cash out.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
