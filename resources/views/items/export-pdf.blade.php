<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $item->unique_code }} - Asset Report</title>
    <style>
        @page {
            size: A4;
            margin: 16mm 14mm 16mm 14mm;
        }

        :root {
            --ink: #142033;
            --muted: #607086;
            --line: #d6dce5;
            --line-strong: #b7c0cb;
            --gold: #8e6115;
            --gold-soft: #f5ede0;
            --surface: #f7f9fc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink);
            font-family: "Helvetica Neue", Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .document {
            display: block;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 1.5px solid var(--line-strong);
            padding-bottom: 12pt;
            margin-bottom: 16pt;
        }

        .header__brand,
        .header__meta {
            display: table-cell;
            vertical-align: top;
        }

        .header__brand {
            width: 62%;
        }

        .header__meta {
            width: 38%;
            text-align: right;
        }

        .logo {
            max-width: 160pt;
            height: auto;
            display: block;
            margin-bottom: 10pt;
        }

        .eyebrow {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1.9pt;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 3pt;
        }

        .title {
            font-size: 18pt;
            font-weight: 700;
            line-height: 1.18;
            margin: 0 0 6pt;
        }

        .subtitle {
            font-size: 9pt;
            color: var(--muted);
            max-width: 340pt;
        }

        .meta-box {
            display: inline-block;
            min-width: 180pt;
            border: 1px solid var(--line);
            border-radius: 10pt;
            background: var(--surface);
            padding: 10pt 12pt;
            text-align: left;
        }

        .meta-box__label {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 1.4pt;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 4pt;
        }

        .meta-box__value {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 8pt;
        }

        .meta-box__detail {
            font-size: 8.5pt;
            color: var(--muted);
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 12pt;
            padding: 14pt;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfd 100%);
            margin-bottom: 14pt;
        }

        .hero__top {
            display: table;
            width: 100%;
        }

        .hero__main,
        .hero__status {
            display: table-cell;
            vertical-align: top;
        }

        .hero__status {
            width: 145pt;
            text-align: right;
        }

        .asset-name {
            font-size: 16pt;
            font-weight: 700;
            margin-bottom: 5pt;
        }

        .asset-meta {
            font-size: 9.5pt;
            color: var(--muted);
        }

        .status-pill {
            display: inline-block;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6pt 10pt;
            font-size: 8.5pt;
            font-weight: 700;
            background: #ffffff;
        }

        .status-pill--available { color: #0f766e; background: #ecfeff; border-color: #bae6fd; }
        .status-pill--in_use { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
        .status-pill--maintenance { color: #92400e; background: #fffbeb; border-color: #fde68a; }
        .status-pill--broken { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
        .status-pill--lost { color: #475569; background: #f8fafc; border-color: #cbd5e1; }

        .section {
            margin-bottom: 14pt;
        }

        .section__header {
            display: table;
            width: 100%;
            margin-bottom: 8pt;
        }

        .section__title,
        .section__subtitle {
            display: table-cell;
            vertical-align: bottom;
        }

        .section__title {
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 1.4pt;
            text-transform: uppercase;
            color: var(--ink);
            width: 38%;
        }

        .section__subtitle {
            font-size: 8.5pt;
            text-align: right;
            color: var(--muted);
        }

        .section__body {
            border-top: 1px solid var(--line);
            padding-top: 8pt;
        }

        .grid-2 {
            display: table;
            width: 100%;
            border-spacing: 0 8pt;
        }

        .grid-2__col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .grid-2__col + .grid-2__col {
            padding-left: 8pt;
        }

        .detail-card {
            border: 1px solid var(--line);
            border-radius: 10pt;
            background: #ffffff;
            padding: 10pt 11pt;
            min-height: 62pt;
        }

        .detail-label {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 5pt;
        }

        .detail-value {
            font-size: 10pt;
            font-weight: 600;
            color: var(--ink);
            white-space: pre-line;
        }

        .text-block {
            border: 1px solid var(--line);
            border-radius: 10pt;
            background: #ffffff;
            padding: 12pt;
            white-space: pre-line;
        }

        .photo-grid {
            display: table;
            width: 100%;
            border-spacing: 0 8pt;
        }

        .photo-row {
            display: table-row;
        }

        .photo-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 8pt;
        }

        .photo-cell:last-child {
            padding-right: 0;
        }

        .photo-card {
            border: 1px solid var(--line);
            border-radius: 10pt;
            overflow: hidden;
            background: #ffffff;
            padding: 6pt;
        }

        .photo-card img {
            width: 100%;
            height: 180pt;
            object-fit: cover;
            display: block;
            border-radius: 7pt;
        }

        .history-list {
            display: block;
        }

        .history-item {
            border: 1px solid var(--line);
            border-radius: 10pt;
            background: #ffffff;
            padding: 11pt 12pt;
            margin-bottom: 8pt;
            page-break-inside: avoid;
        }

        .history-item__meta {
            margin-bottom: 4pt;
            font-size: 8pt;
            color: var(--muted);
        }

        .history-item__badge {
            display: inline-block;
            margin-right: 8pt;
            border-radius: 999px;
            background: var(--gold-soft);
            color: var(--gold);
            padding: 3pt 7pt;
            font-weight: 700;
        }

        .history-item__title {
            font-size: 10pt;
            font-weight: 700;
            margin-bottom: 4pt;
            color: var(--ink);
        }

        .history-item__text {
            font-size: 9pt;
            color: #334155;
            white-space: pre-line;
        }

        .footer {
            margin-top: 14pt;
            padding-top: 8pt;
            border-top: 1px solid var(--line);
            font-size: 8pt;
            color: var(--muted);
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div class="header__brand">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="Yulie Sekuritas" class="logo">
                @endif
                <div class="eyebrow">Inventaris IT</div>
                <div class="title">Asset Profile Document</div>
                <div class="subtitle">
                    Dokumen inventaris untuk kebutuhan identifikasi asset, pencatatan penanggung jawab, dan arsip operasional perusahaan.
                </div>
            </div>
            <div class="header__meta">
                <div class="meta-box">
                    <div class="meta-box__label">Kode Asset</div>
                    <div class="meta-box__value">{{ $item->unique_code }}</div>
                    <div class="meta-box__detail">Generated {{ $generatedAt->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="hero">
            <div class="hero__top">
                <div class="hero__main">
                    <div class="eyebrow">{{ $item->category->name }} - {{ $item->category->code }}</div>
                    <div class="asset-name">{{ $item->name }}</div>
                    <div class="asset-meta">
                        {{ $item->location ?: 'Lokasi belum dicatat.' }}
                        @if($item->brand || $item->model)
                            | {{ $item->brand ?: 'Tanpa brand' }}{{ $item->model ? ' / ' . $item->model : '' }}
                        @endif
                    </div>
                </div>
                <div class="hero__status">
                    <span class="status-pill status-pill--{{ $item->status }}">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section__header">
                <div class="section__title">Informasi Asset</div>
                <div class="section__subtitle">Ringkasan data utama asset</div>
            </div>
            <div class="section__body">
                <div class="grid-2">
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Kategori</div>
                            <div class="detail-value">{{ $item->category->name }} ({{ $item->category->code }})</div>
                        </div>
                    </div>
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Brand / Model</div>
                            <div class="detail-value">{{ $item->brand ?: '-' }}{{ $item->model ? ' / ' . $item->model : '' }}</div>
                        </div>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Serial Number</div>
                            <div class="detail-value">{{ $item->serial_number ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Dipakai Sejak</div>
                            <div class="detail-value">{{ $item->assigned_since?->format('d M Y') ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section__header">
                <div class="section__title">Pengguna Saat Ini</div>
                <div class="section__subtitle">Penanggung jawab operasional asset</div>
            </div>
            <div class="section__body">
                <div class="grid-2">
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value">{{ $item->assigned_user_name ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Divisi</div>
                            <div class="detail-value">{{ $item->assigned_division ?: '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Nomor Telepon</div>
                            <div class="detail-value">{{ $item->assigned_phone ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="grid-2__col">
                        <div class="detail-card">
                            <div class="detail-label">Lokasi</div>
                            <div class="detail-value">{{ $item->location ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section__header">
                <div class="section__title">Spesifikasi Lengkap</div>
                <div class="section__subtitle">Rincian teknis asset</div>
            </div>
            <div class="section__body">
                <div class="text-block">{{ $item->specifications ?: 'Belum ada spesifikasi lengkap yang dicatat.' }}</div>
            </div>
        </div>

        @if($photoDataUris->isNotEmpty())
            <div class="section">
                <div class="section__header">
                    <div class="section__title">Foto Asset</div>
                    <div class="section__subtitle">{{ $photoDataUris->count() }} foto terlampir</div>
                </div>
                <div class="section__body">
                    <div class="photo-grid">
                        @foreach($photoDataUris->chunk(2) as $photoRow)
                            <div class="photo-row">
                                @foreach($photoRow as $photoDataUri)
                                    <div class="photo-cell">
                                        <div class="photo-card">
                                            <img src="{{ $photoDataUri }}" alt="Foto Asset {{ $item->unique_code }}">
                                        </div>
                                    </div>
                                @endforeach
                                @if($photoRow->count() === 1)
                                    <div class="photo-cell"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="section">
            <div class="section__header">
                <div class="section__title">Riwayat Asset</div>
                <div class="section__subtitle">Peristiwa operasional dan maintenance</div>
            </div>
            <div class="section__body">
                @if($item->histories->isEmpty())
                    <div class="text-block">Belum ada riwayat asset yang tercatat.</div>
                @else
                    <div class="history-list">
                        @foreach($item->histories as $history)
                            <div class="history-item">
                                <div class="history-item__meta">
                                    <span class="history-item__badge">{{ strtoupper($history->event_type_label) }}</span>
                                    {{ $history->event_date->format('d M Y') }}
                                </div>
                                <div class="history-item__title">{{ $history->title }}</div>
                                <div class="history-item__text">
                                    {{ $history->description ?: 'Tidak ada rincian tambahan.' }}
                                    @if($history->responsible_party || $history->contact_phone)

PIC / Vendor: {{ $history->responsible_party ?: '-' }}{{ $history->contact_phone ? ' | ' . $history->contact_phone : '' }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="footer">
            Dokumen ini dihasilkan oleh Sistem Inventaris IT Yulie Sekuritas untuk kebutuhan arsip internal perusahaan.
        </div>
    </div>
</body>
</html>
