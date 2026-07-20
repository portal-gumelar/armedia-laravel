<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_no', 'name', 'nik', 'position', 'division',
        'employment_type', 'join_date', 'end_date', 'status',
        'phone', 'email', 'address', 'photo', 'basic_salary',
        'bpjs_kes_no', 'bpjs_tk_no', 'bank_name', 'bank_account_no', 'birth_date',
    ];

    protected function casts(): array
    {
        return [
            'join_date'    => 'date',
            'end_date'     => 'date',
            'birth_date'   => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Employee $e) {
            $count = static::withTrashed()->count() + 1;
            $e->employee_no = 'EMP-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function isBirthdayThisMonth(): bool
    {
        return $this->birth_date && $this->birth_date->month === now()->month;
    }
}
