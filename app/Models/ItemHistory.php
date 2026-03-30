<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model
{
    protected $fillable = [
        'item_id',
        'created_by',
        'event_type',
        'event_date',
        'title',
        'description',
        'responsible_party',
        'contact_phone',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {
            'maintenance' => 'Maintenance',
            'service' => 'Servis',
            'handover' => 'Penyerahan',
            'relocation' => 'Relokasi',
            'incident' => 'Insiden',
            'note' => 'Catatan',
            default => ucfirst($this->event_type),
        };
    }
}
