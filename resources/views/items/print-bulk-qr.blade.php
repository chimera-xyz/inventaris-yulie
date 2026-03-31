<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes - {{ count($items) }} Items</title>
    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .print-page {
            page-break-after: always;
            padding: 0;
            min-height: 273mm;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(6, 1fr);
            gap: 3mm;
        }

        .print-page:last-child {
            page-break-after: avoid;
        }

        .qr-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 0;
            text-align: center;
        }

        .qr-image {
            display: block;
            width: var(--qr-size, 40mm);
            height: var(--qr-size, 40mm);
        }

        .qr-image svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .unique-code {
            font-size: var(--font-size, 20px);
            font-weight: 700;
            color: #111827;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .print-controls {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 20px;
            background: #f5f7fa;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .print-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 140px;
            padding: 12px 20px;
            border: 2px solid #d0d7e2;
            border-radius: 999px;
            background: #ffffff;
            color: #111827;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .print-button:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .print-button--primary {
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
        }

        .print-button--primary:hover {
            background: #4338ca;
            border-color: #4338ca;
        }

        .info-text {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #fffbeb;
            border-bottom: 2px solid #fcd34d;
            font-size: 14px;
            color: #92400e;
            text-align: center;
        }

        .print-container {
            padding: 0 0 80px 0;
        }

        @media print {
            @page {
                margin: 12mm;
            }

            body {
                background: #ffffff;
            }

            .print-controls {
                display: none;
            }

            .info-text {
                display: none;
            }

            .print-container {
                padding: 0;
            }

            .print-page {
                page-break-after: always;
                padding: 0;
            }

            .print-page:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="info-text">
        Total {{ count($items) }} QR codes akan dicetak. Setiap halaman A4 berisi QR codes dengan ukuran yang disesuaikan berdasarkan kategori.
    </div>

    <div class="print-container">
        @php
            $itemsPerPage = 24;
            $pages = array_chunk($items->all(), $itemsPerPage);
        @endphp

        @foreach($pages as $pageItems)
            <div class="print-page">
                @foreach($pageItems as $item)
                    @php
                        $qrSize = $item->getQrSizeInMm();
                        $fontSize = $qrSize > 20 ? 20 : ($qrSize > 10 ? 14 : 10);
                    @endphp
                    <div class="qr-label" style="--qr-size: {{ $qrSize }}mm; --font-size: {{ $fontSize }}px;">
                        <div class="qr-image" aria-label="QR {{ $item->unique_code }}">
                            {!! $item->renderQrCodeSvg() !!}
                        </div>
                        <div class="unique-code">{{ $item->unique_code }}</div>
                    </div>
                @endforeach

                {{-- Fill empty slots for complete grid --}}
                @for($i = count($pageItems); $i < $itemsPerPage; $i++)
                    <div class="qr-label"></div>
                @endfor
            </div>
        @endforeach
    </div>

    <div class="print-controls">
        <button onclick="window.print()" class="print-button print-button--primary">Print QR Codes</button>
        <button onclick="window.close()" class="print-button">Close</button>
    </div>
</body>
</html>
