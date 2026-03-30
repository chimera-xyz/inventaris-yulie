@extends('layouts.app')

@section('title', 'Loans - Inventaris IT')
@section('page-eyebrow', 'Loan Control')
@section('page-title', 'Peminjaman Asset')
@section('page-subtitle', 'Pantau semua asset yang sedang dipinjam, sudah kembali, atau berisiko terlambat dalam satu daftar operasional.')

@section('content')
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-card__label">Aktif</div>
            <div class="metric-card__value">{{ number_format($activeLoansCount) }}</div>
            <div class="metric-card__helper">Asset yang masih berada di luar atau dipakai user.</div>
        </div>

        <div class="metric-card">
            <div class="metric-card__label">Terlambat</div>
            <div class="metric-card__value">{{ number_format($overdueLoansCount) }}</div>
            <div class="metric-card__helper">Target kembali sudah lewat namun status belum ditutup.</div>
        </div>

        <div class="metric-card">
            <div class="metric-card__label">Selesai</div>
            <div class="metric-card__value">{{ number_format($returnedLoansCount) }}</div>
            <div class="metric-card__helper">Peminjaman yang sudah dikembalikan dan ditutup.</div>
        </div>

        <div class="metric-card">
            <div class="metric-card__label">Total Record</div>
            <div class="metric-card__value">{{ number_format($loans->total()) }}</div>
            <div class="metric-card__helper">Jumlah data sesuai filter yang sedang aktif.</div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Filter Loans</h2>
                <p class="section-subtitle">Cari berdasarkan nama peminjam, departemen, nama asset, atau kode asset.</p>
            </div>
        </div>

        <div class="section-body">
            <form action="{{ route('loans.index') }}" method="GET" class="form-grid">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div class="form-field xl:col-span-2">
                        <label for="search" class="form-label">Cari</label>
                        <input type="text" id="search" name="search" class="form-input" value="{{ $search }}" placeholder="Nama peminjam, departemen, asset, atau kode">
                    </div>

                    <div class="form-field">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="active" @selected($status === 'active')>Aktif</option>
                            <option value="overdue" @selected($status === 'overdue')>Terlambat</option>
                            <option value="returned" @selected($status === 'returned')>Dikembalikan</option>
                        </select>
                    </div>
                </div>

                <div class="toolbar">
                    <div class="toolbar__info">Menampilkan {{ $loans->count() }} record pada halaman ini.</div>
                    <div class="toolbar__actions">
                        <a href="{{ route('loans.index') }}" class="btn btn--secondary">Reset</a>
                        <button type="submit" class="btn btn--primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Daftar Peminjaman</h2>
                <p class="section-subtitle">Kontrol pengembalian dan lihat konteks peminjaman tanpa pindah halaman.</p>
            </div>
        </div>

        @if($loans->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">LO</div>
                <div class="empty-state__title">Belum ada data peminjaman</div>
                <div class="empty-state__text">Catat peminjaman dari halaman detail asset untuk mulai membangun histori penggunaan.</div>
            </div>
        @else
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Peminjam</th>
                            <th>Pinjam / Kembali</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr>
                                <td>
                                    <div class="entity-stack">
                                        <a href="{{ route('items.show', $loan->item) }}" class="entity-stack__title">{{ $loan->item->name }}</a>
                                        <div class="entity-stack__meta">{{ $loan->item->unique_code }} - {{ $loan->item->category->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="entity-stack">
                                        <div class="entity-stack__title">{{ $loan->borrower_name }}</div>
                                        <div class="entity-stack__meta">{{ $loan->borrower_department ?: 'Departemen tidak dicatat' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="entity-stack">
                                        <div class="entity-stack__title">{{ $loan->loan_date->format('d M Y') }}</div>
                                        <div class="entity-stack__meta">
                                            Target {{ $loan->expected_return_date?->format('d M Y') ?: '-' }}<br>
                                            Kembali {{ $loan->return_date?->format('d M Y') ?: '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td>{!! $loan->status_badge !!}</td>
                                <td>{{ $loan->notes ?: '-' }}</td>
                                <td>
                                    @if($loan->status === 'active')
                                        <form action="{{ route('loans.return', $loan) }}" method="POST" class="form-grid">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn--primary">Tutup</button>
                                        </form>
                                    @else
                                        <span class="chip chip--success">Closed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="section-body">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
@endsection
