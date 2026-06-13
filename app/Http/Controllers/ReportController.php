<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Exports\AttendancesExport;
use App\Exports\VacationsExport;
use App\Exports\LeavesExport;
use App\Exports\GuardsExport;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\GuardRotation;
use App\Models\Leave;
use App\Models\Vacation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PdfReport;
use App\Jobs\GenerateEmployeePdfReport;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // ─────────────────────────────────────────────
    // ÍNDICE — Estadísticas reales desde BD
    // ─────────────────────────────────────────────
    public function index()
    {
        $departments   = Department::orderBy('name')->get();
        $rotations     = GuardRotation::where('is_active', true)->orderBy('name')->get();
        $employees     = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'id_number']);

        // Métricas en tiempo real
        $stats = [
            'total_employees'    => Employee::where('status', 'activo')->count(),
            'attendances_today'  => Attendance::whereDate('date', today())->count(),
            'vacations_active'   => Vacation::whereIn('status', ['aprobado', 'en_curso'])->count(),
            'leaves_active'      => Leave::whereDate('end_date', '>=', today())->count(),
        ];

        return view('reports.index', compact('departments', 'rotations', 'employees', 'stats'));
    }

    // ─────────────────────────────────────────────
    // PERSONAL — Excel
    // ─────────────────────────────────────────────
    public function employeesExcel(Request $request)
    {
        $filters = $request->only(['department_id', 'status']);
        return Excel::download(new EmployeesExport($filters), 'personal-'.now()->format('d-m-Y').'.xlsx');
    }

    // ─────────────────────────────────────────────
    // PERSONAL — PDF Lista General
    // ─────────────────────────────────────────────
    public function employeesPdf(Request $request)
    {
        ini_set('memory_limit', '2G');
        set_time_limit(3600);
        
        $query = Employee::with(['department', 'rank']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees   = $query->orderBy('first_name')->get();
        $generatedAt = now();

        $pdf = Pdf::loadView('reports.employees-list', compact('employees', 'generatedAt'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('lista-personal-'.now()->format('d-m-Y').'.pdf');
    }

    // ─────────────────────────────────────────────
    // HOJA DE VIDA INSTITUCIONAL — PDF Individual
    // ─────────────────────────────────────────────
    public function employeeProfile(Employee $employee)
    {
        ini_set('memory_limit', '2G');
        set_time_limit(3600);
        
        $employee->load(['department', 'rank', 'documents', 'leaves', 'vacations', 'deployments']);

        $balance = $employee->getVacationBalance();

        $vacations = $employee->vacations()
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        $leaves = $employee->leaves()
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        $attendances = $employee->attendances()
            ->where('date', '>=', now()->subMonths(3)->toDateString())
            ->orderBy('date', 'desc')
            ->get();

        $deployments = $employee->deployments()
            ->orderBy('deployments.start_datetime', 'desc')
            ->limit(5)
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('reports.employee-profile', compact(
            'employee', 'balance', 'vacations', 'leaves', 'attendances', 'deployments', 'generatedAt'
        ))->setPaper('a4', 'portrait');

        $filename = 'hoja-vida-' . str_replace(' ', '-', strtolower($employee->full_name)) . '-' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────
    // ASISTENCIAS — Excel
    // ─────────────────────────────────────────────
    public function attendancesExcel(Request $request)
    {
        $filters = $request->only(['period', 'employee_id']);
        return Excel::download(new AttendancesExport($filters), 'asistencias-'.now()->format('d-m-Y').'.xlsx');
    }

    // ─────────────────────────────────────────────
    // ASISTENCIAS — PDF
    // ─────────────────────────────────────────────
    public function attendancesPdf(Request $request)
    {
        ini_set('memory_limit', '2G');
        set_time_limit(3600);
        
        $query = Attendance::with('employee');

        if ($request->filled('period')) {
            $date = Carbon::parse($request->period);
            $query->whereYear('date', $date->year)->whereMonth('date', $date->month);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->orderBy('date', 'desc')->get();
        $generatedAt = now();
        $period      = $request->filled('period') ? Carbon::parse($request->period)->format('F Y') : 'Todos los períodos';

        $pdf = Pdf::loadView('reports.attendances-list', compact('attendances', 'generatedAt', 'period'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('asistencias-'.now()->format('d-m-Y').'.pdf');
    }

    // ─────────────────────────────────────────────
    // VACACIONES — Excel
    // ─────────────────────────────────────────────
    public function vacationsExcel(Request $request)
    {
        $filters = $request->only(['year', 'status', 'employee_id']);
        return Excel::download(new VacationsExport($filters), 'vacaciones-'.now()->format('d-m-Y').'.xlsx');
    }

    // ─────────────────────────────────────────────
    // REPOSOS — Excel
    // ─────────────────────────────────────────────
    public function leavesExcel(Request $request)
    {
        $filters = $request->only(['year', 'employee_id']);
        return Excel::download(new LeavesExport($filters), 'reposos-'.now()->format('d-m-Y').'.xlsx');
    }

    // ─────────────────────────────────────────────
    // GUARDIAS — Excel
    // ─────────────────────────────────────────────
    public function guardsExcel(Request $request)
    {
        $filters = $request->only(['rotation_id', 'month', 'year']);
        if (empty($filters['year'])) {
            $filters['year'] = now()->year;
        }
        return Excel::download(new GuardsExport($filters), 'guardias-'.now()->format('d-m-Y').'.xlsx');
    }

}
