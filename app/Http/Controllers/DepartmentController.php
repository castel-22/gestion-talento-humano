<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $this->authorize('viewAny', Department::class);

        $query = Department::withCount('employees');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $sort = $request->get('sort', 'id_desc');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default: $query->orderBy('id', 'desc');
        }

        $departments = $query->paginate(10);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create', Department::class);
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Department::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description']);
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        Department::create($data);
        return redirect()->route('departments.index')->with('success', 'Departamento creado.');
    }

    public function show(Department $department)
    {
        $this->authorize('view', $department);
        $department->load('employees');
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description']);
        if ($request->hasFile('logo')) {
            if ($department->logo) {
                Storage::disk('public')->delete($department->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $department->update($data);
        return redirect()->route('departments.index')->with('success', 'Departamento actualizado.');
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        if ($department->logo) {
            Storage::disk('public')->delete($department->logo);
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departamento eliminado.');
    }

    public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $departments = Department::where('name', 'LIKE', "%{$term}%")->limit(10)->get(['id', 'name']);
        $results = [];
        foreach ($departments as $dept) {
            $results[] = ['id' => $dept->id, 'label' => $dept->name, 'value' => $dept->name];
        }
        return response()->json($results);
    }
}