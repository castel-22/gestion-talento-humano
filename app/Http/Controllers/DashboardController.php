<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Department;
use App\Models\GuardRotation;
use App\Models\GuardDuty;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==================== TARJETAS DE RESUMEN ====================
        $totalEmployees   = Employee::count();
        $pendingVacations = Vacation::where('status', Vacation::STATUS_PENDIENTE)->count();
        $pendingLeaves    = Leave::where('status', 'pendiente')->count();
        $todayAttendances = Attendance::whereDate('date', Carbon::today())->count();
        $activeLeaves     = Leave::where('status', 'aprobado')
            ->whereDate('end_date', '>=', Carbon::today())
            ->count();

        // ==================== EMPLEADOS DE VACACIONES HOY (WIDGET) ====================
        $employeesOnVacation = Vacation::with(['employee.department'])
            ->whereIn('status', ['aprobado', 'en_curso'])
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->get();

        // ==================== GRÁFICO: EMPLEADOS POR DEPARTAMENTO ====================
        $departments      = Department::withCount('employees')->get();
        $departmentNames  = $departments->pluck('name');
        $departmentCounts = $departments->pluck('employees_count');

        // ==================== GRÁFICO: ESTADO DE EMPLEADOS ====================
        $activeEmployees   = Employee::where('status', 'activo')->count();
        $inactiveEmployees = Employee::where('status', 'inactivo')->count();
        $reposeEmployees   = Employee::where('status', 'reposo')->count();

        // ==================== GRÁFICO: ASISTENCIAS ÚLTIMOS 30 DÍAS ====================
        // Una sola query con groupBy en lugar de 30 queries separadas
        $start = Carbon::today()->subDays(29);
        $end   = Carbon::today();

        $rawAttendances = Attendance::select(
                DB::raw('DATE(date) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('date', [$start, $end])
            ->groupBy('day')
            ->pluck('total', 'day');

        // Rellenar los días sin asistencias con 0 para mantener eje continuo
        $attendanceDates  = [];
        $attendanceCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date               = Carbon::today()->subDays($i)->format('Y-m-d');
            $attendanceDates[]  = Carbon::today()->subDays($i)->format('d/m');
            $attendanceCounts[] = $rawAttendances[$date] ?? 0;
        }

        // ==================== LISTAS DINÁMICAS ====================
        $latestEmployees = Employee::with(['department', 'position'])->latest()->take(10)->get();

        $upcomingVacations = Vacation::with('employee')
            ->where('status', 'aprobado')
            ->whereDate('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $endingLeaves = Leave::with('employee')
            ->where('status', 'aprobado')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('end_date', 'asc')
            ->take(5)
            ->get();

        // ==================== ÚLTIMAS 5 ASISTENCIAS ====================
        $latestAttendances = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // ==================== GUARDIAS DEL DÍA (WIDGET) ====================
        $today = now()->toDateString();
        $activeRotations = GuardRotation::where('is_active', true)->get();
        $guardiasHoy = [];

        $activeRotationIds = $activeRotations->pluck('id');
        $duties = GuardDuty::whereIn('guard_rotation_id', $activeRotationIds)
            ->whereDate('date', $today)
            ->with(['employee.department', 'employee.position'])
            ->get()
            ->keyBy('guard_rotation_id');

        foreach ($activeRotations as $rotation) {
            $duty = $duties->get($rotation->id);
            if ($duty) {
                $guardiasHoy[] = [
                    'rotation' => $rotation->name,
                    'letter'   => $duty->letter,
                    'employee' => $duty->employee,
                    'notes'    => $duty->notes,
                ];
            }
        }

        return view('dashboard', compact(
            'totalEmployees',
            'pendingVacations',
            'pendingLeaves',
            'todayAttendances',
            'activeLeaves',
            'employeesOnVacation',
            'departmentNames',
            'departmentCounts',
            'activeEmployees',
            'inactiveEmployees',
            'reposeEmployees',
            'attendanceDates',
            'attendanceCounts',
            'latestEmployees',
            'upcomingVacations',
            'endingLeaves',
            'latestAttendances',
            'guardiasHoy'
        ));
    }
}