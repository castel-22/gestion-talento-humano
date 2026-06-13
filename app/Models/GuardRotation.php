<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuardRotation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'employee_a_id',
        'employee_b_id',
        'employee_c_id',
        'employee_d_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function duties(): HasMany
    {
        return $this->hasMany(GuardDuty::class);
    }

    public function employeeA(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_a_id');
    }

    public function employeeB(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_b_id');
    }

    public function employeeC(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_c_id');
    }

    public function employeeD(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_d_id');
    }
}