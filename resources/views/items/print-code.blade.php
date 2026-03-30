<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode {{ $item->unique_code }}</title>
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

        .code-sheet {
            display: grid;
            justify-items: center;
            gap: 18px;
            width: min(100%, 360px);
            padding: 32px 24px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .code-text {
            font-size: 38px;
            line-height: 1.05;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-align: center;
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

            .code-sheet {
                width: auto;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .code-text {
                font-size: 26pt;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="code-sheet">
        <div class="code-text">{{ $item->unique_code }}</div>
        <button onclick="window.print()" class="print-button">Print</button>
    </div>
</body>
</html>
