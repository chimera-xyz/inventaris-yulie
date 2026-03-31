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
            display: flex;
            flex-direction: column;
            gap: 3mm;
        }

        .print-page:last-child {
            page-break-after: avoid;
        }

        .qr-section {
            display: flex;
            flex-direction: column;
            gap: 3mm;
        }

        .qr-section-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 2mm;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2mm;
        }

        .qr-grid {
            display: grid;
            gap: 3mm;
        }

        .qr-grid--large {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(6, 1fr);
        }

        .qr-grid--medium {
            grid-template-columns: repeat(6, 1fr);
            grid-template-rows: repeat(8, 1fr);
        }

        .qr-grid--small {
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(10, 1fr);
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

            .qr-section-title {
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

            .qr-section {
                gap: 2mm;
            }
        }
    </style>
</head>
<body>
    <div class="info-text">
        Total {{ count($items) }} QR codes akan dicetak. QR codes dikelompokkan berdasarkan ukuran untuk efisiensi kertas.
    </div>

    <div class="print-container">
        @php
            // Group items by QR size
            $largeItems = $items->filter(fn($item) => $item->getQrSizeInMm() === 40);
            $mediumItems = $items->filter(fn($item) => $item->getQrSizeInMm() === 20);
            $smallItems = $items->filter(fn($item) => $item->getQrSizeInMm() === 10);

            // Calculate pages for each size
            $largePerPage = 24;  // 4x6 grid
            $mediumPerPage = 48; // 6x8 grid
            $smallPerPage = 80;  // 8x10 grid

            $largePages = $largeItems->chunk($largePerPage);
            $mediumPages = $mediumItems->chunk($mediumPerPage);
            $smallPages = $smallItems->chunk($smallPerPage);

            // Get max pages
            $maxPages = max(count($largePages), count($mediumPages), count($smallPages));
        @endphp

        @for($page = 0; $page < $maxPages; $page++)
            <div class="print-page">
                {{-- Large QR Section --}}
                @if(isset($largePages[$page]))
                    <div class="qr-section">
                        <div class="qr-section-title">QR BESAR (40mm) - {{ $largePages[$page]->count() }} items</div>
                        <div class="qr-grid qr-grid--large">
                            @foreach($largePages[$page] as $item)
                                <div class="qr-label" style="--qr-size: 40mm; --font-size: 20px;">
                                    <div class="qr-image" aria-label="QR {{ $item->unique_code }}">
                                        {!! $item->renderQrCodeSvg() !!}
                                    </div>
                                    <div class="unique-code">{{ $item->unique_code }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Medium QR Section --}}
                @if(isset($mediumPages[$page]))
                    <div class="qr-section">
                        <div class="qr-section-title">QR SEDANG (20mm) - {{ $mediumPages[$page]->count() }} items</div>
                        <div class="qr-grid qr-grid--medium">
                            @foreach($mediumPages[$page] as $item)
                                <div class="qr-label" style="--qr-size: 20mm; --font-size: 14px;">
                                    <div class="qr-image" aria-label="QR {{ $item->unique_code }}">
                                        {!! $item->renderQrCodeSvg() !!}
                                    </div>
                                    <div class="unique-code">{{ $item->unique_code }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Small QR Section --}}
                @if(isset($smallPages[$page]))
                    <div class="qr-section">
                        <div class="qr-section-title">QR KECIL (10mm) - {{ $smallPages[$page]->count() }} items</div>
                        <div class="qr-grid qr-grid--small">
                            @foreach($smallPages[$page] as $item)
                                <div class="qr-label" style="--qr-size: 10mm; --font-size: 10px;">
                                    <div class="qr-image" aria-label="QR {{ $item->unique_code }}">
                                        {!! $item->renderQrCodeSvg() !!}
                                    </div>
                                    <div class="unique-code">{{ $item->unique_code }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endfor
    </div>

    <div class="print-controls">
        <button onclick="window.print()" class="print-button print-button--primary">Print QR Codes</button>
        <button onclick="window.close()" class="print-button">Close</button>
    </div>
</body>
</html>
