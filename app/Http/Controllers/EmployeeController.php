<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Position;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\ActivityLogger;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Genera un código de empleado único con formato EMP + Año + Número correlativo.
     * Usa una transacción con lockForUpdate para evitar race conditions.
     */
    private function generateEmployeeCode(): string
    {
        return DB::transaction(function () {
            $lastEmployee = Employee::lockForUpdate()->latest('id')->first();
            $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
            return 'EMP' . date('Y') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Retorna los datos comunes necesarios para los formularios de empleados.
     */
    private function getFormData(): array
    {
        return [
            'bloodTypes'      => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'educationLevels' => ['Primaria', 'Secundaria', 'Técnico Superior', 'Universitario', 'Postgrado', 'Maestría', 'Doctorado'],
            'departments'     => Department::all(),
            'positions'       => Position::orderBy('order')->get(),
            'ranks'           => Rank::orderBy('order')->get(),
        ];
    }

    /**
     * Determina si el usuario autenticado puede ver/editar el campo "user_id".
     */
    private function canSeeUserField()
    {
        if (Auth::user()->hasRole('administrador')) {
            return true;
        }
        $currentEmployee = Auth::user()->employee;
        if ($currentEmployee && $currentEmployee->department && 
            stripos($currentEmployee->department->name, 'personal') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Busca un empleado por número de cédula (endpoint API interno).
     * Devuelve datos básicos del empleado y sus días de vacaciones disponibles.
     */
    public function findByIdNumber(string $idNumber)
    {
        $employee = Employee::with('department')
            ->where('id_number', trim($idNumber))
            ->first();

        if (! $employee) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }

        $balance = $employee->getVacationBalance();

        return response()->json([
            'id'             => $employee->id,
            'full_name'      => $employee->full_name,
            'hired_date'     => $employee->hired_date?->format('Y-m-d'),
            'available_days' => $balance['total_available'],
            'regular_available' => $balance['regular_available'],
            'accumulated_available' => $balance['accumulated_available'],
            'department'     => $employee->department?->name,
            'position'       => $employee->position,
        ]);
    }

    /**
     * Autocompletado AJAX para búsqueda de empleados por cédula o nombre.
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
        foreach ($employees as $employee) {
            $results[] = [
                'id' => $employee->id,
                'label' => $employee->id_number . ' - ' . $employee->full_name,
                'value' => $employee->id_number,
            ];
        }
        return response()->json($results);
    }

    /**
     * Listado de empleados con búsqueda, ordenamiento y paginación.
     * Aplica políticas de autorización (viewAny).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        $query = Employee::with(['department', 'position', 'rank']);

        // Búsqueda por cédula o nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_number', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Ordenamiento según parámetro 'sort'
        $sort = $request->get('sort', 'id_desc');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'id_number_asc':
                $query->orderBy('id_number', 'asc');
                break;
            case 'id_number_desc':
                $query->orderBy('id_number', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }

        $employees = $query->paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * Muestra el formulario de creación de empleado.
     * Carga datos necesarios: código automático, tipos de sangre, niveles educativos, etc.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Employee::class);

        $employeeCode = $this->generateEmployeeCode();
        $formData = $this->getFormData();

        // Usuarios disponibles para asignar cuenta
        $personalDepartment = Department::where('name', 'like', '%personal%')->first();
        if ($personalDepartment) {
            $users = User::whereHas('employee', function ($query) use ($personalDepartment) {
                $query->where('department_id', $personalDepartment->id);
            })->orDoesntHave('employee')->get();
        } else {
            $users = User::doesntHave('employee')->get();
        }

        $showUserField = $this->canSeeUserField();

        return view('employees.create', array_merge($formData, [
            'employeeCode' => $employeeCode,
            'users' => $users,
            'showUserField' => $showUserField,
        ]));
    }

    /**
     * Almacena un nuevo empleado en la base de datos.
     * Valida todos los campos, incluyendo documentos.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(\App\Http\Requests\StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        // Si no se proporcionó código, generar uno
        if (empty($validated['employee_code'])) {
            $validated['employee_code'] = $this->generateEmployeeCode();
        }

        $employee = Employee::create($validated);

        ActivityLogger::log('create', 'employees', "Se creó el expediente del empleado: {$employee->full_name} ({$employee->id_number})", $employee->toArray());

        // Guardar documentos nuevos
        if ($request->has('new_documents')) {
            foreach ($request->file('new_documents') as $docData) {
                $file = $docData['file'];
                $path = $file->store('employee_documents', 'public');
                $employee->documents()->create([
                    'title' => $docData['title'],
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'document_type' => $docData['type'],
                    'description' => $docData['description'] ?? null,
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Empleado creado correctamente.');
    }

    /**
     * Muestra los detalles de un empleado específico.
     *
     * @param Employee $employee
     * @return \Illuminate\View\View
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);
        $employee->load(['department', 'user', 'documents', 'position', 'rank']);
        return view('employees.show', compact('employee'));
    }

    /**
     * Muestra el formulario de edición general (aunque se usa más la edición por secciones).
     *
     * @param Employee $employee
     * @return \Illuminate\View\View
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee);

        $formData = $this->getFormData();

        $personalDepartment = Department::where('name', 'like', '%personal%')->first();
        if ($personalDepartment) {
            $users = User::whereHas('employee', function ($query) use ($personalDepartment) {
                $query->where('department_id', $personalDepartment->id);
            })->orDoesntHave('employee')->get();
        } else {
            $users = User::doesntHave('employee')->get();
        }

        $showUserField = $this->canSeeUserField();

        return view('employees.edit', array_merge($formData, [
            'employee' => $employee,
            'users' => $users,
            'showUserField' => $showUserField,
        ]));
    }

    /**
     * Actualiza los datos generales del empleado (poco usado; se prefiere update por secciones).
     *
     * @param Request $request
     * @param Employee $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\App\Http\Requests\UpdateEmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();

        $oldData = $employee->toArray();
        $employee->update($validated);

        ActivityLogger::log('update', 'employees', "Se actualizaron datos generales del empleado: {$employee->full_name}", [
            'old' => $oldData,
            'new' => $employee->fresh()->toArray()
        ]);

        // Manejo de documentos: nuevos y eliminación
        if ($request->has('new_documents')) {
            foreach ($request->file('new_documents') as $docData) {
                $file = $docData['file'];
                $path = $file->store('employee_documents', 'public');
                $employee->documents()->create([
                    'title' => $docData['title'],
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'document_type' => $docData['type'],
                    'description' => $docData['description'] ?? null,
                ]);
            }
        }

        if ($request->has('delete_documents')) {
            $employee->documents()->whereIn('id', $request->delete_documents)->get()->each(function ($doc) {
                Storage::disk('public')->delete($doc->file_path);
                $doc->delete();
            });
        }

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Elimina un empleado (soft delete o hard, según configuración del modelo).
     *
     * @param Employee $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);
        foreach ($employee->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }
        ActivityLogger::log('delete', 'employees', "Se eliminó el expediente del empleado: {$employee->full_name} ({$employee->id_number})");
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Empleado eliminado correctamente.');
    }

    // ==================== MÉTODOS DE EDICIÓN POR SECCIONES ====================

    /**
     * Edita solo los datos personales del empleado.
     */
    public function editPersonal(Employee $employee)
    {
        $this->authorize('update', $employee);
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        return view('employees.edit-personal', compact('employee', 'bloodTypes'));
    }

    /**
     * Actualiza los datos personales.
     */
    public function updatePersonal(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'required|string|unique:employees,id_number,' . $employee->id,
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:soltero,casado,divorciado,viudo,otro',
            'address' => 'nullable|string',
            'sector' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'personal_phone' => 'nullable|string|max:20',
            'home_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'Datos personales actualizados.');
    }

    /**
     * Edita solo los datos académicos.
     */
    public function editAcademic(Employee $employee)
    {
        $this->authorize('update', $employee);
        $educationLevels = ['Primaria', 'Secundaria', 'Técnico Superior', 'Universitario', 'Postgrado', 'Maestría', 'Doctorado'];
        return view('employees.edit-academic', compact('employee', 'educationLevels'));
    }

    /**
     * Actualiza los datos académicos.
     */
    public function updateAcademic(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $validated = $request->validate([
            'education_level' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'currently_studying' => 'sometimes|boolean',
            'specializations' => 'nullable|string',
        ]);
        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'Datos académicos actualizados.');
    }

    /**
     * Edita los datos laborales (cargo, departamento, usuario asociado, etc.)
     */
    public function editLaboral(Employee $employee)
    {
        $this->authorize('update', $employee);
        $departments = Department::all();
        $users = User::doesntHave('employee')->orWhere('id', $employee->user_id)->get();
        $showUserField = $this->canSeeUserField();
        $positions = Position::orderBy('order')->get();
        $ranks = Rank::orderBy('order')->get();
        return view('employees.edit-laboral', compact('employee', 'departments', 'users', 'showUserField', 'positions', 'ranks'));
    }

    /**
     * Actualiza los datos laborales.
     */
    public function updateLaboral(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id,' . $employee->id,
            'department_id' => 'nullable|exists:departments,id',
            'employee_code' => 'nullable|string|unique:employees,employee_code,' . $employee->id,
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:fijo,contratado,comision',
            'status' => 'required|in:activo,inactivo,reposo',
            'employee_type' => 'nullable|in:gobernacion,alcaldia,homologado,nacional',
            'gender' => 'nullable|in:masculino,femenino,otro',
            'position_id' => 'nullable|exists:positions,id',
            'rank_id' => 'nullable|exists:ranks,id',
            'institutional_code' => 'nullable|string|max:255',
        ]);
        // hired_date se ignora si viene en el request para evitar su modificación
        $employee->update(collect($validated)->except('hired_date')->toArray());
        return redirect()->route('employees.index')->with('success', 'Datos laborales actualizados.');
    }

    /**
     * Edita la sección de documentos.
     */
    public function editDocuments(Employee $employee)
    {
        $this->authorize('update', $employee);
        return view('employees.edit-documents', compact('employee'));
    }

    /**
     * Actualiza los documentos (subir nuevos, eliminar existentes).
     */
    public function updateDocuments(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $validated = $request->validate([
            'new_documents' => 'sometimes|array',
            'new_documents.*.title' => 'required_with:new_documents|string',
            'new_documents.*.file' => 'required_with:new_documents|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'new_documents.*.type' => 'required_with:new_documents|string',
            'new_documents.*.description' => 'nullable|string',
            'delete_documents' => 'sometimes|array',
            'delete_documents.*' => 'exists:employee_documents,id',
        ]);
        if ($request->has('new_documents')) {
            foreach ($request->file('new_documents') as $docData) {
                $file = $docData['file'];
                $path = $file->store('employee_documents', 'public');
                $employee->documents()->create([
                    'title' => $docData['title'],
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'document_type' => $docData['type'],
                    'description' => $docData['description'] ?? null,
                ]);
            }
        }
        if ($request->has('delete_documents')) {
            $employee->documents()->whereIn('id', $request->delete_documents)->get()->each(function ($doc) {
                Storage::disk('public')->delete($doc->file_path);
                $doc->delete();
            });
        }
        return redirect()->route('employees.index')->with('success', 'Documentos actualizados.');
    }

    // ==================== NUEVO MÉTODO PARA MÓDULO DE GUARDIAS ====================
    /**
     * Muestra las guardias asignadas a un empleado (útil para el nuevo módulo de guardias rotativas).
     *
     * @param Employee $employee
     * @return \Illuminate\View\View
     */
    public function guardias(Employee $employee)
    {
        $this->authorize('view', $employee);
        
        $guardias = $employee->guardDuties()
            ->with('rotation')
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        return view('employees.guardias', compact('employee', 'guardias'));
    }
}