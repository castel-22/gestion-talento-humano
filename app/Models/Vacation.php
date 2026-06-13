<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'days_taken',
        'status',
        'interruption_reason',
        'approved_by',
        'remaining_days',
        'accumulated_days_used',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // Constantes de estado
    const STATUS_PENDIENTE    = 'pendiente';
    const STATUS_APROBADO     = 'aprobado';
    const STATUS_EN_CURSO     = 'en_curso';
    const STATUS_INTERRUMPIDO = 'interrumpido';
    const STATUS_FINALIZADO   = 'finalizado';
    const STATUS_RECHAZADO    = 'rechazado';
    const STATUS_REANUDADO    = 'reanudado';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Verifica si la vacación puede ser interrumpida.
     */
    public function canBeInterrupted(): bool
    {
        return in_array($this->status, [self::STATUS_APROBADO, self::STATUS_EN_CURSO]);
    }

    /**
     * Verifica si la vacación puede ser reanudada.
     * Requisitos:
     * - Estado interrumpido
     * - Tener días restantes (> 0)
     * - No haber pasado más de 72 horas desde la interrupción
     */
    public function canBeResumed(): bool
    {
        if ($this->status !== self::STATUS_INTERRUMPIDO || $this->remaining_days <= 0) {
            return false;
        }

        // Calcular horas transcurridas desde la última actualización (momento de la interrupción)
        $hoursSinceInterruption = $this->updated_at->diffInHours(now());

        return $hoursSinceInterruption <= 72;
    }
}