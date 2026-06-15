<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendancesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ActivityLogger;

class AttendanceController extends Controller
{
    /**
     * Listado de asistencias con filtros.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $statusMap = [
                'puntual' => 'present',
                'tardanza' => 'late',
                'ausente' => 'absent',
                'justificado' => 'permission'
            ];
            if (array_key_exists($request->status, $statusMap)) {
                $query->where('status', $statusMap[$request->status]);
            }
        }

        $attendances = $query->orderBy('date', 'desc')
                             ->orderBy('check_in', 'asc')
                             ->paginate(15);

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'id_number']);

        // KPIs globales (no filtrados) usando los enums reales de BD
        $statsBase = Attendance::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'present'    THEN 1 ELSE 0 END) as puntuales,
            SUM(CASE WHEN status = 'late'       THEN 1 ELSE 0 END) as tardanzas,
            SUM(CASE WHEN status = 'absent'     THEN 1 ELSE 0 END) as ausentes,
            SUM(CASE WHEN status = 'permission' THEN 1 ELSE 0 END) as justificados
        ")->first();

        $stats = [
            'total'        => $statsBase->total ?? 0,
            'puntuales'    => $statsBase->puntuales ?? 0,
            'tardanzas'    => $statsBase->tardanzas ?? 0,
            'ausentes'     => $statsBase->ausentes ?? 0,
            'justificados' => $statsBase->justificados ?? 0,
            'hoy'          => Attendance::whereDate('date', today())->count(),
        ];

        return view('attendances.index', compact('attendances', 'employees', 'stats'));
    }

    /**
     * Ver detalle de una asistencia.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Eliminar un registro de asistencia.
     */
    public function destroy(Attendance $attendance)
    {
        $id = $attendance->id;
        $attendance->delete();

        ActivityLogger::log('delete', 'attendances', "Se eliminó el registro de asistencia ID: {$id}");

        return redirect()->route('attendances.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Justificar una ausencia o tardanza.
     */
    public function justify(Request $request, Attendance $attendance)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $attendance->status = 'permission'; // Enum for 'justificado'
        $attendance->justification_reason = $request->reason;
        $attendance->save();

        ActivityLogger::log('update', 'attendances', "Se justificó la asistencia ID: {$attendance->id} - Motivo: {$request->reason}");

        return redirect()->route('attendances.index')
            ->with('success', 'Asistencia justificada correctamente.');
    }

    /**
     * Exportar asistencias a Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['employee_id', 'start_date', 'end_date', 'status', 'period']);
        return Excel::download(new AttendancesExport($filters), 'asistencias-' . now()->format('d-m-Y') . '.xlsx');
    }

    /**
     * Exportar asistencias a PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('exports.attendances_pdf', compact('attendances'));
        return $pdf->download('asistencias.pdf');
    }

    /**
     * Buscar empleado por cédula para el componente de asistencia rápida.
     */
    public function search(Request $request)
    {
        $idNumber = $request->get('id_number');
        $numericId = preg_replace('/[^0-9]/', '', $idNumber); // Solo números

        $employee = Employee::where('id_number', $idNumber)
                            ->orWhere('id_number', $numericId)
                            ->orWhere('id_number', 'V-' . $numericId)
                            ->orWhere('id_number', 'E-' . $numericId)
                            ->orWhere('id_number', 'LIKE', '%' . $numericId . '%')
                            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 404);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $today)
                        ->first();

        $canCheckIn  = !$attendance || !$attendance->check_in;
        $canCheckOut = $attendance && $attendance->check_in && !$attendance->check_out;

        return response()->json([
            'success' => true,
            'employee' => [
                'id'         => $employee->id,
                'full_name'  => $employee->full_name,
                'id_number'  => $employee->id_number,
                'department' => $employee->department->name ?? 'Sin departamento',
            ],
            'can_check_in'  => $canCheckIn,
            'can_check_out' => $canCheckOut,
        ]);
    }

    /**
     * Registrar entrada o salida (usado por el componente Alpine del dashboard).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:check_in,check_out',
        ]);

        $employeeId = $validated['employee_id'];
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employeeId,
            'date'        => $today,
        ]);

        if ($validated['type'] == 'check_in') {
            if ($attendance->check_in) {
                return response()->json(['success' => false, 'message' => 'Ya registró entrada hoy.']);
            }
            $attendance->check_in = $now->toTimeString();
            $attendance->status   = 'present';
            $attendance->save();

            ActivityLogger::log('create', 'attendances', "Se registró entrada del empleado ID: {$employeeId} a las {$now->format('H:i')}");

            return response()->json(['success' => true, 'message' => 'Entrada registrada a las ' . $now->format('H:i')]);
        }

        if ($validated['type'] == 'check_out') {
            if (!$attendance->check_in) {
                return response()->json(['success' => false, 'message' => 'Debe registrar entrada primero.']);
            }
            if ($attendance->check_out) {
                return response()->json(['success' => false, 'message' => 'Ya registró salida hoy.']);
            }
            $attendance->check_out = $now->toTimeString();
            $attendance->save();

            ActivityLogger::log('update', 'attendances', "Se registró salida del empleado ID: {$employeeId} a las {$now->format('H:i')}");

            return response()->json(['success' => true, 'message' => 'Salida registrada a las ' . $now->format('H:i')]);
        }

        return response()->json(['success' => false, 'message' => 'Tipo inválido.'], 400);
    }

    /**
     * Autocompletado para el listado de asistencias.
     */
    public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $employees = Employee::where('id_number', 'LIKE', "%{$term}%")
            ->orWhere('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get(['id', 'id_number', 'first_name', 'last_name']);

        $results = [];
        foreach ($employees as $emp) {
            $results[] = [
                'id'    => $emp->id,
                'label' => $emp->id_number . ' - ' . $emp->full_name,
                'value' => $emp->id_number,
            ];
        }

        return response()->json($results);
    }

    /**
     * Búsqueda de empleados para el componente de asistencia (alias de autocomplete).
     */
    public function searchEmployees(Request $request)
    {
        return $this->autocomplete($request);
    }
}