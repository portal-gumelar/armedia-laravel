<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_no', 'expense_date', 'category', 'description',
        'amount', 'payment_method', 'receipt_file', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['expense_date' => 'date', 'amount' => 'decimal:2'];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Expense $e) {
            $prefix     = 'EXP-' . now()->format('ymd') . '-';
            $todayCount = static::withTrashed()->where('expense_no', 'like', $prefix . '%')->count() + 1;
            $e->expense_no = $prefix . str_pad($todayCount, 4, '0', STR_PAD_LEFT);
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
