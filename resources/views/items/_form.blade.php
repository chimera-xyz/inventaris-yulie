@php($item = $item ?? null)
@php($hasWarranty = filter_var(old('has_warranty', $item?->has_warranty ?? true), FILTER_VALIDATE_BOOLEAN))

<div class="section-card">
    <div class="section-header">
        <div>
            <h2 class="section-title">{{ $heading }}</h2>
            <p class="section-subtitle">{{ $description }}</p>
        </div>
    </div>

    <div class="section-body">
        <form action="{{ $action }}" method="POST" class="form-grid" enctype="multipart/form-data">
            @csrf
            @isset($method)
                @method($method)
            @endisset

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="form-field">
                    <label for="category_id" class="form-label">Kategori *</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $item?->category_id) == $category->id)>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="name" class="form-label">Nama Asset *</label>
                    <input type="text" id="name" name="name" class="form-input" maxlength="200" required
                        placeholder="Contoh: Dell Latitude 5440"
                        value="{{ old('name', $item?->name) }}">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach([
                            'available' => 'Tersedia',
                            'in_use' => 'Sedang Dipakai',
                            'broken' => 'Rusak',
                            'maintenance' => 'Perbaikan',
                            'lost' => 'Hilang',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $item?->status ?? 'available') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="form-field">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" id="brand" name="brand" class="form-input" maxlength="100"
                        placeholder="Dell, HP"
                        value="{{ old('brand', $item?->brand) }}">
                    @error('brand')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="model" class="form-label">Model</label>
                    <input type="text" id="model" name="model" class="form-input" maxlength="100"
                        placeholder="Latitude 5440"
                        value="{{ old('model', $item?->model) }}">
                    @error('model')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="serial_number" class="form-label">Serial Number</label>
                    <input type="text" id="serial_number" name="serial_number" class="form-input" maxlength="100"
                        placeholder="SN-AX34234"
                        value="{{ old('serial_number', $item?->serial_number) }}">
                    @error('serial_number')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="form-field">
                    <label for="purchase_date" class="form-label">Tanggal Beli</label>
                    <input type="date" id="purchase_date" name="purchase_date" class="form-input"
                        value="{{ old('purchase_date', $item?->purchase_date?->format('Y-m-d')) }}">
                    @error('purchase_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <span class="form-label">Garansi</span>
                    <label class="form-toggle" data-warranty-toggle>
                        <input type="hidden" name="has_warranty" value="0">
                        <input
                            type="checkbox"
                            id="has_warranty"
                            name="has_warranty"
                            value="1"
                            class="form-toggle__input"
                            data-warranty-checkbox
                            @checked($hasWarranty)
                        >
                        <span class="form-toggle__control" aria-hidden="true"></span>
                        <span class="form-toggle__content">
                            <span class="form-toggle__title" data-warranty-title>
                                {{ $hasWarranty ? 'Asset memiliki garansi' : 'Asset tidak memiliki garansi' }}
                            </span>
                        </span>
                    </label>
                    @error('has_warranty')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field" data-warranty-date-field @if(! $hasWarranty) hidden @endif>
                    <label for="warranty_expiry" class="form-label">Garansi Berakhir</label>
                    <input
                        type="date"
                        id="warranty_expiry"
                        name="warranty_expiry"
                        class="form-input"
                        value="{{ old('warranty_expiry', $item?->warranty_expiry?->format('Y-m-d')) }}"
                        data-warranty-expiry
                        data-date-disabled-label="Tidak perlu diisi"
                        @disabled(! $hasWarranty)
                    >
                    @error('warranty_expiry')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="price" class="form-label">Nilai Asset (Rp)</label>
                    <input type="number" id="price" name="price" class="form-input" step="0.01"
                        placeholder="15000000"
                        value="{{ old('price', $item?->price) }}">
                    @error('price')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="location" class="form-label">Lokasi</label>
                    <input type="text" id="location" name="location" class="form-input" maxlength="200"
                        placeholder="Ruang Server Lt.2"
                        value="{{ old('location', $item?->location) }}">
                    @error('location')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section-heading">Pengguna Saat Ini</div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="form-field">
                    <label for="assigned_user_name" class="form-label">Nama Pengguna</label>
                    <input type="text" id="assigned_user_name" name="assigned_user_name" class="form-input" maxlength="150"
                        placeholder="Nama pengguna asset"
                        value="{{ old('assigned_user_name', $item?->assigned_user_name) }}">
                    @error('assigned_user_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="assigned_division" class="form-label">Divisi</label>
                    <input type="text" id="assigned_division" name="assigned_division" class="form-input" maxlength="150"
                        placeholder="IT, Accounting"
                        value="{{ old('assigned_division', $item?->assigned_division) }}">
                    @error('assigned_division')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="assigned_phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="assigned_phone" name="assigned_phone" class="form-input" maxlength="30"
                        placeholder="08xxxx atau ext"
                        value="{{ old('assigned_phone', $item?->assigned_phone) }}">
                    @error('assigned_phone')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="assigned_since" class="form-label">Dipakai Sejak</label>
                    <input type="date" id="assigned_since" name="assigned_since" class="form-input"
                        value="{{ old('assigned_since', $item?->assigned_since?->format('Y-m-d')) }}">
                    @error('assigned_since')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section-heading">Rincian Asset</div>

            <div class="form-field">
                <label for="specifications" class="form-label">Spesifikasi Lengkap</label>
                <textarea id="specifications" name="specifications" class="form-textarea" placeholder="Tulis spesifikasi lengkap asset.">{{ old('specifications', $item?->specifications) }}</textarea>
                @error('specifications')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label for="notes" class="form-label">Catatan Internal</label>
                <textarea id="notes" name="notes" class="form-textarea" placeholder="Catatan operasional.">{{ old('notes', $item?->notes) }}</textarea>
                @error('notes')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label for="photos" class="form-label">Foto Asset</label>
                <div class="upload-picker" data-upload-picker>
                    <input
                        type="file"
                        id="photos"
                        name="photos[]"
                        class="upload-picker__input"
                        accept="image/*"
                        multiple
                        data-upload-input
                    >
                    <div class="upload-picker__header">
                        <div>
                            <div class="upload-picker__title">Pilih Foto Asset</div>
                            <div class="upload-picker__hint">Opsional. Foto besar dari HP akan diperkecil otomatis bila perlu.</div>
                        </div>
                        <label for="photos" class="upload-picker__button">Pilih Foto</label>
                    </div>
                    <div class="upload-picker__status" data-upload-status>Belum ada foto dipilih.</div>
                    <div class="upload-picker__preview" data-upload-preview hidden></div>
                </div>
                @error('photos')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                @if($errors->has('photos.*'))
                    <div class="form-error">{{ $errors->first('photos.*') }}</div>
                @endif
            </div>

            @if($item?->photos?->isNotEmpty())
                <div class="form-field">
                    <div class="photo-manage-toolbar" data-photo-manager>
                        <div>
                            <label class="form-label">Foto Saat Ini</label>
                            <div class="form-help">{{ $item->photos->count() }} foto tersimpan. Pilih foto yang ingin dihapus tanpa perlu menyimpan ulang seluruh asset.</div>
                        </div>
                        <div class="photo-manage-toolbar__actions">
                            <span class="photo-manage-toolbar__count" data-photo-selected-count>0 dipilih</span>
                            <button type="button" class="btn btn--secondary" data-photo-select-all>Pilih Semua</button>
                            <button type="button" class="btn btn--secondary" data-photo-clear>Kosongkan</button>
                            <button type="submit" class="btn btn--danger" form="item-photo-delete-form" data-photo-delete-button disabled>Hapus Terpilih</button>
                        </div>
                    </div>

                    <div class="photo-manage-grid">
                        @foreach($item->photos as $photo)
                            <label class="photo-manage-card">
                                <div class="photo-manage-card__preview">
                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="Foto {{ $item->unique_code }}">
                                </div>
                                <span class="photo-manage-card__toggle">
                                    <input
                                        type="checkbox"
                                        name="photo_ids[]"
                                        value="{{ $photo->id }}"
                                        form="item-photo-delete-form"
                                        data-photo-checkbox
                                    >
                                    Pilih
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <div class="form-help">Gunakan tombol hapus terpilih untuk menghapus foto yang dipilih.</div>
                    @error('photo_ids')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @if($errors->has('photo_ids.*'))
                        <div class="form-error">{{ $errors->first('photo_ids.*') }}</div>
                    @endif
                </div>
            @endif

            <div class="toolbar">
                <div class="toolbar__info">Field bertanda * wajib diisi. Sistem akan menyimpan jejak audit untuk perubahan asset.</div>
                <div class="toolbar__actions">
                    <a href="{{ $cancelUrl }}" class="btn btn--secondary">Batal</a>
                    <button type="submit" class="btn btn--primary">{{ $submitLabel }}</button>
                </div>
            </div>
        </form>

        @if($item?->photos?->isNotEmpty())
            <form id="item-photo-delete-form" action="{{ route('items.photos.destroy', $item) }}" method="POST">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>
