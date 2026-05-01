<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Purchase Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('purchase_order')}}">Purchase Order</a></li>
                            <li class="breadcrumb-item active">Edit Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{$errors->first()}}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{session('error')}}
                    </div>
                @endif

                <form method="POST" action="{{route('purchase_order.update', $purchaseOrder->id)}}" id="formPurchaseOrderEdit">
                    @csrf
                    @method('PUT')

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Informasi Purchase Order</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Kode PO:</strong> {{$purchaseOrder->kode_po}}</div>
                                <div class="col-md-4"><strong>SPPG:</strong> {{$purchaseOrder->sppg->nama ?? '-'}}</div>
                                <div class="col-md-4"><strong>Status:</strong> {{$purchaseOrder->status_label}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="tanggal_menu_dari">Tanggal Menu Dari</label>
                                    <input type="date" name="tanggal_menu_dari" id="tanggal_menu_dari" class="form-control" value="{{old('tanggal_menu_dari', optional($purchaseOrder->tanggal_menu_dari)->format('Y-m-d'))}}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="tanggal_menu_sampai">Tanggal Menu Sampai</label>
                                    <input type="date" name="tanggal_menu_sampai" id="tanggal_menu_sampai" class="form-control" value="{{old('tanggal_menu_sampai', optional($purchaseOrder->tanggal_menu_sampai)->format('Y-m-d'))}}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Daftar Item Purchase Order</h3>
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-custom-item" data-toggle="modal" data-target="#modal-custom-item">
                                + Tambah Item Custom
                            </button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered mb-0" id="po-edit-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">No.</th>
                                        <th>Item</th>
                                        <th style="width: 180px">Qty</th>
                                        <th style="width: 280px">Vendor</th>
                                        <th style="width: 180px">Harga Satuan</th>
                                        <th style="width: 200px">Subtotal</th>
                                        <th style="width: 80px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->details->where('item_id', '!=', null) as $idx => $detail)
                                        @php
                                            $options = $vendorOptionsByItem[$detail->item_id] ?? [];
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                {{$detail->item->nama ?? '-'}}
                                                <small class="text-muted d-block">Satuan: {{optional($detail->item->uom)->nama ?? '-'}}</small>
                                                <input type="hidden" name="detail_ids[]" value="{{$detail->id}}">
                                            </td>
                                            <td>
                                                <input type="number" name="qtys[]" class="form-control po-qty" min="0.01" step="0.01" value="{{old('qtys.'.$idx, (float) $detail->qty)}}" required>
                                            </td>
                                            <td>
                                                <select name="vendor_ids[]" class="form-control po-vendor" required>
                                                    <option value="">-- Pilih Vendor --</option>
                                                    @foreach($options as $opt)
                                                        <option value="{{$opt['vendor_id']}}" data-harga="{{$opt['harga']}}" {{(int) old('vendor_ids.'.$idx, $detail->vendor_id) === (int) $opt['vendor_id'] ? 'selected' : ''}}>{{$opt['vendor_label']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="po-harga">Rp 0,00</td>
                                            <td class="po-subtotal">Rp 0,00</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tbody id="custom-items-tbody">
                                    @foreach($purchaseOrder->details->where('item_id', null) as $idx => $detail)
                                        <tr class="custom-item-row">
                                            <td class="row-number">{{ $purchaseOrder->details->where('item_id', '!=', null)->count() + $loop->iteration }}</td>
                                            <td>
                                                {{$detail->custom_item_name}}<br>
                                                <small class="text-muted">Satuan: {{$detail->item_satuan}}</small>
                                                <input type="hidden" name="custom_item_names[]" value="{{$detail->custom_item_name}}">
                                                <input type="hidden" name="custom_item_satuan[]" value="{{$detail->item_satuan}}">
                                                <input type="hidden" name="custom_item_uom_ids[]" value="{{$detail->custom_uom_id}}">
                                                <input type="hidden" name="custom_item_vendor_ids[]" value="{{$detail->vendor_id}}">
                                                <input type="hidden" class="custom-item-harga" value="{{(float) $detail->harga}}">
                                            </td>
                                            <td>
                                                <input type="number" name="custom_item_qtys[]" class="form-control po-qty" min="0.01" step="0.01" value="{{old('custom_item_qtys.'.$loop->index, (float) $detail->qty)}}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" value="{{$detail->vendor->kode_vendor}} - {{$detail->vendor->nama}}" readonly>
                                            </td>
                                            <td class="po-harga">Rp 0,00</td>
                                            <td class="po-subtotal">Rp 0,00</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-custom-item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Bayar</th>
                                        <th id="grand-total">Rp 0,00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{route('purchase_order')}}" class="btn btn-default">Kembali</a>
                            <button type="submit" class="btn btn-primary">Update Purchase Order</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-custom-item" aria-hidden="true" style="display:none;">
        <form action="javascript:void(0)" id="formAddCustomItem">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Item Custom</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="custom_item_nama">Nama Item</label>
                            <input type="text" class="form-control" id="custom_item_nama" placeholder="Masukkan nama item" required>
                        </div>
                        <div class="form-group">
                            <label for="custom_item_satuan">Satuan</label>
                            <select class="form-control" id="custom_item_satuan" required>
                                <option value="">-- Pilih Satuan --</option>
                                @foreach($uomOptions as $uom)
                                    <option value="{{$uom->id}}">{{$uom->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="custom_item_vendor_id">Vendor</label>
                            <select class="form-control" id="custom_item_vendor_id" required>
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($customVendors as $vendor)
                                    <option value="{{$vendor->id}}">{{$vendor->kode_vendor}} - {{$vendor->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="custom_item_harga">Harga Satuan</label>
                            <input type="number" class="form-control" id="custom_item_harga" min="0" step="0.01" placeholder="Masukkan harga" required>
                        </div>
                        <div class="form-group">
                            <label for="custom_item_qty">Qty</label>
                            <input type="number" class="form-control" id="custom_item_qty" min="0.01" step="0.01" placeholder="Masukkan qty" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function recalculatePurchaseOrderEdit() {
        let grandTotal = 0;

        $('#po-edit-table tbody tr').each(function () {
            const qty = parseFloat($(this).find('.po-qty').val()) || 0;
            const harga = parseFloat($(this).find('.po-vendor option:selected').data('harga')) || 0;
            const subtotal = qty * harga;

            grandTotal += subtotal;

            $(this).find('.po-harga').text(formatRupiah(harga));
            $(this).find('.po-subtotal').text(formatRupiah(subtotal));
        });

        $('#custom-items-tbody tr').each(function () {
            const qty = parseFloat($(this).find('.po-qty').val()) || 0;
            const harga = parseFloat($(this).find('.custom-item-harga').val()) || 0;
            const subtotal = qty * harga;

            grandTotal += subtotal;

            $(this).find('.po-harga').text(formatRupiah(harga));
            $(this).find('.po-subtotal').text(formatRupiah(subtotal));
        });

        $('#grand-total').text(formatRupiah(grandTotal));
    }

    function buildCustomItemRow(idx, nama, uomId, uomLabel, vendorId, vendorLabel, harga, qty) {
        return `
            <tr class="custom-item-row">
                <td class="row-number"></td>
                <td>
                    ${nama}<br>
                    <small class="text-muted">Satuan: ${uomLabel}</small>
                    <input type="hidden" name="custom_item_names[]" value="${nama}">
                    <input type="hidden" name="custom_item_satuan[]" value="${uomLabel}">
                    <input type="hidden" name="custom_item_uom_ids[]" value="${uomId}">
                    <input type="hidden" name="custom_item_vendor_ids[]" value="${vendorId}">
                    <input type="hidden" class="custom-item-harga" value="${harga}">
                </td>
                <td>
                    <input type="number" name="custom_item_qtys[]" class="form-control po-qty" min="0.01" step="0.01" value="${qty}" required>
                </td>
                <td>
                    <input type="text" class="form-control" value="${vendorLabel}" readonly>
                </td>
                <td class="po-harga">Rp 0,00</td>
                <td class="po-subtotal">Rp 0,00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-custom-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function updateRowNumbers() {
        let counter = 1;
        $('#po-edit-table tbody tr').each(function () {
            $(this).find('td:first').text(counter++);
        });
        $('#custom-items-tbody tr').each(function () {
            $(this).find('.row-number').text(counter++);
        });
    }

    $(function () {
        recalculatePurchaseOrderEdit();
        updateRowNumbers();

        $(document).on('change keyup', '.po-qty, .po-vendor', function () {
            recalculatePurchaseOrderEdit();
        });

        $(document).on('change keyup', '.custom-item-row .po-qty', function () {
            recalculatePurchaseOrderEdit();
        });

        $('#btn-add-custom-item').on('click', function () {
            $('#formAddCustomItem').trigger('reset');
        });

        $('#formAddCustomItem').on('submit', function (e) {
            e.preventDefault();
            const nama = $('#custom_item_nama').val();
            const uomId = $('#custom_item_satuan').val();
            const uomLabel = $('#custom_item_satuan option:selected').text();
            const vendorId = $('#custom_item_vendor_id').val();
            const harga = parseFloat($('#custom_item_harga').val());
            const qty = parseFloat($('#custom_item_qty').val());
            const vendorLabel = $('#custom_item_vendor_id option:selected').text();

            if (!nama || !uomId || !vendorId || !harga || !qty) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Semua field harus diisi.' });
                return;
            }

            const rowHtml = buildCustomItemRow(0, nama, uomId, uomLabel, vendorId, vendorLabel, harga, qty);
            $('#custom-items-tbody').append(rowHtml);
            recalculatePurchaseOrderEdit();
            updateRowNumbers();
            $('#modal-custom-item').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item custom berhasil ditambahkan.', timer: 1200, showConfirmButton: false });
        });

        $(document).on('click', '.btn-remove-custom-item', function () {
            $(this).closest('tr').remove();
            recalculatePurchaseOrderEdit();
            updateRowNumbers();
        });

        $('#formPurchaseOrderEdit').on('submit', function (e) {
            const mulai = $('#tanggal_menu_dari').val();
            const sampai = $('#tanggal_menu_sampai').val();

            if (mulai && sampai && mulai > sampai) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tanggal menu dari tidak boleh lebih besar dari tanggal menu sampai.'
                });
                return;
            }

            let hasVendorWithoutPrice = false;
            $('#po-edit-table tbody tr').each(function () {
                const vendorSelected = $(this).find('.po-vendor').val();
                const harga = parseFloat($(this).find('.po-vendor option:selected').data('harga')) || 0;

                if (vendorSelected && harga <= 0) {
                    hasVendorWithoutPrice = true;
                }
            });

            if (hasVendorWithoutPrice) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terdapat vendor tanpa harga valid. Silakan periksa kembali data harga vendor item.'
                });
            }
        });
    });
</script>
