@extends('layouts.app')

@section('title', 'Aktivitas - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', 'Aktivitas')
@section('page-subtitle', 'Daftar lengkap aktivitas asset.')

@section('page-actions')
    <a href="{{ route('dashboard') }}" class="btn btn--secondary">Kembali ke Dashboard</a>
@endsection

@section('content')
    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Semua Aktivitas</h2>
                <p class="section-subtitle">Menampilkan {{ $logs->count() }} aktivitas pada halaman ini dari total {{ $logs->total() }} log.</p>
            </div>
        </div>

        <div class="section-body">
            @if($logs->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">0</div>
                    <div class="empty-state__title">Belum ada aktivitas</div>
                    <div class="empty-state__text">Log akan muncul setelah asset mulai ditambahkan atau diubah.</div>
                </div>
            @else
                <div class="timeline">
                    @foreach($logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-item__icon">{{ $log->user?->name ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</div>
                            <div>
                                <div class="timeline-item__title">
                                    @if($log->item)
                                        <a href="{{ route('items.show', $log->item) }}" class="entity-stack__title">{{ $log->item->name }}</a>
                                    @else
                                        Asset terhapus
                                    @endif
                                </div>
                                <div class="timeline-item__meta">
                                    {{ $log->action_label }}
                                    @if($log->field)
                                        · {{ $log->field_label }}
                                    @endif
                                    @if($log->item)
                                        · {{ $log->item->unique_code }}
                                    @endif
                                    <br>
                                    {{ $log->notes ?: 'Perubahan tercatat.' }}
                                    <br>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                    @if($log->user?->name)
                                        · {{ $log->user->name }}
                                    @endif
                                </div>

                                @if($log->item)
                                    <div class="toolbar__actions mt-3">
                                        <a href="{{ route('items.show', $log->item) }}" class="btn btn--secondary">Detail Asset</a>
                                        <a href="{{ route('items.logs', $log->item) }}" class="btn btn--secondary">Audit Asset</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $logs->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
