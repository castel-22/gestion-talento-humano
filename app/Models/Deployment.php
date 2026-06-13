<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Deployment extends Model
{
    protected $fillable = [
        'place',
        'reason',
        'division',
        'supervisor_id',
        'start_datetime',
        'end_datetime',
        'is_indefinite',
        'status',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_indefinite'  => 'boolean',
    ];

    const STATUS_PROGRAMADO  = 'programado';
    const STATUS_EN_CURSO    = 'en_curso';
    const STATUS_FINALIZADO  = 'finalizado';
    const STATUS_CANCELADO   = 'cancelado';

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'deployment_participants')
                    ->withPivot('role', 'division', 'is_leader')
                    ->withTimestamps();
    }

    public function computeStatus(): string
    {
        if (in_array($this->status, [self::STATUS_CANCELADO, self::STATUS_FINALIZADO])) {
            return $this->status;
        }
        $now = now();
        if ($this->start_datetime && $this->start_datetime > $now) return self::STATUS_PROGRAMADO;
        if ($this->is_indefinite || !$this->end_datetime) return self::STATUS_EN_CURSO;
        if ($this->end_datetime <= $now) return self::STATUS_FINALIZADO;
        return self::STATUS_EN_CURSO;
    }

    public function refreshStatus(): void
    {
        $computed = $this->computeStatus();
        if ($computed !== $this->status && !in_array($this->status, [self::STATUS_CANCELADO, self::STATUS_FINALIZADO])) {
            $this->update(['status' => $computed]);
        }
    }
}