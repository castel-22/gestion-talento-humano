<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardDuty extends Model
{
    protected $fillable = [
        'guard_rotation_id',
        'date',
        'letter',
        'employee_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function rotation(): BelongsTo
    {
        return $this->belongsTo(GuardRotation::class, 'guard_rotation_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}