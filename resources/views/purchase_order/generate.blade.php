<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Purchase Order Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('purchase_order')}}">Purchase Order</a></li>
                            <li class="breadcrumb-item active">Generate Detail</li>
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

                <form method="POST" action="{{route('purchase_order.store')}}" id="formPurchaseOrderDetail">
                    @csrf
                    <input type="hidden" name="sppg_id" value="{{$sppg->id}}">
                    <input type="hidden" name="tanggal_menu_dari" value="{{$tanggalMenuDari}}">
                    <input type="hidden" name="tanggal_menu_sampai" value="{{$tanggalMenuSampai}}">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Informasi Generate</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4"><strong>SPPG:</strong> {{$sppg->nama}}</div>
                                <div class="col-md-4"><strong>Periode Menu:</strong> {{$tanggalMenuDari}} s/d {{$tanggalMenuSampai}}</div>
                                <div class="col-md-4"><strong>Jumlah Item Unik:</strong> {{count($items)}}</div>
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
                        <div class="card-body border-bottom py-2">
                            <small class="text-muted">Hilangkan centang pada item yang bahan bakunya masih tersedia agar item tersebut tidak ikut dibuat ke Purchase Order.</small>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered mb-0" id="po-detail-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">No.</th>
                                        <th style="width: 95px">Ikut PO</th>
                                        <th>Item</th>
                                        <th style="width: 180px">Qty</th>
                                        <th style="width: 280px">Vendor</th>
                                        <th style="width: 180px">Harga Satuan</th>
                                        <th style="width: 200px">Subtotal</th>
                                        <th style="width: 80px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $idx => $item)
                                        @php
                                            $options = $vendorOptionsByItem[$item->id] ?? [];
                                        @endphp
                                        <tr class="po-item-row" data-row-type="regular">
                                            <td>{{ $idx + 1 }}</td>
                                            <td class="text-center align-middle">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input po-include-toggle" id="po_include_{{$item->id}}" checked>
                                                    <label class="custom-control-label" for="po_include_{{$item->id}}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                {{$item->nama}}
                                                <small class="text-muted d-block">Satuan: {{optional($item->uom)->nama ?? '-'}}</small>
                                                <input type="hidden" name="item_ids[]" value="{{$item->id}}" class="po-item-id-input">
                                            </td>
                                            <td>
                                                <input type="number" name="qtys[]" class="form-control po-qty" min="0.01" step="0.01" placeholder="Masukkan qty" required>
                                            </td>
                                            <td>
                                                <select name="vendor_ids[]" class="form-control po-vendor" required>
                                                    <option value="">-- Pilih Vendor --</option>
                                                    @foreach($options as $opt)
                                                        <option value="{{$opt['vendor_id']}}" data-harga="{{$opt['harga']}}">{{$opt['vendor_label']}}</option>
                                                    @endforeach
                                                </select>
                                                @if(empty($options))
                                                    <small class="text-danger">Harga vendor untuk item ini belum tersedia. Silahakan Tambah <a href="{{ url('vendor') }}" class="btn btn-primary btn-sm">Vendor</a> atau <a class="btn btn-success btn-sm" href="{{ url('item-vendor') }}">Tambah Harga Vendor Items</a></small>
                                                @endif
                                            </td>
                                            <td class="po-harga">Rp 0,00</td>
                                            <td class="po-subtotal">Rp 0,00</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tbody id="custom-items-tbody"></tbody>
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
                            <button type="submit" class="btn btn-primary">Simpan Purchase Order</button>
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

    function recalculatePurchaseOrder() {
        let grandTotal = 0;

        $('#po-detail-table tbody tr.po-item-row').each(function () {
            if ($(this).hasClass('po-row-excluded')) {
                $(this).find('.po-harga').text(formatRupiah(0));
                $(this).find('.po-subtotal').text(formatRupiah(0));
                return;
            }

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

    function syncPurchaseOrderRowState($row) {
        const included = $row.find('.po-include-toggle').is(':checked');

        $row.toggleClass('po-row-excluded', !included);
        $row.find('.po-item-id-input').prop('disabled', !included);
        $row.find('.po-qty').prop('disabled', !included).prop('required', included);
        $row.find('.po-vendor').prop('disabled', !included).prop('required', included);

        if (!included) {
            $row.find('.po-qty').val('');
            $row.find('.po-vendor').val('');
        }

        recalculatePurchaseOrder();
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
        $('#po-detail-table tbody tr').each(function () {
            $(this).find('td:first').text(counter++);
        });
        $('#custom-items-tbody tr').each(function () {
            $(this).find('.row-number').text(counter++);
        });
    }

    $(function () {
        $('.po-item-row').each(function () {
            syncPurchaseOrderRowState($(this));
        });

        recalculatePurchaseOrder();
        updateRowNumbers();

        $(document).on('change keyup', '.po-qty, .po-vendor', function () {
            recalculatePurchaseOrder();
        });

        $(document).on('change', '.po-include-toggle', function () {
            syncPurchaseOrderRowState($(this).closest('.po-item-row'));
        });

        $(document).on('change keyup', '.custom-item-row .po-qty', function () {
            recalculatePurchaseOrder();
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
            recalculatePurchaseOrder();
            updateRowNumbers();
            $('#modal-custom-item').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item custom berhasil ditambahkan.', timer: 1200, showConfirmButton: false });
        });

        $(document).on('click', '.btn-remove-custom-item', function () {
            $(this).closest('tr').remove();
            recalculatePurchaseOrder();
            updateRowNumbers();
        });

        $('#formPurchaseOrderDetail').on('submit', function (e) {
            let hasVendorWithoutPrice = false;

            if ($('.po-item-row').length > 0 && $('.po-item-row .po-include-toggle:checked').length === 0 && $('#custom-items-tbody tr').length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih minimal satu item yang akan di-PO atau tambahkan item custom.'
                });
                return;
            }

            $('#po-detail-table tbody tr.po-item-row').each(function () {
                if ($(this).hasClass('po-row-excluded')) {
                    return;
                }

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

