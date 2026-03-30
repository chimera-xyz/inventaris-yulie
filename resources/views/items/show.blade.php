@extends('layouts.app')

@section('title', $item->name . ' - Inventaris IT')
@section('page-eyebrow', 'Asset Detail')
@section('page-title', $item->unique_code)
@section('page-subtitle', '')

@section('page-actions')
    <a href="{{ route('items.print-qr', $item) }}" target="_blank" class="btn btn--secondary">Print QR</a>
    <a href="{{ route('items.print-code', $item) }}" target="_blank" class="btn btn--secondary">Print Kode</a>
    <a href="{{ route('items.print-label', $item) }}" target="_blank" class="btn btn--secondary">Print Label</a>
    <a href="{{ route('items.edit', $item) }}" class="btn btn--primary">Edit Asset</a>
@endsection

@section('content')
    <div class="split-grid">
        <div class="form-grid">
            <div class="hero-panel">
                <div>
                    <div class="hero-panel__eyebrow">{{ $item->category->name }} - {{ $item->category->code }}</div>
                    <h2 class="hero-panel__title">{{ $item->name }}</h2>
                    <p class="hero-panel__text">
                        {{ $item->location ?: 'Lokasi belum dicatat.' }}
                        @if($item->brand || $item->model)
                            - {{ $item->brand ?: 'Tanpa brand' }}{{ $item->model ? ' ' . $item->model : '' }}
                        @endif
                    </p>
                </div>

                <div class="hero-panel__meta">
                    {!! $item->status_badge !!}
                    @if($item->assigned_user_name)
                        <span class="chip chip--info">{{ $item->assigned_user_name }}</span>
                    @endif
                    @if($item->serial_number)
                        <span class="chip chip--muted">SN {{ $item->serial_number }}</span>
                    @endif
                    @if($item->purchase_date)
                        <span class="chip chip--muted">Beli {{ $item->purchase_date->format('d M Y') }}</span>
                    @endif
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Profil Asset</h2>
                        <p class="section-subtitle"></p>
                    </div>
                </div>

                <div class="section-body">
                    <div class="detail-grid lg:grid-cols-3">
                        <div class="detail-item">
                            <div class="detail-item__label">Kode Unik</div>
                            <div class="detail-item__value">{{ $item->unique_code }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Kategori</div>
                            <div class="detail-item__value">{{ $item->category->name }} ({{ $item->category->code }})</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Lokasi</div>
                            <div class="detail-item__value">{{ $item->location ?: '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Brand / Model</div>
                            <div class="detail-item__value">{{ $item->brand ?: '-' }}{{ $item->model ? ' / ' . $item->model : '' }}</div>
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
                            <div class="detail-item__label">Nilai Asset</div>
                            <div class="detail-item__value">{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Serial Number</div>
                            <div class="detail-item__value">{{ $item->serial_number ?: '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Status</div>
                            <div class="detail-item__value">{!! $item->status_badge !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Spesifikasi Lengkap</h2>
                        <p class="section-subtitle">Spesifikasi teknis asset yang tersimpan pada sistem.</p>
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

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Pengguna Saat Ini</h2>
                        <p class="section-subtitle">Data pengguna atau penanggung jawab asset saat ini.</p>
                    </div>
                </div>

                <div class="section-body">
                    @if(filled($item->assigned_user_name) || filled($item->assigned_division) || filled($item->assigned_phone) || $item->assigned_since)
                        <div class="detail-grid lg:grid-cols-2">
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
                            <div class="detail-item">
                                <div class="detail-item__label">Dipakai Sejak</div>
                                <div class="detail-item__value">{{ $item->assigned_since?->format('d M Y') ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">0</div>
                            <div class="empty-state__title">Belum ada pengguna aktif</div>
                            <div class="empty-state__text">Data pengguna saat ini belum dicatat pada asset ini.</div>
                        </div>
                    @endif

                    <div class="callout mt-4">
                        <div class="callout__title">Catatan Internal</div>
                        <p class="callout__text">{{ $item->notes ?: 'Tidak ada catatan tambahan.' }}</p>
                    </div>
                </div>
            </div>

            <div class="section-card" id="asset-history">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Riwayat Asset</h2>
                        <p class="section-subtitle">Catat maintenance, servis, relokasi, insiden, atau peristiwa penting lainnya.</p>
                    </div>
                </div>

                <div class="section-body">
                    <div class="history-composer">
                        <form action="{{ $editingHistory ? route('items.histories.update', [$item, $editingHistory]) : route('items.histories.store', $item) }}" method="POST" class="form-grid">
                            @csrf
                            @if($editingHistory)
                                @method('PUT')
                            @endif

                            <div class="history-composer__header">
                                <div>
                                    <div class="section-title">{{ $editingHistory ? 'Edit Riwayat Asset' : 'Tambah Riwayat Asset' }}</div>
                                    <div class="section-subtitle">{{ $editingHistory ? 'Perbaiki typo atau detail peristiwa yang sudah tercatat.' : 'Masukkan peristiwa operasional baru untuk asset ini.' }}</div>
                                </div>
                                @if($editingHistory)
                                    <a href="{{ route('items.show', $item) }}#asset-history" class="btn btn--secondary">Batal Edit</a>
                                @endif
                            </div>

                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="form-field">
                                    <label for="event_type" class="form-label">Tipe Peristiwa</label>
                                    <select id="event_type" name="event_type" class="form-select" required>
                                        @foreach([
                                            'maintenance' => 'Maintenance',
                                            'service' => 'Servis',
                                            'handover' => 'Penyerahan',
                                            'relocation' => 'Relokasi',
                                            'incident' => 'Insiden',
                                            'note' => 'Catatan',
                                        ] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('event_type', $editingHistory?->event_type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="event_date" class="form-label">Tanggal</label>
                                    <input type="date" id="event_date" name="event_date" class="form-input" value="{{ old('event_date', $editingHistory?->event_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                                </div>

                                <div class="form-field">
                                    <label for="responsible_party" class="form-label">PIC / Vendor</label>
                                    <input type="text" id="responsible_party" name="responsible_party" class="form-input" value="{{ old('responsible_party', $editingHistory?->responsible_party) }}" maxlength="150" placeholder="Nama PIC atau vendor">
                                </div>

                                <div class="form-field">
                                    <label for="contact_phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" id="contact_phone" name="contact_phone" class="form-input" value="{{ old('contact_phone', $editingHistory?->contact_phone) }}" maxlength="30" placeholder="08xxx">
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="title" class="form-label">Judul Singkat</label>
                                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $editingHistory?->title) }}" maxlength="200" placeholder="Contoh: Ganti adaptor, servis panel, pindah ruangan" required>
                            </div>

                            <div class="form-field">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea id="description" name="description" class="form-textarea" placeholder="Rincian pekerjaan, hasil pengecekan, keluhan, tindakan, atau catatan kejadian.">{{ old('description', $editingHistory?->description) }}</textarea>
                            </div>

                            <div class="toolbar">
                                <div class="toolbar__info">Riwayat asset dipakai untuk peristiwa operasional.</div>
                                <div class="toolbar__actions">
                                    <button type="submit" class="btn btn--primary">{{ $editingHistory ? 'Simpan Perubahan' : 'Tambah Riwayat' }}</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($item->histories->isEmpty())
                        <div class="empty-state empty-state--compact mt-4">
                            <div class="empty-state__icon">0</div>
                            <div class="empty-state__title">Belum ada riwayat asset</div>
                            <div class="empty-state__text">Tambahkan peristiwa pertama untuk asset ini bila ada maintenance, servis, relokasi, atau kejadian lain.</div>
                        </div>
                    @else
                        <div class="timeline mt-4">
                            @foreach($item->histories as $history)
                                <div class="timeline-item">
                                    <div class="timeline-item__icon">{{ strtoupper(substr($history->event_type_label, 0, 1)) }}</div>
                                    <div>
                                        <div class="history-event__meta">
                                            <div class="history-event__meta-main">
                                                <span class="chip chip--info">{{ $history->event_type_label }}</span>
                                                <span>{{ $history->event_date->format('d M Y') }}</span>
                                            </div>
                                            <a href="{{ route('items.show', ['item' => $item, 'edit_history' => $history->id]) }}#asset-history" class="btn btn--secondary history-event__edit">Edit</a>
                                        </div>
                                        <div class="timeline-item__title">{{ $history->title }}</div>
                                        <div class="timeline-item__meta">
                                            {{ $history->description ?: 'Tidak ada rincian tambahan.' }}
                                            @if($history->responsible_party || $history->contact_phone)
                                                <br>
                                                {{ $history->responsible_party ?: '-' }}{{ $history->contact_phone ? ' · ' . $history->contact_phone : '' }}
                                            @endif
                                            @if($history->creator)
                                                <br>
                                                Dicatat oleh {{ $history->creator->name }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Riwayat Log</h2>
                        <p class="section-subtitle">Audit teknis sistem.</p>
                    </div>
                    <a href="{{ route('items.logs', $item) }}" class="btn btn--secondary">Lihat Semua Log</a>
                </div>

                <div class="section-body">
                    @if($item->logs->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state__icon">LG</div>
                            <div class="empty-state__title">Belum ada log</div>
                            <div class="empty-state__text">Perubahan asset dan pencetakan QR akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($item->logs->take(5) as $log)
                                <div class="timeline-item">
                                    <div class="timeline-item__icon">{{ $log->user?->name ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</div>
                                    <div>
                                        <div class="timeline-item__title">{{ $log->action_label }}</div>
                                        <div class="timeline-item__meta">
                                            @if($log->field)
                                                {{ $log->field_label }}: {{ $log->old_value ?? '-' }} -> {{ $log->new_value ?? '-' }}<br>
                                            @endif
                                            {{ $log->notes ?: 'Tidak ada catatan tambahan.' }}<br>
                                            {{ $log->created_at->format('d M Y H:i') }} - IP {{ $log->ip_address ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-grid">
            @if($item->photos->isNotEmpty())
                <div class="section-card">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Foto Asset</h2>
                            <p class="section-subtitle">{{ $item->photos->count() }} foto tersimpan untuk asset ini.</p>
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
                        <h2 class="section-title">QR Code</h2>
                        <p class="section-subtitle">Label digital untuk akses cepat ke halaman publik asset ini.</p>
                    </div>
                </div>

                <div class="section-body text-center">
                    @if($item->qr_code_image)
                        <img src="{{ asset('storage/' . $item->qr_code_image) }}" alt="QR Code {{ $item->unique_code }}" class="mx-auto w-full max-w-xs rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="mt-4 text-sm text-slate-500 break-all">{{ $item->qr_code_url }}</p>
                        <div class="toolbar__actions mt-4 justify-center">
                            <a href="{{ route('items.generate-qr', $item) }}" class="btn btn--secondary">Generate Ulang</a>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">QR</div>
                            <div class="empty-state__title">QR belum tersedia</div>
                            <div class="empty-state__text">Generate ulang bila QR publik asset ini belum terbentuk.</div>
                            <a href="{{ route('items.generate-qr', $item) }}" class="btn btn--primary">Generate QR</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if(session('asset_created_modal') === $item->unique_code)
        <div class="modal-backdrop" data-modal>
            <div class="modal-card">
                <div class="modal-card__header">
                    <div>
                        <div class="modal-card__eyebrow">Asset Berhasil Ditambahkan</div>
                        <h2 class="modal-card__title">{{ $item->unique_code }}</h2>
                        <p class="modal-card__text">Asset sudah tersimpan. Anda bisa langsung mencetak QR atau kode uniknya dari sini.</p>
                    </div>
                    <button type="button" class="modal-card__close" data-modal-close aria-label="Tutup">x</button>
                </div>

                <div class="modal-card__actions">
                    <a href="{{ route('items.print-qr', $item) }}" target="_blank" class="btn btn--primary">Cetak QR</a>
                    <a href="{{ route('items.print-code', $item) }}" target="_blank" class="btn btn--secondary">Cetak Kode</a>
                    <button type="button" class="btn btn--secondary" data-modal-close>Tutup</button>
                </div>
            </div>
        </div>
    @endif
@endsection
