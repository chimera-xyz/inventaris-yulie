<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'item_id',
        'loaned_by',
        'borrower_name',
        'borrower_department',
        'loan_date',
        'return_date',
        'expected_return_date',
        'status',
        'notes',
        'signature',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'expected_return_date' => 'date',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function loanedBy()
    {
        return $this->belongsTo(User::class, 'loaned_by');
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_overdue) {
            return '<span class="status-badge status-badge--danger">Terlambat</span>';
        }

        return match($this->status) {
            'active' => '<span class="status-badge status-badge--info">Aktif</span>',
            'returned' => '<span class="status-badge status-badge--success">Dikembalikan</span>',
            'overdue' => '<span class="status-badge status-badge--danger">Terlambat</span>',
            default => '<span class="status-badge status-badge--muted">' . e($this->status) . '</span>',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status != 'active') {
            return false;
        }

        return $this->expected_return_date && $this->expected_return_date->isPast();
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->return_date) {
            if ($this->expected_return_date) {
                $days = now()->diffInDays($this->expected_return_date);
                return $days > 0 ? "$days hari tersisa" : "Hari ini";
            }
            return null;
        }

        $days = $this->return_date->diffInDays($this->loan_date);
        return "$days hari";
    }

    public function markAsReturned(?string $notes = null): void
    {
        $this->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);

        $this->item->update(['status' => 'available']);
        $message = "Dikembalikan oleh {$this->borrower_name}";

        if (filled($notes)) {
            $message .= '. ' . trim($notes);
        }

        $this->item->log('return', notes: $message);
    }

    protected static function booted(): void
    {
        static::created(function ($loan) {
            $loan->item->update(['status' => 'in_use']);
            $loan->item->log('loan', notes: "Dipinjamkan kepada {$loan->borrower_name}");
        });
    }
}
