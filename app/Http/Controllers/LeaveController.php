<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Facades\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    /**
     * Verifica si un rango de fechas se solapa con otros reposos o vacaciones del empleado.
     */
    private function hasAbsenceOverlap(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): ?string
    {
        // Solapamiento con otros reposos
        $leaveOverlap = Leave::where('employee_id', $employeeId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereIn('status', ['pendiente', 'aprobado'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })->exists();

        if ($leaveOverlap) {
            return 'El empleado ya tiene un reposo registrado en este período.';
        }

        // Solapamiento con vacaciones
        $vacationOverlap = \App\Models\Vacation::where('employee_id', $employeeId)
            ->whereIn('status', [\App\Models\Vacation::STATUS_APROBADO, \App\Models\Vacation::STATUS_EN_CURSO])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })->exists();

        if ($vacationOverlap) {
            return 'El empleado ya tiene vacaciones aprobadas en este período.';
        }

        return null;
    }
    public function index(Request $request)
    {
        $this->authorize('viewAny', Leave::class);

        // Auto-finalizar reposos aprobados cuya fecha de fin ya pasó
        Leave::whereIn('status', ['aprobado', 'en_curso'])
            ->whereDate('end_date', '<', now()->startOfDay())
            ->update(['status' => 'finalizado']);

        $query = Leave::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaves    = $query->orderBy('start_date', 'desc')->paginate(12);
        $employees = Employee::orderBy('first_name')->get();

        // KPIs para tarjetas superiores
        $stats = [
            'pendientes'  => Leave::where('status', 'pendiente')->count(),
            'en_curso'    => Leave::whereIn('status', ['aprobado', 'en_curso'])
                                  ->whereDate('start_date', '<=', now())
                                  ->whereDate('end_date', '>=', now())
                                  ->count(),
            'finalizados' => Leave::where('status', 'finalizado')->count(),
            'rechazados'  => Leave::where('status', 'rechazado')->count(),
            'total'       => Leave::count(),
        ];

        return view('leaves.index', compact('leaves', 'employees', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', Leave::class);
        return view('leaves.create');
    }

    public function store(\App\Http\Requests\StoreLeaveRequest $request)
    {
        $validated = $request->validated();

        $startDate = Carbon::parse($validated['start_date']);
        $value = (int)$validated['duration_value'];
        $unit = $validated['duration_unit'];

        switch ($unit) {
            case 'days':   $endDate = $startDate->copy()->addDays($value); break;
            case 'weeks':  $endDate = $startDate->copy()->addWeeks($value); break;
            case 'months': $endDate = $startDate->copy()->addMonths($value); break;
            default:       $endDate = $startDate;
        }

        // Validación de solapamiento
        $overlapMessage = $this->hasAbsenceOverlap($validated['employee_id'], $startDate->toDateString(), $endDate->toDateString());
        if ($overlapMessage) {
            return back()->withInput()->with('error', $overlapMessage);
        }

        $leave = Leave::create([
            'employee_id' => $validated['employee_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_value' => $value,
            'duration_unit' => $unit,
            'doctor_name' => $validated['doctor_name'],
            'issuing_institution' => $validated['issuing_institution'],
            'medical_condition' => $validated['medical_condition'],
            'status' => 'pendiente',
        ]);
        
        ActivityLogger::log('create', 'leaves', "Se registró un reposo médico para el empleado ID: {$leave->employee_id}");

        return redirect()->route('leaves.index')->with('success', 'Reposo registrado correctamente.');
    }

    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        return view('leaves.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $this->authorize('update', $leave);
        return view('leaves.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorize('update', $leave);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,weeks,months',
            'doctor_name' => 'required|string|max:255',
            'issuing_institution' => 'required|string|max:255',
            'medical_condition' => 'nullable|string',
            'status' => 'required|in:pendiente,aprobado,rechazado,finalizado',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $value = (int)$request->duration_value;
        $unit = $request->duration_unit;

        switch ($unit) {
            case 'days':   $endDate = $startDate->copy()->addDays($value); break;
            case 'weeks':  $endDate = $startDate->copy()->addWeeks($value); break;
            case 'months': $endDate = $startDate->copy()->addMonths($value); break;
            default:       $endDate = $startDate;
        }

        $leave->update([
            'employee_id' => $request->employee_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_value' => $value,
            'duration_unit' => $unit,
            'doctor_name' => $request->doctor_name,
            'issuing_institution' => $request->issuing_institution,
            'medical_condition' => $request->medical_condition,
            'status' => $request->status,
            'approved_by' => ($request->status == 'aprobado') ? Auth::id() : $leave->approved_by,
        ]);

        if ($request->status == 'aprobado') {
            $this->autoJustifyAttendances($leave);
        }

        ActivityLogger::log('update', 'leaves', "Se actualizó el reposo médico ID: {$leave->id}");

        return redirect()->route('leaves.index')->with('success', 'Reposo actualizado correctamente.');
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);
        $id = $leave->id;
        $leave->delete();
        ActivityLogger::log('delete', 'leaves', "Se eliminó el reposo médico ID: {$id}");
        return redirect()->route('leaves.index')->with('success', 'Reposo eliminado correctamente.');
    }

    public function approve(Leave $leave)
    {
        $this->authorize('update', $leave);
        $leave->update([
            'status' => 'aprobado',
            'approved_by' => Auth::id(),
        ]);
        
        $this->autoJustifyAttendances($leave);

        ActivityLogger::log('update', 'leaves', "Se aprobó el reposo médico ID: {$leave->id}");

        return redirect()->route('leaves.index')->with('success', 'Reposo aprobado.');
    }

    public function reject(Leave $leave)
    {
        $this->authorize('update', $leave);
        $leave->update([
            'status' => 'rechazado',
            'approved_by' => Auth::id(),
        ]);
        ActivityLogger::log('update', 'leaves', "Se rechazó el reposo médico ID: {$leave->id}");
        return redirect()->route('leaves.index')->with('success', 'Reposo rechazado.');
    }

    public function searchEmployeeByIdNumber(Request $request)
    {
        $idNumber = $request->get('id_number');
        $employee = Employee::where('id_number', $idNumber)->first();
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado']);
        }
        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'id_number' => $employee->id_number,
            ]
        ]);
    }

    /**
     * Justifica automáticamente las asistencias durante el período del reposo.
     */
    private function autoJustifyAttendances(Leave $leave)
    {
        if (!$leave->start_date || !$leave->end_date) return;

        $startDate = Carbon::parse($leave->start_date);
        $endDate = Carbon::parse($leave->end_date);
        
        $reason = "Reposo Médico: {$leave->medical_condition} (Dr. {$leave->doctor_name})";

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Opcional: Omitir fines de semana si la empresa no labora
            if ($date->isWeekend()) {
                continue;
            }

            $attendance = \App\Models\Attendance::firstOrNew([
                'employee_id' => $leave->employee_id,
                'date' => $date->format('Y-m-d'),
            ]);

            // Solo justificamos si no hay marca de entrada (o si ya estaba ausente)
            if (!$attendance->check_in) {
                $attendance->status = 'permission'; // Justificado
                $attendance->justification_reason = $reason;
                $attendance->save();
            }
        }
    }
}