<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO {{ $purchaseOrder->kode_po }}</title>
    <style>
        body {
            margin: 0;
            font-family: 'Times New Roman', serif;
            color: #000;
            font-size: 12px;
        }

        .po-document {
            margin-bottom: 16px;
        }

        .po-document.with-page-break {
            page-break-after: always;
        }

        .po-sheet {
            width: 100%;
            border-collapse: collapse;
        }

        .po-sheet td,
        .po-sheet th {
            border: 1px solid #333;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .po-sheet .sheet-title {
            background: #ffef3a;
            font-weight: 700;
            text-align: center;
            font-size: 18px;
        }

        .po-sheet .sheet-subtitle {
            text-align: center;
            font-weight: 700;
            font-size: 14px;
        }

        .po-sheet .meta-label-spacer {
            width: 7%;
        }

        .po-sheet .meta-label {
            width: 14%;
            font-weight: 700;
        }

        .po-sheet .meta-separator {
            width: 3%;
            text-align: center;
            font-weight: 700;
        }

        .po-sheet .meta-value {
            font-size: 12px;
        }

        .po-sheet .col-no,
        .po-sheet .col-name,
        .po-sheet .col-qty,
        .po-sheet .col-unit,
        .po-sheet .col-price,
        .po-sheet .col-delivery {
            background: #dce6f1;
            text-align: center;
            font-weight: 700;
        }

        .po-sheet .col-no {
            width: 7%;
        }

        .po-sheet .col-name {
            width: 23%;
        }

        .po-sheet .col-qty {
            width: 12%;
        }

        .po-sheet .col-unit {
            width: 10%;
        }

        .po-sheet .col-price {
            width: 24%;
        }

        .po-sheet .col-delivery {
            width: 24%;
        }

        .po-sheet .currency-cell {
            width: 6%;
            text-align: left;
            white-space: nowrap;
        }

        .po-sheet .text-center {
            text-align: center;
        }

        .po-sheet .text-right {
            text-align: right;
        }

        .po-sheet .delivery-cell {
            text-align: center;
            font-weight: 700;
        }

        .po-sheet .empty-cell,
        .po-sheet .total-label,
        .po-sheet .total-value {
            font-weight: 700;
        }

        .po-sheet .total-label {
            text-align: right;
        }
    </style>
</head>
<body>
    @include('purchase_order.partials.vendor_documents', ['vendorGroups' => $vendorGroups, 'purchaseOrder' => $purchaseOrder])
</body>
</html>
