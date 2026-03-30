<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Item - {{ $item->name }} ({{ $item->unique_code }})</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .label-container {
                page-break-inside: avoid;
            }
        }
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .label-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }
        .label-title {
            font-size: 14px;
            color: #6b7280;
        }
        .item-details {
            margin-top: 20px;
        }
        .item-code {
            font-size: 36px;
            font-weight: bold;
            color: #1e40af;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 3px;
            margin-bottom: 15px;
        }
        .item-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .item-category {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #374151;
            min-width: 100px;
        }
        .detail-value {
            color: #1f2937;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            background: #f3f4f6;
            border: 2px dashed #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 20px;
            border-radius: 8px;
        }
        .label-qr {
            width: 100px;
            height: 100px;
            margin-left: 20px;
        }
        .label-qr svg {
            display: block;
            width: 100%;
            height: 100%;
        }
        .print-info {
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
        .print-button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .print-button:hover {
            background: #1e3a8a;
        }
        @media print {
            .print-button {
                display: none;
            }
            .print-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="label-header">
            <div>
                <div class="company-name">Inventaris IT</div>
                <div class="label-title">Label Aset IT</div>
            </div>
        </div>

        <div class="item-details">
            <div class="item-code">{{ $item->unique_code }}</div>
            <div class="item-name">{{ $item->name }}</div>
            <div class="item-category">{{ $item->category->name }} ({{ $item->category->code }})</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Merek:</div>
            <div class="detail-value">{{ $item->brand ?? '-' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Model:</div>
            <div class="detail-value">{{ $item->model ?? '-' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Nomor Seri:</div>
            <div class="detail-value">{{ $item->serial_number ?? '-' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Lokasi:</div>
            <div class="detail-value">{{ $item->location ?? '-' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Status:</div>
            <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
        </div>

        <div class="detail-row" style="align-items: center; margin-top: 20px;">
            <div class="detail-label">QR Code:</div>
            @if($item->qr_code_image)
                <div class="label-qr" aria-label="QR Code">
                    {!! $item->renderQrCodeSvg() !!}
                </div>
            @else
                <div class="qr-placeholder">
                    <span style="font-size: 24px; color: #9ca3af;">?</span>
                </div>
            @endif
        </div>

        <div class="print-info">
            Scan QR code untuk melihat detail item di sistem inventaris
        </div>

        <button onclick="window.print()" class="print-button">
            Print Label
        </button>
    </div>
</body>
</html>
