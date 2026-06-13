<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Employee extends Model
{
    protected $table = 'employees';

    // ==================== CONSTANTES ====================

    const STATUS_ACTIVO   = 'activo';
    const STATUS_INACTIVO = 'inactivo';
    const STATUS_REPOSO   = 'reposo';

    const TYPE_FIJO       = 'fijo';
    const TYPE_CONTRATADO = 'contratado';
    const TYPE_COMISION   = 'comision';

    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'rank_id',
        'first_name',
        'last_name',
        'id_number',
        'birth_date',
        'birth_place',
        'marital_status',
        'address',
        'sector',
        'parish',
        'personal_phone',
        'home_phone',
        'email',
        'blood_type',
        'allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
        'education_level',
        'degree',
        'institution',
        'graduation_year',
        'currently_studying',
        'specializations',
        'employee_code',
        'hired_date',
        'position',
        'employment_type',
        'shift_group',
        'status',
        'employee_type',
        'gender',
        'institutional_code',
        'accumulated_days',  // <-- NUEVO
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hired_date' => 'date',
        'currently_studying' => 'boolean',
    ];

    // ==================== RELACIONES ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * @todo Feature futuro: módulo de horarios/turnos de trabajo.
     * La tabla work_schedules y el modelo WorkSchedule serán implementados en una fase posterior.
     */
    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function deployments(): BelongsToMany
    {
        return $this->belongsToMany(Deployment::class, 'deployment_participants')
                    ->withTimestamps();
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function guardDuties(): HasMany
    {
        return $this->hasMany(GuardDuty::class);
    }

    // ==================== ACCESORES ====================

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getWeeklyHoursAttribute(): float
    {
        $totalHours = 0;
        foreach ($this->workSchedules as $schedule) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $totalHours += $start->diffInHours($end);
        }
        return $totalHours;
    }

    public function getIsOnActiveDeploymentAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->deployments()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();
    }

    // ==================== SCOPES ====================

    /**
     * Filtra empleados activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Retorna los días de vacaciones correspondientes por antigüedad.
     */
    public function getBaseVacationDays(): int
    {
        if (!$this->hired_date) {
            return 0;
        }

        $years = Carbon::parse($this->hired_date)->diffInYears(now());

        return match (true) {
            $years >= 15 => 25,
            $years >= 10 => 21,
            $years >= 5  => 18,
            default      => 15,
        };
    }

    /**
     * Calcula los días de vacaciones disponibles separando regulares y acumulados.
     */
    public function getVacationBalance(): array
    {
        $baseDays = $this->getBaseVacationDays();
        $accumulatedTotal = $this->accumulated_days ?? 0;

        $regularUsed = 0;
        $accumulatedUsed = 0;

        $vacations = $this->vacations()
            ->whereIn('status', [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO, Vacation::STATUS_FINALIZADO, Vacation::STATUS_INTERRUMPIDO])
            ->get();

        foreach ($vacations as $vacation) {
            // Lo solicitado originalmente en la vacación
            $totalRequested = $vacation->days_taken;
            $accRequested = $vacation->accumulated_days_used ?? 0;
            $regRequested = max(0, $totalRequested - $accRequested);

            // Lo realmente consumido (si se interrumpió, es menos)
            $taken = $totalRequested;
            if ($vacation->status === Vacation::STATUS_INTERRUMPIDO) {
                $taken = max(0, $totalRequested - $vacation->remaining_days);
            }

            // Consumo: Se gastan primero los regulares, luego los acumulados.
            // Por lo tanto, al interrumpir se devuelven primero los acumulados.
            $actualRegUsed = min($taken, $regRequested);
            $actualAccUsed = max(0, $taken - $regRequested);

            $regularUsed += $actualRegUsed;
            $accumulatedUsed += $actualAccUsed;
        }

        $regularAvailable = max(0, $baseDays - $regularUsed);
        $accumulatedAvailable = max(0, $accumulatedTotal - $accumulatedUsed);

        return [
            'base_days' => $baseDays,
            'regular_used' => $regularUsed,
            'regular_available' => $regularAvailable,
            'accumulated_total' => $accumulatedTotal,
            'accumulated_used' => $accumulatedUsed,
            'accumulated_available' => $accumulatedAvailable,
            'total_available' => $regularAvailable + $accumulatedAvailable
        ];
    }

    /**
     * Helper para compatibilidad con código existente.
     * Retorna el total disponible sumando ambos saldos.
     */
    public function getAvailableVacationDays(): int
    {
        return $this->getVacationBalance()['total_available'];
    }
}