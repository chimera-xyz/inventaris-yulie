@php($category = $category ?? null)

<div class="section-card">
    <div class="section-header">
        <div>
            <h2 class="section-title">{{ $heading }}</h2>
            <p class="section-subtitle">{{ $description }}</p>
        </div>
    </div>

    <div class="section-body">
        <form action="{{ $action }}" method="POST" class="form-grid">
            @csrf
            @isset($method)
                @method($method)
            @endisset

            <div class="grid gap-4 md:grid-cols-2">
                <div class="form-field">
                    <label for="code" class="form-label">Kode Kategori *</label>
                    <input type="text" id="code" name="code" class="form-input" maxlength="10" required
                        placeholder="MON, LAP, SRV"
                        value="{{ old('code', $category?->code) }}">
                    <div class="form-help">Gunakan kode singkat dan stabil untuk pembentukan nomor asset.</div>
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="name" class="form-label">Nama Kategori *</label>
                    <input type="text" id="name" name="name" class="form-input" maxlength="100" required
                        placeholder="Monitor, Laptop, Server"
                        value="{{ old('name', $category?->name) }}">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="qr_size" class="form-label">Ukuran QR *</label>
                    <select id="qr_size" name="qr_size" class="form-input" required>
                        <option value="large" {{ old('qr_size', $category?->qr_size) === 'large' ? 'selected' : '' }}>Besar (40mm) - Monitor, Printer, Router</option>
                        <option value="medium" {{ old('qr_size', $category?->qr_size) === 'medium' ? 'selected' : '' }}>Sedang (20mm) - Keyboard, Laptop</option>
                        <option value="small" {{ old('qr_size', $category?->qr_size) === 'small' ? 'selected' : '' }}>Kecil (10mm) - Mouse, Kabel, Aksesoris</option>
                    </select>
                    <div class="form-help">Ukuran QR akan disesuaikan otomatis saat mencetak berdasarkan kategori ini.</div>
                    @error('qr_size')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="description" class="form-label">Catatan Kategori</label>
                <textarea id="description" name="description" class="form-textarea" placeholder="Isi catatan internal, ruang lingkup kategori, atau aturan khusus yang ingin Anda simpan.">{{ old('description', $category?->description) }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="toolbar">
                <div class="toolbar__info">Format kode akan dipakai untuk penomoran unik asset, misalnya `MON-2026-0001`.</div>
                <div class="toolbar__actions">
                    <a href="{{ route('categories.index') }}" class="btn btn--secondary">Batal</a>
                    <button type="submit" class="btn btn--primary">{{ $submitLabel }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
