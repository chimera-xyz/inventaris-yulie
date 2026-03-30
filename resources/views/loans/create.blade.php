@extends('layouts.app')

@section('title', 'Catat Peminjaman - Inventaris IT')
@section('page-eyebrow', 'Loan Control')
@section('page-title', 'Catat Peminjaman')
@section('page-subtitle', 'Dokumentasikan siapa yang memegang asset, kapan mulai dipakai, dan kapan target pengembaliannya.')

@section('page-actions')
    <a href="{{ route('items.show', $item) }}" class="btn btn--secondary">Kembali ke Asset</a>
@endsection

@section('content')
    <div class="split-grid">
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Form Peminjaman</h2>
                    <p class="section-subtitle">Asset yang akan dicatat: {{ $item->name }} ({{ $item->unique_code }}).</p>
                </div>
            </div>

            <div class="section-body">
                <form action="{{ route('loans.store', $item) }}" method="POST" class="form-grid">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="form-field">
                            <label for="borrower_name" class="form-label">Nama Peminjam *</label>
                            <input type="text" id="borrower_name" name="borrower_name" class="form-input" required
                                placeholder="Nama pegawai atau PIC"
                                value="{{ old('borrower_name') }}">
                            @error('borrower_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="borrower_department" class="form-label">Departemen</label>
                            <input type="text" id="borrower_department" name="borrower_department" class="form-input"
                                placeholder="Finance, Operations, IT"
                                value="{{ old('borrower_department') }}">
                            @error('borrower_department')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="form-field">
                            <label for="loan_date" class="form-label">Tanggal Pinjam *</label>
                            <input type="date" id="loan_date" name="loan_date" class="form-input" required value="{{ old('loan_date', now()->format('Y-m-d')) }}">
                            @error('loan_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="expected_return_date" class="form-label">Target Kembali</label>
                            <input type="date" id="expected_return_date" name="expected_return_date" class="form-input" value="{{ old('expected_return_date') }}">
                            @error('expected_return_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea id="notes" name="notes" class="form-textarea" placeholder="Aksesori yang dibawa, kebutuhan kerja, atau kondisi asset saat dipinjam.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="toolbar">
                        <div class="toolbar__info">Setelah disimpan, status asset otomatis berubah menjadi dipinjam / dipakai.</div>
                        <div class="toolbar__actions">
                            <a href="{{ route('items.show', $item) }}" class="btn btn--secondary">Batal</a>
                            <button type="submit" class="btn btn--primary">Simpan Peminjaman</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="form-grid">
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Ringkasan Asset</h2>
                        <p class="section-subtitle">Pastikan asset yang dipilih memang benar sebelum peminjaman dicatat.</p>
                    </div>
                </div>

                <div class="section-body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">Asset</div>
                            <div class="detail-item__value">{{ $item->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Kode</div>
                            <div class="detail-item__value">{{ $item->unique_code }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Kategori</div>
                            <div class="detail-item__value">{{ $item->category->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Status Saat Ini</div>
                            <div class="detail-item__value">{!! $item->status_badge !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="callout">
                <div class="callout__title">Praktik yang disarankan</div>
                <ul class="space-y-2">
                    <li>Masukkan target kembali agar dashboard bisa mendeteksi keterlambatan.</li>
                    <li>Isi catatan bila asset keluar bersama charger, adaptor, atau lisensi tambahan.</li>
                    <li>Tutup peminjaman segera saat asset kembali untuk menjaga data tetap akurat.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
