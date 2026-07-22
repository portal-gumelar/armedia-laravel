<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationTask extends Model
{
    protected $fillable = [
        'task_no',
        'customer_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'scheduled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            if (empty($task->task_no)) {
                $task->task_no = 'INS-' . date('ymd') . '-' . rand(1000, 9999);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
