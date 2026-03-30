<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Helper method to preview the next sequence for the current year.
    public function getNextItemNumber(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = sprintf('%s-%d-', $this->code, $year);

        $number = $this->items()
            ->withTrashed()
            ->where('unique_code', 'like', $prefix . '%')
            ->pluck('unique_code')
            ->map(fn (string $code) => (int) str($code)->afterLast('-'))
            ->max() ?? 0;

        return str_pad($number + 1, 4, '0', STR_PAD_LEFT);
    }
}
