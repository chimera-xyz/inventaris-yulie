<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ItemPhoto extends Model
{
    protected $fillable = [
        'item_id',
        'path',
        'sort_order',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    protected static function booted(): void
    {
        static::deleted(function (ItemPhoto $photo) {
            if ($photo->path) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }
}
