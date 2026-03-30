@extends('layouts.app')

@section('title', 'Log Asset - Inventaris IT')
@section('page-eyebrow', 'Audit Trail')
@section('page-title', 'Riwayat ' . $item->unique_code)
@section('page-subtitle', 'Catatan lengkap perubahan untuk asset ini, termasuk update field dan QR.')

@section('page-actions')
    <a href="{{ route('items.show', $item) }}" class="btn btn--secondary">Kembali ke Detail</a>
@endsection

@section('content')
    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">{{ $item->name }}</h2>
                <p class="section-subtitle">Audit trail tersusun berdasarkan waktu terbaru lebih dulu.</p>
            </div>
        </div>

        <div class="section-body">
            @if($logs->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">LG</div>
                    <div class="empty-state__title">Belum ada log</div>
                    <div class="empty-state__text">Setiap perubahan pada asset ini akan masuk otomatis ke daftar audit.</div>
                </div>
            @else
                <div class="timeline">
                    @foreach($logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-item__icon">{{ $log->user?->name ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</div>
                            <div>
                                <div class="timeline-item__title">{{ $log->action_label }}</div>
                                <div class="timeline-item__meta">
                                    @if($log->field)
                                        {{ $log->field_label }}: {{ $log->old_value ?? '-' }} -> {{ $log->new_value ?? '-' }}<br>
                                    @endif
                                    {{ $log->notes ?: 'Tidak ada catatan tambahan.' }}<br>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                    @if($log->ip_address)
                                        - IP {{ $log->ip_address }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
