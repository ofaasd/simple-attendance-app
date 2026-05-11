<table class="table table-bordered table-hover" id="distribusi-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Menu</th>
            <th>SPPG</th>
            <th>Waktu Pengiriman</th>
            <th>Waktu Diterima</th>
            <th>Status</th>
            <th>Jumlah</th>
            <th>Penerima Manfaat</th>
            <th>Foto Menu</th>
            <th>Foto Suhu</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($distribusi as $row)
        <tr>
            <td>{{ ++$no }}</td>
            <td>{{ $row->menu->nama ?? '-' }}</td>
            <td>{{ $row->menu->sppg->nama ?? '-' }}</td>
            <td>{{ optional($row->tanggal_pengiriman)->format('d M Y H:i') ?? '-' }}</td>
            <td>{{ optional($row->tanggal_diterima)->format('d M Y H:i') ?? '-' }}</td>
            <td>
                @if($row->status == 'on progress')
                    <span class="badge badge-info">On Progress</span>
                @elseif($row->status == 'on delivery')
                    <span class="badge badge-warning">On Delivery</span>
                @elseif($row->status == 'done')
                    <span class="badge badge-success">Done</span>
                @else
                    <span class="badge badge-secondary">{{ $row->status }}</span>
                @endif
            </td>
            <td>{{ $row->jumlah }}</td>
            <td>
                @if($row->distribusiDetails->count() > 0)
                    <ul class="pl-3 mb-0">
                        @foreach($row->distribusiDetails as $detail)
                            @php
                                $pmKategori = $detail->penerimaManfaat->kategori ?? 'Lainnya';
                                $pmTotal = 0;
                                if ($pmKategori == 'Sekolah') {
                                    $pmTotal = $detail->jml_kecil + $detail->jml_besar + $detail->jml_orcil + $detail->jml_orbes_sekolah;
                                } elseif ($pmKategori == 'B3') {
                                    $pmTotal = $detail->jml_bumil + $detail->jml_busui + $detail->jml_balita + $detail->jml_orbes_b3;
                                } else {
                                    $pmTotal = $detail->jml_lainnya;
                                }
                            @endphp
                            <li>{{ $detail->penerimaManfaat->nama ?? '-' }} ({{ $pmTotal }})</li>
                        @endforeach
                    </ul>
                @else
                    -
                @endif
            </td>
            <td class="text-center">
                @if($row->foto_menu)
                    <a href="{{ asset('storage/' . $row->foto_menu) }}" target="_blank">
                        <img src="{{ asset('storage/' . $row->foto_menu) }}" alt="Foto Menu" class="img-thumbnail" style="max-height: 60px;">
                    </a>
                @else
                    -
                @endif
            </td>
            <td class="text-center">
                @if($row->foto_suhu)
                    <a href="{{ asset('storage/' . $row->foto_suhu) }}" target="_blank">
                        <img src="{{ asset('storage/' . $row->foto_suhu) }}" alt="Foto Suhu" class="img-thumbnail" style="max-height: 60px;">
                    </a>
                @else
                    -
                @endif
            </td>
            <td>
                <a href="{{ route('distribusi_menu.edit', $row->id) }}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                <form action="{{ route('distribusi_menu.destroy', $row->id) }}" method="POST" class="d-inline-block form-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm mb-1"><i class="fas fa-trash"></i> Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
$('#distribusi-table').DataTable({
    responsive: true,
    autoWidth: false,
});
</script>

