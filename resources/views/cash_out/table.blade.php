<table class="table table-bordered table-hover" id="cash-out-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>SPPG</th>
            <th>Jenis Cash Out</th>
            <th>Nominal</th>
            <th>Keterangan</th>
            <th>File</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cashOuts as $row)
            <tr>
                <td>{{++$no}}</td>
                <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                <td>{{ $row->sppg->nama ?? '-' }}</td>
                <td>{{ $row->jenisCashout->nama ?? '-' }}</td>
                <td>Rp {{ number_format((float) $row->nominal, 2, ',', '.') }}</td>
                <td>{{ $row->keterangan ?? '-' }}</td>
                <td>
                    @if($row->file_pendukung)
                        <a href="{{ asset('storage/' . $row->file_pendukung) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
$('#cash-out-table').DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
    order: [[1, 'desc']]
});
</script>

