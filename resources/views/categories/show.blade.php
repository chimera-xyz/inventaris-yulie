@extends('layouts.app')

@section('title', $category->name . ' - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', $category->name)
@section('page-subtitle', 'Ringkasan kategori dan asset terkait.')

@section('page-actions')
    <a href="{{ route('categories.edit', $category) }}" class="btn btn--secondary">Edit Kategori</a>
    <a href="{{ route('items.create') }}" class="btn btn--primary">Tambah Asset</a>
@endsection

@section('content')
    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Informasi Kategori</h2>
                <p class="section-subtitle">Kode kategori, jumlah asset, dan catatan yang Anda simpan.</p>
            </div>
        </div>

        <div class="section-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-item__label">Kode</div>
                    <div class="detail-item__value">{{ $category->code }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-item__label">Total Asset</div>
                    <div class="detail-item__value">{{ $category->items_count }}</div>
                </div>
            </div>

            <div class="callout mt-4">
                <div class="callout__title">Catatan Kategori</div>
                <p class="callout__text">{{ $category->description ?: 'Belum ada catatan kategori.' }}</p>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Asset Dalam Kategori</h2>
                <p class="section-subtitle">Daftar asset terbaru yang sudah tergabung pada kategori ini.</p>
            </div>
        </div>

        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">[]</div>
                <div class="empty-state__title">Belum ada asset</div>
                <div class="empty-state__text">Kategori ini sudah siap dipakai, tetapi belum ada asset yang tercatat.</div>
            </div>
        @else
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Asset</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td data-label="Kode"><a href="{{ route('items.show', $item) }}" class="entity-stack__title">{{ $item->unique_code }}</a></td>
                                <td data-label="Asset">
                                    <div class="entity-stack">
                                        <div class="entity-stack__title">{{ $item->name }}</div>
                                        <div class="entity-stack__meta">{{ $item->brand ?: 'Tanpa brand' }}{{ $item->model ? ' - ' . $item->model : '' }}</div>
                                    </div>
                                </td>
                                <td data-label="Status">{!! $item->status_badge !!}</td>
                                <td data-label="Lokasi">{{ $item->location ?: '-' }}</td>
                                <td data-label="Dibuat">{{ $item->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="section-body">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
