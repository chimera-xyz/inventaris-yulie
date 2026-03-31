<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Item extends Model
{
    use SoftDeletes;

    protected array $previousValues = [];

    protected $fillable = [
        'category_id',
        'unique_code',
        'name',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'has_warranty',
        'warranty_expiry',
        'price',
        'location',
        'status',
        'notes',
        'specifications',
        'assigned_user_name',
        'assigned_division',
        'assigned_phone',
        'assigned_since',
        'qr_code_image',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'has_warranty' => 'boolean',
        'warranty_expiry' => 'date',
        'assigned_since' => 'date',
        'price' => 'decimal:2',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function logs()
    {
        return $this->hasMany(ItemLog::class)->orderBy('created_at', 'desc');
    }

    public function photos()
    {
        return $this->hasMany(ItemPhoto::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function histories()
    {
        return $this->hasMany(ItemHistory::class)
            ->orderByDesc('event_date')
            ->orderByDesc('id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class)->orderBy('created_at', 'desc');
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }

    public function loanedBy()
    {
        return $this->belongsTo(User::class, 'loaned_by');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (filled($filters['category'] ?? null)) {
            $query->where('category_id', $filters['category']);
        }

        if (filled($filters['status'] ?? null)) {
            $query->where('status', $filters['status']);
        }

        if (filled($filters['search'] ?? null)) {
            $search = trim((string) $filters['search']);

            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('unique_code', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%')
                    ->orWhere('serial_number', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'available' => '<span class="status-badge status-badge--success">Tersedia</span>',
            'in_use' => '<span class="status-badge status-badge--info">Sedang Dipakai</span>',
            'broken' => '<span class="status-badge status-badge--danger">Rusak</span>',
            'maintenance' => '<span class="status-badge status-badge--warning">Perbaikan</span>',
            'lost' => '<span class="status-badge status-badge--muted">Hilang</span>',
            default => '<span class="status-badge status-badge--muted">' . e($this->status) . '</span>',
        };
    }

    public function getQrCodeUrlAttribute(): string
    {
        return $this->buildQrCodeUrl();
    }

    public function getWarrantyStatusLabelAttribute(): string
    {
        if (! $this->has_warranty) {
            return 'Tidak ada garansi sejak pembelian';
        }

        return $this->warranty_expiry?->format('d M Y') ?: 'Tanggal garansi belum dicatat';
    }

    public function buildQrCodeUrl(?string $baseUrl = null): string
    {
        return $this->resolvePublicBaseUrl($baseUrl) . route('public.items.show', $this, false);
    }

    public function renderQrCodeSvg(?string $baseUrl = null): string
    {
        return QrCode::encoding('UTF-8')
            ->format('svg')
            ->errorCorrection('H')
            ->size(300)
            ->margin(2)
            ->generate($this->buildQrCodeUrl($baseUrl));
    }

    public function generateQRCode(): string
    {
        $previousPath = $this->qr_code_image;
        $qrCode = $this->renderQrCodeSvg();

        // Store QR code image
        $filename = "qrcodes/{$this->unique_code}.svg";
        Storage::disk('public')->put($filename, $qrCode);

        // Update item with QR code image path
        $this->update(['qr_code_image' => $filename]);

        if ($previousPath && $previousPath !== $filename) {
            Storage::disk('public')->delete($previousPath);
        }

        return Storage::url($filename);
    }

    public function resolvePublicBaseUrl(?string $baseUrl = null): string
    {
        $resolvedBaseUrl = $baseUrl;

        if (! filled($resolvedBaseUrl)) {
            $resolvedBaseUrl = config('app.public_url');
        }

        if (! filled($resolvedBaseUrl)) {
            /** @var Request|null $request */
            $request = request();

            if ($request instanceof Request) {
                $resolvedBaseUrl = $request->getSchemeAndHttpHost();
            }
        }

        if (! filled($resolvedBaseUrl)) {
            $resolvedBaseUrl = config('app.url');
        }

        return rtrim((string) $resolvedBaseUrl, '/');
    }

    public function ensureQrCodeIsAvailable(): bool
    {
        $hasCurrentQrCode = filled($this->qr_code_image)
            && Str::endsWith(Str::lower($this->qr_code_image), '.svg')
            && Storage::disk('public')->exists($this->qr_code_image);

        if ($hasCurrentQrCode) {
            return false;
        }

        $this->generateQRCode();
        $this->refresh();

        return true;
    }

    public function getQrSizeInMm(): int
    {
        $qrSize = $this->category->qr_size ?? 'large';

        return match($qrSize) {
            'small' => 10,
            'medium' => 20,
            'large' => 40,
            default => 40,
        };
    }

    public function log(string $action, ?string $field = null, ?string $oldValue = null, ?string $newValue = null, ?string $notes = null): ItemLog
    {
        return ItemLog::create([
            'item_id' => $this->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'unique_code';
    }

    protected static function booted(): void
    {
        static::created(function ($item) {
            $item->log('create', notes: 'Item baru ditambahkan');
        });

        static::updating(function ($item) {
            $item->previousValues = $item->getOriginal();
        });

        static::updated(function ($item) {
            foreach ($item->getChanges() as $field => $value) {
                if (! in_array($field, ['qr_code_image', 'updated_at'], true)) {
                    $item->log(
                        'update',
                        $field,
                        $item->previousValues[$field] ?? null,
                        is_scalar($value) ? (string) $value : json_encode($value)
                    );
                }
            }

            if (array_key_exists('status', $item->getChanges())) {
                $item->log('status_change', 'status', $item->previousValues['status'] ?? null, $item->status);
            }
        });

        static::deleted(function ($item) {
            $item->log('delete', notes: 'Item dihapus');
        });
    }
}
