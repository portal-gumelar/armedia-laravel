<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $fillable = [
        'name',
        'kecamatan',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function odps(): HasMany
    {
        return $this->hasMany(Odp::class);
    }

    public function csrContributions(): HasMany
    {
        return $this->hasMany(CsrContribution::class);
    }
}
