@extends('layouts.app')

@section('title', 'Kategori - Inventaris IT')
@section('page-eyebrow', 'Master Data')
@section('page-title', 'Kategori')
@section('page-subtitle', 'Daftar kategori untuk pengelompokan dan penomoran asset.')

@section('page-actions')
    <a href="{{ route('categories.create') }}" class="btn btn--primary">Tambah Kategori</a>
@endsection

@section('content')
    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Daftar Kategori</h2>
                <p class="section-subtitle">{{ $categories->count() }} kategori tercatat.</p>
            </div>
        </div>

        @if($categories->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">0</div>
                <div class="empty-state__title">Belum ada kategori</div>
                <div class="empty-state__text">Mulai dari kategori yang benar-benar dipakai.</div>
            </div>
        @else
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Catatan</th>
                            <th>Total Asset</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td data-label="Kode"><span class="chip chip--muted">{{ $category->code }}</span></td>
                                <td data-label="Nama">{{ $category->name }}</td>
                                <td data-label="Catatan">{{ $category->description ?: '-' }}</td>
                                <td data-label="Total Asset">{{ $category->items_count }}</td>
                                <td data-label="Aksi">
                                    <div class="toolbar__actions">
                                        <a href="{{ route('categories.show', $category) }}" class="btn btn--secondary">Detail</a>
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn--secondary">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
