@extends('layouts.app')

@section('title', 'Assets - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', 'Assets')
@section('page-subtitle', 'Daftar inventaris.')

@section('page-actions')
    <a href="{{ route('items.create') }}" class="btn btn--primary">Tambah Asset</a>
@endsection

@section('content')
    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Filter</h2>
                <p class="section-subtitle">Cari berdasarkan nama, kode, serial number, brand, model, atau lokasi.</p>
            </div>
            <div class="toolbar__info">{{ $items->total() }} hasil</div>
        </div>

        <div class="section-body">
            <form action="{{ route('items.index') }}" method="GET" class="form-grid">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="form-field xl:col-span-2">
                        <label for="search" class="form-label">Cari</label>
                        <input type="text" id="search" name="search" class="form-input" value="{{ $filters['search'] ?? '' }}" placeholder="Cari asset">
                    </div>
                    <div class="form-field">
                        <label for="category" class="form-label">Kategori</label>
                        <select id="category" name="category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="available" @selected(($filters['status'] ?? '') === 'available')>Tersedia</option>
                            <option value="in_use" @selected(($filters['status'] ?? '') === 'in_use')>Dipakai</option>
                            <option value="broken" @selected(($filters['status'] ?? '') === 'broken')>Rusak</option>
                            <option value="maintenance" @selected(($filters['status'] ?? '') === 'maintenance')>Perbaikan</option>
                            <option value="lost" @selected(($filters['status'] ?? '') === 'lost')>Hilang</option>
                        </select>
                    </div>
                </div>

                <div class="toolbar">
                    <div class="toolbar__info">Menampilkan {{ $items->count() }} item pada halaman ini.</div>
                    <div class="toolbar__actions">
                        <a href="{{ route('items.index') }}" class="btn btn--secondary">Reset</a>
                        <button type="submit" class="btn btn--primary">Terapkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Daftar Asset</h2>
                <p class="section-subtitle">Tampilan utama inventaris.</p>
            </div>
            <div class="selection-toolbar" data-export-manager>
                <span class="selection-toolbar__count" data-export-count>0 asset dipilih</span>
                <button type="button" class="btn btn--secondary" data-export-select-page>Pilih Halaman Ini</button>
                <button type="button" class="btn btn--secondary" data-export-clear>Hapus Pilihan</button>
                <button type="button" class="btn btn--primary" data-export-open disabled>Export Pilihan</button>
            </div>
        </div>

        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">0</div>
                <div class="empty-state__title">Belum ada asset</div>
                <div class="empty-state__text">Tambahkan kategori lalu catat asset pertama.</div>
            </div>
        @else
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pilih</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td data-label="Pilih">
                                        <input
                                            type="checkbox"
                                            value="{{ $item->id }}"
                                            data-export-checkbox
                                            aria-label="Pilih {{ $item->unique_code }}"
                                        >
                                    </td>
                                    <td data-label="Kode"><a href="{{ route('items.show', $item) }}" class="entity-stack__title">{{ $item->unique_code }}</a></td>
                                    <td data-label="Nama">
                                        <div class="entity-stack">
                                        <div class="entity-stack__title">{{ $item->name }}</div>
                                        <div class="entity-stack__meta">
                                            {{ $item->brand ?: 'Tanpa brand' }}{{ $item->model ? ' - ' . $item->model : '' }}
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kategori">{{ $item->category->name }}</td>
                                <td data-label="Status">{!! $item->status_badge !!}</td>
                                <td data-label="Lokasi">{{ $item->location ?: '-' }}</td>
                                <td data-label="Aksi">
                                    <div class="toolbar__actions">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn--secondary">Detail</a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn--secondary">Edit</a>
                                    </div>
                                </td>
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

    <div class="modal-backdrop" data-export-modal hidden>
        <div class="modal-card">
            <div class="modal-card__header">
                <div>
                    <div class="modal-card__eyebrow">Export Asset</div>
                    <h2 class="modal-card__title">Pilih Format Export</h2>
                    <div class="modal-card__count" data-export-modal-count>0 asset akan diexport.</div>
                </div>
                <button type="button" class="modal-card__close" data-export-close aria-label="Tutup">x</button>
            </div>

            <div class="export-format-grid">
                <button type="button" class="export-format-card" data-export-submit="excel">
                    <span class="export-format-card__eyebrow">Spreadsheet</span>
                    <span class="export-format-card__title">Excel</span>
                </button>

                <button type="button" class="export-format-card" data-export-submit="pdf">
                    <span class="export-format-card__eyebrow">Document Pack</span>
                    <span class="export-format-card__title">PDF</span>
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('items.export-selected') }}" method="POST" id="items-export-form">
        @csrf
        <input type="hidden" name="format" value="" data-export-format>
        <div data-export-hidden-inputs></div>
    </form>
@endsection
