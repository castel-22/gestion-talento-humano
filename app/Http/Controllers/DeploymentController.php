<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Notifications\SystemAlert;
use Illuminate\Support\Facades\Notification;

class DeploymentController extends Controller
{
    public function index(Request $request)
    {
        // Auto-actualizar estados de despliegues según fechas
        Deployment::where('status', Deployment::STATUS_PROGRAMADO ?? 'programado')
            ->whereDate('start_datetime', '<=', now())
            ->update(['status' => 'en_curso']);
        Deployment::where('status', 'en_curso')
            ->where('is_indefinite', false)
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->update(['status' => 'finalizado']);

        $query = Deployment::with(['supervisor', 'participants']);
        if ($request->filled('status'))        $query->where('status', $request->status);
        if ($request->filled('division'))      $query->where('division', 'like', '%'.$request->division.'%');
        if ($request->filled('start_date'))    $query->whereDate('start_datetime', '>=', $request->start_date);
        if ($request->filled('end_date'))      $query->whereDate('start_datetime', '<=', $request->end_date);
        if ($request->filled('supervisor_id')) $query->where('supervisor_id', $request->supervisor_id);

        $deployments = $query->orderBy('start_datetime', 'desc')->paginate(10);

        // Cargar todos los despliegues con coordenadas para el mapa (sin paginar)
        $allDeployments = Deployment::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereIn('status', ['programado', 'en_curso'])
            ->get(['id', 'place', 'latitude', 'longitude', 'status', 'division']);

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $statuses  = ['programado', 'en_curso', 'finalizado', 'cancelado'];

        // KPIs
        $stats = [
            'programados'  => Deployment::where('status', 'programado')->count(),
            'en_curso'     => Deployment::where('status', 'en_curso')->count(),
            'finalizados'  => Deployment::where('status', 'finalizado')->count(),
            'cancelados'   => Deployment::where('status', 'cancelado')->count(),
            'personal_hoy' => Deployment::where('status', 'en_curso')
                                ->with('participants')->get()
                                ->sum(fn($d) => $d->participants->count()),
        ];

        return view('deployments.index', compact('deployments', 'allDeployments', 'employees', 'statuses', 'stats'));
    }

    public function create()
    {
        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'id_number']);
        return view('deployments.create', compact('employees'));
    }

    public function store(\App\Http\Requests\StoreDeploymentRequest $request)
    {
        $validated = $request->validated();

        $participants = [];
        if (!empty($validated['participants_json'])) {
            $participants = json_decode($validated['participants_json'], true);
        } elseif (!empty($validated['participants'])) {
            foreach ($validated['participants'] as $key => $value) {
                if (is_array($value)) {
                    $participants[] = [
                        'employee_id' => $value['employee_id'] ?? $key,
                        'role'        => $value['role'] ?? null,
                        'division'    => $value['division'] ?? null,
                        'is_leader'   => ($value['is_leader'] ?? '0') == '1',
                    ];
                } else {
                    $participants[] = ['employee_id' => $value, 'role' => null, 'division' => null, 'is_leader' => false];
                }
            }
        }

        if (empty($participants)) {
            return back()->withInput()->with('error', 'Debe seleccionar al menos un participante.');
        }

        $deployment = Deployment::create([
            'place'          => $validated['place'],
            'reason'         => $validated['reason'],
            'division'       => $validated['division'],
            'supervisor_id'  => $validated['supervisor_id'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime'   => $validated['is_indefinite'] ? null : ($validated['end_datetime'] ?? null),
            'is_indefinite'  => $request->boolean('is_indefinite'),
            'notes'          => $validated['notes'] ?? null,
            'latitude'       => $validated['latitude'] ?? null,
            'longitude'      => $validated['longitude'] ?? null,
            'status'         => Deployment::STATUS_PROGRAMADO,
        ]);

        $deployment->participants()->sync(
            collect($participants)->keyBy('employee_id')->map(fn($p) => [
                'role'      => $p['role'] ?? null,
                'division'  => $p['division'] ?? null,
                'is_leader' => $p['is_leader'] ?? false,
            ])->toArray()
        );

        // Auditoría
        ActivityLogger::log('create', 'deployments', "Se inició un nuevo despliegue en: {$deployment->place}");

        // Notificar a administradores
        $admins = User::role('administrador')->get();
        Notification::send($admins, new SystemAlert(
            'Nueva Misión Operativa',
            "Despliegue iniciado en {$deployment->place} para la división {$deployment->division}.",
            'info',
            route('deployments.show', $deployment)
        ));

        return redirect()->route('deployments.index')->with('success', 'Despliegue creado correctamente.');
    }

    public function show(Deployment $deployment)
    {
        $deployment->load(['supervisor', 'participants']);
        $deployment->refreshStatus();
        return view('deployments.show', compact('deployment'));
    }

    public function edit(Deployment $deployment)
    {
        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'id_number']);
        $deployment->load('participants');
        return view('deployments.edit', compact('deployment', 'employees'));
    }

    public function update(\App\Http\Requests\StoreDeploymentRequest $request, Deployment $deployment)
    {
        $validated = $request->validated();

        $participants = [];
        if (!empty($validated['participants_json'])) {
            $participants = json_decode($validated['participants_json'], true);
        } elseif (!empty($validated['participants'])) {
            foreach ($validated['participants'] as $key => $value) {
                if (is_array($value)) {
                    $participants[] = [
                        'employee_id' => $value['employee_id'] ?? $key,
                        'role'        => $value['role'] ?? null,
                        'division'    => $value['division'] ?? null,
                        'is_leader'   => ($value['is_leader'] ?? '0') == '1',
                    ];
                } else {
                    $participants[] = ['employee_id' => $value, 'role' => null, 'division' => null, 'is_leader' => false];
                }
            }
        }

        if (empty($participants)) {
            return back()->withInput()->with('error', 'Debe seleccionar al menos un participante.');
        }

        $deployment->update([
            'place'          => $validated['place'],
            'reason'         => $validated['reason'],
            'division'       => $validated['division'],
            'supervisor_id'  => $validated['supervisor_id'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime'   => $validated['is_indefinite'] ? null : ($validated['end_datetime'] ?? null),
            'is_indefinite'  => $request->boolean('is_indefinite'),
            'notes'          => $validated['notes'] ?? null,
            'latitude'       => $validated['latitude'] ?? null,
            'longitude'      => $validated['longitude'] ?? null,
        ]);

        $deployment->participants()->sync(
            collect($participants)->keyBy('employee_id')->map(fn($p) => [
                'role'      => $p['role'] ?? null,
                'division'  => $p['division'] ?? null,
                'is_leader' => $p['is_leader'] ?? false,
            ])->toArray()
        );

        ActivityLogger::log('update', 'deployments', "Se actualizaron datos del despliegue: {$deployment->place}");

        return redirect()->route('deployments.index')->with('success', 'Despliegue actualizado.');
    }

    public function destroy(Deployment $deployment)
    {
        $place = $deployment->place;
        $deployment->delete();
        ActivityLogger::log('delete', 'deployments', "Se eliminó el despliegue programado en: {$place}");
        return redirect()->route('deployments.index')->with('success', 'Despliegue eliminado.');
    }

    public function changeStatus(Request $request, Deployment $deployment)
    {
        $validated = $request->validate(['status' => 'required|in:finalizado,cancelado']);
        $deployment->update(['status' => $validated['status']]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function pdf(Deployment $deployment)
    {
        $deployment->load(['supervisor', 'participants']);
        $pdf = Pdf::loadView('deployments.pdf', compact('deployment'));
        return $pdf->download('despliegue-'.$deployment->id.'.pdf');
    }

    public function widget()
    {
        $activeDeployments = Deployment::where('status', Deployment::STATUS_EN_CURSO)
            ->orWhere(function ($q) {
                $q->where('status', Deployment::STATUS_PROGRAMADO)
                  ->whereDate('start_datetime', '<=', now())
                  ->where(function ($q2) { $q2->where('is_indefinite', true)->orWhere('end_datetime', '>=', now()); });
            })
            ->with(['supervisor', 'participants'])
            ->orderBy('start_datetime')->limit(5)->get();
        return view('partials.deployments-widget', compact('activeDeployments'));
    }
}