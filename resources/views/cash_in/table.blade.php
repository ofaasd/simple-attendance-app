<table class="table table-bordered table-hover" id="cash-in-table">
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
                <td>{{++$no}}</td>
                <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                <td>{{ $row->sppg->nama ?? '-' }}</td>
                <td>{{ $row->sumber_dana }}</td>
                <td>Rp {{ number_format((float) $row->jumlah_dana, 2, ',', '.') }}</td>
                <td>{{ $row->keterangan ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
$('#cash-in-table').DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
    order: [[1, 'desc']]
});
</script>