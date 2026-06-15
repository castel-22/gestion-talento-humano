<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Models\ContingencyPlan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Facades\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VacationController extends Controller
{
    use AuthorizesRequests;

    // ==================== MÉTODOS PRIVADOS DE VALIDACIÓN ====================

    /**
     * Verifica si un rango de fechas entra en conflicto con algún plan de contingencia.
     */
    private function hasContingencyConflict(string $startDate, string $endDate): bool
    {
        return ContingencyPlan::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        })->exists();
    }

    /**
     * Verifica si un rango de fechas se solapa con vacaciones activas del mismo empleado.
     *
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeId  ID de la vacación a excluir (para updates)
     */
    private function hasVacationOverlap(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        return Vacation::where('employee_id', $employeeId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereIn('status', [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })->exists();
    }

    /**
     * Listado de solicitudes de vacaciones con filtros.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Vacation::class);

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'id_number']);
        $year = $request->year;

        // Pendientes
        $pendingQuery = Vacation::with(['employee', 'approver'])
            ->where('status', Vacation::STATUS_PENDIENTE);
        if ($request->filled('employee_id')) {
            $pendingQuery->where('employee_id', $request->employee_id);
        }
        if ($year) {
            $pendingQuery->whereYear('start_date', $year);
        }
        $pendingVacations = $pendingQuery->orderBy('start_date', 'desc')->paginate(10, ['*'], 'pending_page');

        // Aprobadas (aprobado + en_curso)
        $approvedQuery = Vacation::with(['employee', 'approver'])
            ->whereIn('status', [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO]);
        if ($request->filled('employee_id')) {
            $approvedQuery->where('employee_id', $request->employee_id);
        }
        if ($year) {
            $approvedQuery->whereYear('start_date', $year);
        }
        $approvedVacations = $approvedQuery->orderBy('start_date', 'desc')->paginate(10, ['*'], 'approved_page');

        // Pausadas/Reanudadas (interrumpido + reanudado)
        $pausedQuery = Vacation::with(['employee', 'approver'])
            ->whereIn('status', [Vacation::STATUS_INTERRUMPIDO, Vacation::STATUS_REANUDADO]);
        if ($request->filled('employee_id')) {
            $pausedQuery->where('employee_id', $request->employee_id);
        }
        if ($year) {
            $pausedQuery->whereYear('start_date', $year);
        }
        $pausedVacations = $pausedQuery->orderBy('start_date', 'desc')->paginate(10, ['*'], 'paused_page');

        // Vacaciones en curso (widget)
        $ongoingVacations = Vacation::with('employee')
            ->whereIn('status', [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderBy('end_date')
            ->get();

        // Planes de contingencia (bloqueos)
        $contingencyPlans = ContingencyPlan::orderBy('start_date')->get();

        return view('vacations.index', compact(
            'employees',
            'pendingVacations',
            'approvedVacations',
            'pausedVacations',
            'ongoingVacations',
            'contingencyPlans'
        ));
    }

    /**
     * Formulario para crear nueva solicitud.
     */
    public function create()
    {
        $this->authorize('create', Vacation::class);
        $employees = Employee::where('status', 'activo')->orderBy('first_name')->get();
        $contingencyPlans = \App\Models\ContingencyPlan::orderBy('start_date')->get();
        return view('vacations.create', compact('employees', 'contingencyPlans'));
    }

    /**
     * Guardar nueva solicitud.
     */
    public function store(\App\Http\Requests\StoreVacationRequest $request)
    {
        $validated = $request->validated();

        $employee = Employee::findOrFail($validated['employee_id']);
        $balances = $employee->getVacationBalance();

        $regularDaysToTake = (int) $request->input('regular_days_to_take', 0);
        $accumulatedDaysToTake = (int) $request->input('accumulated_days_to_take', 0);
        $totalDaysTaken = $regularDaysToTake + $accumulatedDaysToTake;

        // Validar contra contingencias
        if ($this->hasContingencyConflict($validated['start_date'], $validated['end_date'])) {
            $message = 'Las fechas coinciden con un plan de contingencia bloqueado.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        // Validar saldo regular
        if ($regularDaysToTake > $balances['regular_available']) {
            $message = "Los días regulares solicitados ({$regularDaysToTake}) superan los disponibles ({$balances['regular_available']}).";
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        // Validar saldo acumulado
        if ($accumulatedDaysToTake > $balances['accumulated_available']) {
            $message = "Los días acumulados solicitados ({$accumulatedDaysToTake}) superan los disponibles ({$balances['accumulated_available']}).";
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        // Validar solapamiento con vacaciones activas del mismo empleado
        if ($this->hasVacationOverlap($validated['employee_id'], $validated['start_date'], $validated['end_date'])) {
            $message = 'El empleado ya tiene vacaciones aprobadas o en curso en ese período.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        $vacation = Vacation::create([
            'employee_id' => $validated['employee_id'],
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'days_taken'  => $totalDaysTaken,
            'accumulated_days_used' => $accumulatedDaysToTake,
            'status'      => Vacation::STATUS_PENDIENTE,
        ]);

        ActivityLogger::log('create', 'vacations', "Se registró una solicitud de vacaciones para el empleado ID: {$vacation->employee_id}");

        // Notificar a administradores
        $admins = \App\Models\User::role('administrador')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemAlert(
            'Nueva Solicitud de Vacaciones',
            "El integrante {$employee->full_name} ha solicitado {$totalDaysTaken} días.",
            'vacation',
            route('vacations.index')
        ));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('vacations.index'),
                'message' => 'Solicitud creada correctamente.'
            ]);
        }

        return redirect()->route('vacations.index')->with('success', 'Solicitud creada.');
    }

    /**
     * Mostrar detalle.
     */
    public function show(Vacation $vacation)
    {
        $this->authorize('view', $vacation);
        return view('vacations.show', compact('vacation'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Vacation $vacation)
    {
        $this->authorize('update', $vacation);
        $employees = Employee::orderBy('first_name')->get();
        $contingencyPlans = \App\Models\ContingencyPlan::orderBy('start_date')->get();
        return view('vacations.edit', compact('vacation', 'employees', 'contingencyPlans'));
    }

    /**
     * Actualizar solicitud.
     */
    public function update(Request $request, Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'regular_days_to_take' => 'required|integer|min:0',
            'accumulated_days_to_take' => 'required|integer|min:0',
        ]);

        $regularDaysToTake = (int) $request->input('regular_days_to_take', 0);
        $accumulatedDaysToTake = (int) $request->input('accumulated_days_to_take', 0);
        $totalDaysTaken = $regularDaysToTake + $accumulatedDaysToTake;

        if ($totalDaysTaken <= 0) {
            return back()->withInput()->withErrors(['days_total' => 'Debe solicitar al menos 1 día de vacaciones en total.']);
        }

        $employee = $vacation->employee;
        $balances = $employee->getVacationBalance();

        // Validar contingencias
        if ($this->hasContingencyConflict($validated['start_date'], $validated['end_date'])) {
            $message = 'Las fechas coinciden con un plan de contingencia bloqueado.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        // Si se está editando una vacación que ya afecta el saldo, devolvemos temporalmente esos días al balance
        if (in_array($vacation->status, [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO])) {
            $regRequested = max(0, $vacation->days_taken - ($vacation->accumulated_days_used ?? 0));
            $balances['regular_available'] += $regRequested;
            $balances['accumulated_available'] += ($vacation->accumulated_days_used ?? 0);
        }

        if ($regularDaysToTake > $balances['regular_available']) {
            $message = "Los días regulares solicitados ({$regularDaysToTake}) superan los disponibles ({$balances['regular_available']}).";
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        if ($accumulatedDaysToTake > $balances['accumulated_available']) {
            $message = "Los días acumulados solicitados ({$accumulatedDaysToTake}) superan los disponibles ({$balances['accumulated_available']}).";
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        // Validar solapamiento
        if ($this->hasVacationOverlap($employee->id, $validated['start_date'], $validated['end_date'], $vacation->id)) {
            $message = 'El empleado ya tiene vacaciones aprobadas o en curso en ese período.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 422);
            }
            return back()->withInput()->with('error', $message);
        }

        $vacation->update([
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days_taken' => $totalDaysTaken,
            'accumulated_days_used' => $accumulatedDaysToTake,
        ]);

        ActivityLogger::log('update', 'vacations', "Se actualizó la solicitud de vacaciones ID: {$vacation->id}");

        return redirect()->route('vacations.index')->with('success', 'Solicitud actualizada.');
    }

    /**
     * Eliminar solicitud.
     */
    public function destroy(Vacation $vacation)
    {
        $this->authorize('delete', $vacation);
        $id = $vacation->id;
        $vacation->delete();
        ActivityLogger::log('delete', 'vacations', "Se eliminó la solicitud de vacaciones ID: {$id}");
        return redirect()->route('vacations.index')->with('success', 'Solicitud eliminada.');
    }

    /**
     * Aprobar solicitud (individual) con revalidación.
     */
    public function approve(Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        if ($vacation->status !== Vacation::STATUS_PENDIENTE) {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        if ($this->hasContingencyConflict($vacation->start_date, $vacation->end_date)) {
            return back()->with('error', 'Las fechas ahora coinciden con un plan de contingencia bloqueado. No se puede aprobar.');
        }

        if ($this->hasVacationOverlap($vacation->employee_id, $vacation->start_date, $vacation->end_date, $vacation->id)) {
            return back()->with('error', 'El empleado ya tiene otras vacaciones en ese período. No se puede aprobar.');
        }

        $vacation->update([
            'status'      => Vacation::STATUS_APROBADO,
            'approved_by' => Auth::id(),
        ]);

        ActivityLogger::log('update', 'vacations', "Se aprobó la solicitud de vacaciones ID: {$vacation->id}");

        return back()->with('success', 'Vacaciones aprobadas.');
    }

    /**
     * Rechazar solicitud.
     */
    public function reject(Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        if ($vacation->status !== Vacation::STATUS_PENDIENTE) {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $vacation->update(['status' => Vacation::STATUS_RECHAZADO]);
        return back()->with('success', 'Vacaciones rechazadas.');
    }

    /**
     * Interrumpir vacaciones (individual) – guarda remaining_days.
     */
    public function interrupt(\App\Http\Requests\InterruptVacationRequest $request, Vacation $vacation)
    {
        $validated = $request->validated();

        $start = Carbon::parse($vacation->start_date);
        $today = Carbon::today();
        $elapsed = $today->diffInDays($start);
        $remaining = max(0, $vacation->days_taken - $elapsed);

        $vacation->update([
            'status'              => Vacation::STATUS_INTERRUMPIDO,
            'interruption_reason' => $validated['interruption_reason'],
            'remaining_days'      => $remaining,
        ]);

        return back()->with('success', 'Vacaciones interrumpidas. Días restantes: ' . $remaining);
    }

    /**
     * Muestra el formulario para reanudar una vacación interrumpida.
     */
    public function showResumeForm(Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        if (!$vacation->canBeResumed()) {
            return redirect()->route('vacations.index')->with('error', 'Esta vacación no puede ser reanudada.');
        }

        return view('vacations.resume', compact('vacation'));
    }

    /**
     * Reanudar vacaciones interrumpidas.
     */
    public function resume(Request $request, Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        if (!$vacation->canBeResumed()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Esta vacación no puede ser reanudada.'], 422);
            }
            return back()->with('error', 'Esta vacación no puede ser reanudada.');
        }

        $validated = $request->validate([
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'days_to_take' => 'required|integer|min:1|max:' . $vacation->remaining_days,
        ]);

        $daysTaken = $validated['days_to_take'];
        $daysLeft = $vacation->remaining_days - $daysTaken;

        // Calcular de los días que reanuda, cuántos corresponden a acumulados y regulares.
        // La regla fue: gasta primero regulares, sobran acumulados.
        // Los días "remaining_days" contienen primero los acumulados sobrantes.
        // Ejemplo: Pidió 10 reg y 5 acc (15). Gastó 12. 
        // 10 reg y 2 acc. Quedaron 3 acc. (remaining_days = 3).
        // Por lo tanto, los remaining_days siempre están compuestos por los días que pidio menos los que gastó.
        $totalOriginal = $vacation->days_taken;
        $accOriginal = $vacation->accumulated_days_used ?? 0;
        $regOriginal = max(0, $totalOriginal - $accOriginal);

        $spentTotal = $totalOriginal - $vacation->remaining_days;
        $regSpent = min($spentTotal, $regOriginal);
        $accSpent = max(0, $spentTotal - $regOriginal);

        $regRemaining = max(0, $regOriginal - $regSpent);
        $accRemaining = max(0, $accOriginal - $accSpent);

        // De los daysTaken ahora (al reanudar), gastamos los remaining. Como la regla original,
        // al reanudar se sigue la misma lógica: gastar lo regular primero, luego acumulado.
        $newRegToTake = min($daysTaken, $regRemaining);
        $newAccToTake = max(0, $daysTaken - $newRegToTake);

        // Crear nueva solicitud con los días elegidos
        Vacation::create([
            'employee_id' => $vacation->employee_id,
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'days_taken'  => $daysTaken,
            'accumulated_days_used' => $newAccToTake,
            'status'      => Vacation::STATUS_PENDIENTE,
        ]);

        // Si aún quedan días, la vacación original sigue interrumpida con los días actualizados.
        // Si se usaron todos, se marca como reanudada.
        if ($daysLeft > 0) {
            $vacation->update(['remaining_days' => $daysLeft]);
        } else {
            $vacation->update([
                'status'         => Vacation::STATUS_REANUDADO,
                'remaining_days' => 0,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reanudación solicitada correctamente.'
            ]);
        }

        return redirect()->route('vacations.index')->with('success', 'Reanudación solicitada correctamente.');
    }

    /**
     * Finalizar vacaciones manualmente.
     */
    public function finalize(Vacation $vacation)
    {
        $this->authorize('update', $vacation);

        if (!in_array($vacation->status, [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO])) {
            return back()->with('error', 'Solo se pueden finalizar vacaciones aprobadas o en curso.');
        }

        $vacation->update(['status' => Vacation::STATUS_FINALIZADO]);
        return back()->with('success', 'Vacaciones finalizadas.');
    }

    // ==================== ACCIONES MASIVAS ====================

    public function massApprove(Request $request)
    {
        $this->authorize('update', Vacation::class);

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No se seleccionaron solicitudes.');
        }

        $approvedCount = 0;
        $vacations = Vacation::whereIn('id', $ids)->where('status', Vacation::STATUS_PENDIENTE)->get();

        foreach ($vacations as $vacation) {
            if ($this->hasContingencyConflict($vacation->start_date, $vacation->end_date)) {
                continue;
            }
            if ($this->hasVacationOverlap($vacation->employee_id, $vacation->start_date, $vacation->end_date, $vacation->id)) {
                continue;
            }
            $vacation->update([
                'status'      => Vacation::STATUS_APROBADO,
                'approved_by' => Auth::id(),
            ]);
            $approvedCount++;
        }

        return back()->with('success', "{$approvedCount} solicitudes aprobadas masivamente.");
    }

    public function massReject(Request $request)
    {
        $this->authorize('update', Vacation::class);

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No se seleccionaron solicitudes.');
        }

        $count = Vacation::whereIn('id', $ids)->where('status', Vacation::STATUS_PENDIENTE)
                    ->update(['status' => Vacation::STATUS_RECHAZADO]);

        return back()->with('success', "{$count} solicitudes rechazadas masivamente.");
    }

    public function massInterrupt(Request $request)
    {
        $this->authorize('update', Vacation::class);

        $ids = $request->input('ids', []);
        $reason = $request->input('interruption_reason');

        if (empty($ids)) {
            return back()->with('error', 'No se seleccionaron solicitudes.');
        }
        if (empty($reason)) {
            return back()->with('error', 'Debe especificar un motivo de interrupción.');
        }

        $count = 0;
        $vacations = Vacation::whereIn('id', $ids)->get();

        foreach ($vacations as $vacation) {
            if ($vacation->canBeInterrupted()) {
                $start = Carbon::parse($vacation->start_date);
                $today = Carbon::today();
                $elapsed = $today->diffInDays($start);
                $remaining = max(0, $vacation->days_taken - $elapsed);

                $vacation->update([
                    'status'              => Vacation::STATUS_INTERRUMPIDO,
                    'interruption_reason' => $reason,
                    'remaining_days'      => $remaining,
                ]);
                $count++;
            }
        }

        return back()->with('success', "{$count} vacaciones interrumpidas masivamente.");
    }

    // ==================== GESTIÓN DE CONTINGENCIAS ====================

    public function contingencies(Request $request)
    {
        $this->authorize('viewAny', ContingencyPlan::class);

        $plans = ContingencyPlan::orderBy('start_date')->get();

        if ($request->wantsJson()) {
            return response()->json($plans);
        }

        return view('vacations.contingencies', compact('plans'));
    }

    public function storeContingency(Request $request)
    {
        $this->authorize('create', ContingencyPlan::class);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        ContingencyPlan::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Plan creado.']);
        }

        return redirect()->route('vacations.contingencies')->with('success', 'Plan de contingencia creado.');
    }

    public function updateContingency(Request $request, ContingencyPlan $contingencyPlan)
    {
        $this->authorize('update', $contingencyPlan);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $contingencyPlan->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Plan actualizado.']);
        }

        return redirect()->route('vacations.contingencies')->with('success', 'Plan actualizado.');
    }

    public function destroyContingency(ContingencyPlan $contingencyPlan)
    {
        $this->authorize('delete', $contingencyPlan);
        $contingencyPlan->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('vacations.contingencies')->with('success', 'Plan eliminado.');
    }

    /**
     * Calendario general de vacaciones.
     */
    public function calendar()
    {
        return view('vacations.calendar');
    }

    /**
     * API para FullCalendar.
     */
    public function calendarEvents(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $vacations = Vacation::with('employee')
            ->whereIn('status', [Vacation::STATUS_APROBADO, Vacation::STATUS_EN_CURSO])
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();

        $events = [];

        foreach ($vacations as $vac) {
            $events[] = [
                'id'    => 'vac_' . $vac->id,
                'title' => '🏖️ ' . $vac->employee->full_name,
                'start' => $vac->start_date->format('Y-m-d'),
                'end'   => $vac->end_date->addDay()->format('Y-m-d'), // +1 día para que se vea bien en FullCalendar
                'color' => $vac->status === Vacation::STATUS_EN_CURSO ? '#F97316' : '#0B3B5E',
                'url'   => route('vacations.show', $vac->id),
            ];
        }

        // También incluir planes de contingencia
        $plans = \App\Models\ContingencyPlan::where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();

        foreach ($plans as $plan) {
            $events[] = [
                'id'    => 'plan_' . $plan->id,
                'title' => '🚨 BLOQUEO: ' . $plan->name,
                'start' => $plan->start_date->format('Y-m-d'),
                'end'   => $plan->end_date->addDay()->format('Y-m-d'),
                'color' => '#E63946', // Rojo para contingencia
                'display' => 'background',
                'allDay' => true,
            ];
            // También añadirlo como evento normal para que salga en la lista y tenga etiqueta
            $events[] = [
                'id'    => 'plan_label_' . $plan->id,
                'title' => '🚨 ' . $plan->name,
                'start' => $plan->start_date->format('Y-m-d'),
                'end'   => $plan->end_date->addDay()->format('Y-m-d'),
                'color' => '#E63946',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'plan',
                    'description' => $plan->description
                ]
            ];
        }

        return response()->json($events);
    }
}