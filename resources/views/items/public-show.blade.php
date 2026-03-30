@extends('layouts.public')

@section('title', $item->unique_code . ' | Asset Info')

@section('content')
    <div class="public-card">
        <div class="hero-panel">
            <div>
                <div class="hero-panel__eyebrow">{{ $item->category->name }} - {{ $item->category->code }}</div>
                <h1 class="hero-panel__title">{{ $item->name }}</h1>
                <p class="hero-panel__text">{{ $item->location ?: 'Lokasi belum dicatat.' }}</p>
            </div>

            <div class="hero-panel__meta">
                <span class="chip chip--muted">{{ $item->unique_code }}</span>
                {!! $item->status_badge !!}
            </div>
        </div>

        @if($item->photos->isNotEmpty())
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Foto Asset</h2>
                        <p class="section-subtitle">{{ $item->photos->count() }} foto tercatat.</p>
                    </div>
                </div>
                <div class="section-body">
                    <div class="photo-gallery">
                        @foreach($item->photos as $photo)
                            <a href="{{ asset('storage/' . $photo->path) }}" target="_blank" class="photo-tile">
                                <img src="{{ asset('storage/' . $photo->path) }}" alt="Foto {{ $item->unique_code }}" class="photo-tile__image">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Informasi Asset</h2>
                    <p class="section-subtitle">Informasi dasar asset yang bisa diakses dari hasil scan QR.</p>
                </div>
            </div>

            <div class="section-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-item__label">Kategori</div>
                        <div class="detail-item__value">{{ $item->category->name }} ({{ $item->category->code }})</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item__label">Brand / Model</div>
                        <div class="detail-item__value">{{ $item->brand ?: '-' }}{{ $item->model ? ' / ' . $item->model : '' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item__label">Serial Number</div>
                        <div class="detail-item__value">{{ $item->serial_number ?: '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item__label">Tanggal Beli</div>
                        <div class="detail-item__value">{{ $item->purchase_date?->format('d M Y') ?: '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item__label">Status Garansi</div>
                        <div class="detail-item__value">{{ $item->warranty_status_label }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item__label">Dipakai Sejak</div>
                        <div class="detail-item__value">{{ $item->assigned_since?->format('d M Y') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="public-section-grid">
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Pengguna Saat Ini</h2>
                        <p class="section-subtitle">Penanggung jawab operasional asset saat ini.</p>
                    </div>
                </div>

                <div class="section-body">
                    @if(filled($item->assigned_user_name) || filled($item->assigned_division) || filled($item->assigned_phone))
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-item__label">Nama</div>
                                <div class="detail-item__value">{{ $item->assigned_user_name ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-item__label">Divisi</div>
                                <div class="detail-item__value">{{ $item->assigned_division ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-item__label">Nomor Telepon</div>
                                <div class="detail-item__value">{{ $item->assigned_phone ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">0</div>
                            <div class="empty-state__title">Belum ada pengguna aktif</div>
                            <div class="empty-state__text">Data pengguna saat ini belum dicatat pada asset ini.</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Spesifikasi Lengkap</h2>
                        <p class="section-subtitle">Ringkasan spesifikasi yang tercatat pada asset ini.</p>
                    </div>
                </div>

                <div class="section-body">
                    @if(filled($item->specifications))
                        <div class="rich-text-block">{!! nl2br(e($item->specifications)) !!}</div>
                    @else
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">0</div>
                            <div class="empty-state__title">Spesifikasi belum dicatat</div>
                            <div class="empty-state__text">Belum ada rincian spesifikasi untuk asset ini.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Riwayat Asset</h2>
                    <p class="section-subtitle">Catatan maintenance, servis, relokasi, atau peristiwa operasional lainnya.</p>
                </div>
            </div>

            <div class="section-body">
                @if($item->histories->isEmpty())
                    <div class="empty-state empty-state--compact">
                        <div class="empty-state__icon">0</div>
                        <div class="empty-state__title">Belum ada riwayat</div>
                        <div class="empty-state__text">Belum ada peristiwa asset yang tercatat.</div>
                    </div>
                @else
                    <div class="timeline">
                        @foreach($item->histories as $history)
                            <div class="timeline-item">
                                <div class="timeline-item__icon">{{ strtoupper(substr($history->event_type_label, 0, 1)) }}</div>
                                <div>
                                    <div class="history-event__meta">
                                        <span class="chip chip--info">{{ $history->event_type_label }}</span>
                                        <span>{{ $history->event_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="timeline-item__title">{{ $history->title }}</div>
                                    <div class="timeline-item__meta">
                                        {{ $history->description ?: 'Tidak ada rincian tambahan.' }}
                                        @if($history->responsible_party || $history->contact_phone)
                                            <br>
                                            {{ $history->responsible_party ?: '-' }}{{ $history->contact_phone ? ' · ' . $history->contact_phone : '' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
