@extends('layouts.app')

@section('title', 'Dashboard - Inventaris IT')
@section('page-eyebrow', 'Overview')
@section('page-title', 'Dashboard')

@section('page-actions')
    <a href="{{ route('items.create') }}" class="btn btn--primary">Tambah Asset</a>
@endsection

@section('content')
    <div class="hero-panel">
        <div class="hero-panel__eyebrow">Status Saat ini</div>
        <h2 class="hero-panel__title">{{ number_format($totalItems) }} asset tercatat.</h2>
        <p class="hero-panel__text">
            {{ number_format($availableItems) }} tersedia, {{ number_format($inUseItems) }} sedang dipakai,
            {{ number_format($warrantyAlertCount) }} mendekati akhir garansi, dan nilai asset tercatat
            Rp {{ number_format($totalAssetValue, 0, ',', '.') }}.
        </p>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-card__label">Total</div>
            <div class="metric-card__value">{{ number_format($totalItems) }}</div>
            <div class="metric-card__helper">Seluruh asset aktif.</div>
        </div>
        <div class="metric-card">
            <div class="metric-card__label">Tersedia</div>
            <div class="metric-card__value">{{ number_format($availableItems) }}</div>
            <div class="metric-card__helper">Siap digunakan.</div>
        </div>
        <div class="metric-card">
            <div class="metric-card__label">Dipakai</div>
            <div class="metric-card__value">{{ number_format($inUseItems) }}</div>
            <div class="metric-card__helper">Sedang berstatus in use.</div>
        </div>
        <div class="metric-card">
            <div class="metric-card__label">Kategori</div>
            <div class="metric-card__value">{{ number_format($totalCategories) }}</div>
            <div class="metric-card__helper">Master data aktif.</div>
        </div>
    </div>

    <div class="split-grid">
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Asset Terbaru</h2>
                    <p class="section-subtitle">Entri asset yang paling baru dicatat.</p>
                </div>
                <a href="{{ route('items.index') }}" class="btn btn--secondary">Lihat Semua</a>
            </div>

            @if($recentItems->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">0</div>
                    <div class="empty-state__title">Belum ada data</div>
                    <div class="empty-state__text">Mulai dari kategori dan asset pertama.</div>
                </div>
            @else
                <div class="data-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentItems as $item)
                                <tr>
                                    <td data-label="Kode"><a href="{{ route('items.show', $item) }}" class="entity-stack__title">{{ $item->unique_code }}</a></td>
                                    <td data-label="Nama">
                                        <div class="entity-stack">
                                            <div class="entity-stack__title">{{ $item->name }}</div>
                                            <div class="entity-stack__meta">{{ $item->location ?: 'Lokasi belum dicatat' }}</div>
                                        </div>
                                    </td>
                                    <td data-label="Status">{!! $item->status_badge !!}</td>
                                    <td data-label="Kategori">{{ $item->category->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Perlu Perhatian</h2>
                    <p class="section-subtitle">Ringkasan status yang perlu ditindaklanjuti dan garansi yang segera berakhir.</p>
                </div>
            </div>

            <div class="section-body">
                <div class="attention-panel">
                    <div class="attention-grid">
                        <div class="attention-stat">
                            <div class="attention-stat__label">Rusak</div>
                            <div class="attention-stat__value">{{ number_format($brokenItems) }}</div>
                            <div class="attention-stat__meta">Perlu perbaikan.</div>
                        </div>
                        <div class="attention-stat">
                            <div class="attention-stat__label">Maintenance</div>
                            <div class="attention-stat__value">{{ number_format($maintenanceItems) }}</div>
                            <div class="attention-stat__meta">Sedang proses servis.</div>
                        </div>
                        <div class="attention-stat">
                            <div class="attention-stat__label">Hilang</div>
                            <div class="attention-stat__value">{{ number_format($lostItems) }}</div>
                            <div class="attention-stat__meta">Butuh penelusuran.</div>
                        </div>
                        <div class="attention-stat">
                            <div class="attention-stat__label">Total</div>
                            <div class="attention-stat__value">{{ number_format($attentionItemsCount) }}</div>
                            <div class="attention-stat__meta">Perlu perhatian.</div>
                        </div>
                    </div>

                    <div class="attention-alerts">
                        <div class="attention-alerts__header">
                            <h3 class="attention-alerts__title">Garansi Terdekat</h3>
                            <span class="chip chip--muted">{{ number_format($warrantyAlertCount) }}</span>
                        </div>

                        @if($warrantyAlerts->isEmpty())
                            <div class="empty-state empty-state--compact">
                                <div class="empty-state__icon">0</div>
                                <div class="empty-state__title">Tidak ada alert garansi</div>
                                <div class="empty-state__text">Belum ada asset yang masa garansinya mendekati akhir.</div>
                            </div>
                        @else
                            <div class="attention-alert-list">
                                @foreach($warrantyAlerts as $item)
                                    <div class="attention-alert">
                                        <div class="attention-alert__main">
                                            <div class="attention-alert__title">{{ $item->name }}</div>
                                            <div class="attention-alert__meta">{{ $item->unique_code }} · {{ $item->category->name }}</div>
                                        </div>
                                        <div class="attention-alert__date">{{ $item->warranty_expiry?->format('d M Y') ?? '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Aktivitas Terbaru</h2>
                <p class="section-subtitle">Menampilkan 3 perubahan terakhir pada asset.</p>
            </div>
            @if($hasMoreRecentLogs)
                <a href="{{ route('activities.index') }}" class="btn btn--secondary">Lihat Semua Aktivitas</a>
            @endif
        </div>

        <div class="section-body">
            @if($recentLogs->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">0</div>
                    <div class="empty-state__title">Belum ada aktivitas</div>
                    <div class="empty-state__text">Log akan muncul setelah asset mulai ditambahkan atau diubah.</div>
                </div>
            @else
                <div class="timeline">
                    @foreach($recentLogs as $log)
                        <div class="timeline-item">
                            <div class="timeline-item__icon">{{ $log->user?->name ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</div>
                            <div>
                                <div class="timeline-item__title">{{ $log->item?->name ?? 'Asset terhapus' }}</div>
                                <div class="timeline-item__meta">
                                    {{ $log->action_label }}
                                    @if($log->field)
                                        · {{ $log->field_label }}
                                    @endif
                                    <br>
                                    {{ $log->notes ?: 'Perubahan tercatat.' }}
                                    <br>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
