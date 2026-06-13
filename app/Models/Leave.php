<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'duration_value',
        'duration_unit',
        'doctor_name',
        'issuing_institution',
        'medical_condition',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // ══════════════════════════════════════════
    // RELACIONES
    // ══════════════════════════════════════════

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ══════════════════════════════════════════
    // ESTADO DINÁMICO
    // ══════════════════════════════════════════

    /**
     * Calcula el estado real en función de las fechas actuales.
     * - pendiente / rechazado → se devuelven tal cual
     * - aprobado cuya start_date ya pasó y end_date no → en_curso
     * - aprobado cuya end_date ya pasó                 → finalizado
     */
    public function computeStatus(): string
    {
        if (in_array($this->status, ['pendiente', 'rechazado'])) {
            return $this->status;
        }

        $today = now()->startOfDay();

        if ($this->start_date && $this->start_date->gt($today)) {
            return 'aprobado'; // Aún no ha comenzado
        }
        if ($this->end_date && $this->end_date->lt($today)) {
            return 'finalizado';
        }
        if ($this->start_date && $this->start_date->lte($today)) {
            return 'en_curso';
        }

        return $this->status;
    }

    /**
     * Porcentaje de días transcurridos sobre el total (0–100).
     */
    public function getProgressPercentAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;

        $totalDays = max(1, $this->start_date->diffInDays($this->end_date));
        $elapsed   = max(0, min($this->start_date->diffInDays(now()->startOfDay(), false), $totalDays));

        return (int) round(($elapsed / $totalDays) * 100);
    }

    /**
     * Días transcurridos desde el inicio del reposo.
     */
    public function getDaysElapsedAttribute(): int
    {
        if (!$this->start_date) return 0;
        return max(0, (int) $this->start_date->diffInDays(now()->startOfDay(), false));
    }

    /**
     * Días restantes hasta el fin del reposo.
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date) return 0;
        return max(0, (int) now()->startOfDay()->diffInDays($this->end_date, false));
    }

    /**
     * Total de días del reposo (inicio → fin).
     */
    public function getTotalDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;
        return (int) $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Etiqueta legible de la unidad de duración.
     */
    public function getDurationLabelAttribute(): string
    {
        $labels = [
            'days'   => $this->duration_value == 1 ? 'día'  : 'días',
            'weeks'  => $this->duration_value == 1 ? 'semana' : 'semanas',
            'months' => $this->duration_value == 1 ? 'mes'  : 'meses',
        ];
        return $labels[$this->duration_unit] ?? $this->duration_unit;
    }
}