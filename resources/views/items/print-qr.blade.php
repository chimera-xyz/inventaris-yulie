<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR {{ $item->unique_code }}</title>
    <style>
        @page {
            margin: 12mm;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .qr-sheet {
            display: grid;
            justify-items: center;
            width: min(100%, 340px);
            padding: 24px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .qr-image {
            display: block;
            width: 280px;
            height: 280px;
        }

        .qr-image svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .unique-code {
            margin-top: 24px;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .print-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            padding: 10px 16px;
            border: 1px solid #d0d7e2;
            border-radius: 999px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            cursor: pointer;
        }

        .print-button:hover {
            background: #f8fafc;
        }

        @media print {
            body {
                min-height: auto;
                display: block;
                background: #ffffff;
            }

            .qr-sheet {
                width: auto;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .qr-image {
                width: 74mm;
                height: 74mm;
            }

            .print-button {
                display: none;
            }

            .unique-code {
                margin-top: 3px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="qr-sheet">
        <div class="qr-image" aria-label="QR {{ $item->unique_code }}">
            {!! $item->renderQrCodeSvg() !!}
        </div>
        <div class="unique-code">{{ $item->unique_code }}</div>
        <button onclick="window.print()" class="print-button">Print</button>
    </div>
</body>
</html>
