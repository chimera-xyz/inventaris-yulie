<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemLog extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'create' => 'Ditambahkan',
            'update' => 'Diupdate',
            'delete' => 'Dihapus',
            'status_change' => 'Status Berubah',
            'loan' => 'Dipinjamkan',
            'return' => 'Dikembalikan',
            'qr_generated' => 'QR Code Digenerate',
            'qr_printed' => 'QR Code Dicetak',
            'code_printed' => 'Kode Dicetak',
            default => ucfirst($this->action),
        };
    }

    public function getFieldLabelAttribute(): ?string
    {
        return match($this->field) {
            'category_id' => 'Kategori',
            'unique_code' => 'Kode Unik',
            'name' => 'Nama',
            'brand' => 'Merek',
            'model' => 'Model',
            'serial_number' => 'Nomor Seri',
            'purchase_date' => 'Tanggal Beli',
            'warranty_expiry' => 'Garansi Sampai',
            'price' => 'Harga',
            'location' => 'Lokasi',
            'status' => 'Status',
            'notes' => 'Catatan',
            'specifications' => 'Spesifikasi Lengkap',
            'assigned_user_name' => 'Pengguna',
            'assigned_division' => 'Divisi',
            'assigned_phone' => 'Nomor Telepon',
            'assigned_since' => 'Dipakai Sejak',
            default => $this->field,
        };
    }
}
