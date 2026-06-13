@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Gestión de Personal</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="card-pc p-6">
        {{-- Cabecera con estadísticas rápidas --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                    <i class="fas fa-id-card text-pc-orange"></i> Registro de Personal
                </h2>
                <div class="flex gap-4 mt-2">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">Total: {{ $employees->total() }} Integrantes</span>
                    <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">• Activos: {{ $employees->where('status', 'activo')->count() }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 w-full md:w-auto">

                @can('create', App\Models\Employee::class)
                    <a href="{{ route('employees.create') }}" class="flex-1 md:flex-none bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i> Nuevo Registro
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filtros y Buscador Compactos --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="md:col-span-3">
                <form method="GET" action="{{ route('employees.index') }}" id="searchForm" class="relative group">
                    <div class="absolute z-10 inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                    </div>
                    <input type="text" id="search_input" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por cédula, nombre o apellido..." 
                           class="input-pc pl-12 py-3 bg-gray-50/50 dark:bg-slate-900 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-all text-xs dark:text-white">
                    <div id="suggestions" class="absolute z-50 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl w-full hidden shadow-2xl mt-2 max-h-60 overflow-auto"></div>
                    <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'id_desc') }}">
                </form>
            </div>
            <div class="md:col-span-1">
                <select onchange="document.getElementById('sortInput').value = this.value; document.getElementById('searchForm').submit();" class="input-pc py-3 text-xs font-bold uppercase tracking-widest text-pc-blue">
                    <option value="id_desc" {{ request('sort') == 'id_desc' ? 'selected' : '' }}>Más Recientes</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                    <option value="id_asc" {{ request('sort') == 'id_asc' ? 'selected' : '' }}>Más Antiguos</option>
                </select>
            </div>
        </div>

        {{-- Tabla Compacta --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-pc-blue border-b border-pc-blue/10">
                        <th class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest">Identificación</th>
                        <th class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest">Apellidos y Nombres</th>
                        <th class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest">Departamento / Unidad</th>
                        <th class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest text-center">Estado</th>
                        <th class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @foreach($employees as $employee)
                    <tr class="hover:bg-pc-blue/5 transition-all group">
                        <td class="px-6 py-4">
                            @php
                                $rawId = $employee->id_number;
                                $numericPart = preg_replace('/[^0-9]/', '', $rawId);
                                $prefix = preg_replace('/[0-9]/', '', $rawId);
                                $formatted = $numericPart ? $prefix . number_format((float)$numericPart, 0, ',', '.') : $rawId;
                            @endphp
                            <span class="text-xs font-black text-pc-blue bg-pc-blue/5 px-2 py-1 rounded-lg">{{ $formatted }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 flex items-center justify-center font-black text-[10px] group-hover:bg-pc-orange group-hover:text-white transition-colors shadow-inner">
                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-gray-800 dark:text-white uppercase leading-none">{{ $employee->full_name }}</span>
                                    <span class="text-[9px] font-bold text-gray-400 dark:text-slate-500 mt-1 uppercase tracking-tighter">{{ $employee->position ?: 'Sin Cargo' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-pc-orange"></span>
                                <span class="text-[10px] font-bold text-gray-500 uppercase truncate max-w-[150px]">
                                    {{ $employee->department->name ?? 'Sin Asignar' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClasses = [
                                    'activo' => 'bg-green-100 text-green-600 border-green-200',
                                    'inactivo' => 'bg-gray-100 text-gray-500 border-gray-200',
                                    'reposo' => 'bg-orange-100 text-orange-600 border-orange-200'
                                ];
                                $class = $statusClasses[$employee->status] ?? 'bg-gray-100 text-gray-500';
                            @endphp
                            <span class="px-2 py-1 text-[8px] font-black rounded-md uppercase tracking-widest border {{ $class }}">
                                {{ $employee->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                @can('view', $employee)
                                    <a href="{{ route('employees.show', $employee) }}" class="w-7 h-7 flex items-center justify-center bg-white border border-gray-100 rounded-lg text-pc-blue hover:bg-pc-blue hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </a>
                                @endcan
                                @can('update', $employee)
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="w-7 h-7 flex items-center justify-center bg-white border border-gray-100 rounded-lg text-pc-orange hover:bg-pc-orange hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-edit text-[10px]"></i>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" x-cloak
                                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden z-50 transform origin-top-right transition-all">
                                            <div class="p-2 space-y-0.5">
                                                <a href="{{ route('employees.edit', $employee) }}#personal" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-black uppercase text-gray-500 dark:text-gray-400 hover:bg-pc-blue hover:text-white transition-all">
                                                    <i class="fas fa-user w-4"></i> Datos Personales
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}#academic" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-black uppercase text-gray-500 dark:text-gray-400 hover:bg-pc-blue hover:text-white transition-all">
                                                    <i class="fas fa-graduation-cap w-4"></i> Académicos
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}#laboral" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-black uppercase text-gray-500 dark:text-gray-400 hover:bg-pc-blue hover:text-white transition-all">
                                                    <i class="fas fa-briefcase w-4"></i> Laborales
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}#documents" class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-black uppercase text-gray-500 dark:text-gray-400 hover:bg-pc-blue hover:text-white transition-all">
                                                    <i class="fas fa-folder-open w-4"></i> Documentos
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('delete', $employee)
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline confirm-delete" data-label="{{ $employee->full_name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-7 h-7 flex items-center justify-center bg-white border border-gray-100 rounded-lg text-pc-red hover:bg-pc-red hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search_input');
    const suggestionsDiv = document.getElementById('suggestions');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const term = this.value.trim();
        if (term.length < 2) { suggestionsDiv.classList.add('hidden'); return; }
        debounceTimer = setTimeout(() => {
            fetch(`{{ route('employees.autocomplete') }}?term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) { suggestionsDiv.classList.add('hidden'); return; }
                    suggestionsDiv.innerHTML = data.map(item => `
                        <div class="suggestion-item px-4 py-3 hover:bg-pc-blue/5 cursor-pointer border-b border-gray-50 flex items-center gap-3" data-value="${item.value}">
                            <i class="fas fa-user-tag text-gray-300"></i>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">${item.label}</span>
                        </div>
                    `).join('');
                    suggestionsDiv.classList.remove('hidden');
                });
        }, 300);
    });

    suggestionsDiv.addEventListener('click', (e) => {
        const target = e.target.closest('.suggestion-item');
        if (target) {
            searchInput.value = target.getAttribute('data-value');
            suggestionsDiv.classList.add('hidden');
            document.getElementById('searchForm').submit();
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) suggestionsDiv.classList.add('hidden');
    });
</script>

<style>
    .pagination { display: flex; gap: 0.5rem; justify-content: center; }
    .pagination li { list-style: none; }
    .pagination li a, .pagination li span { padding: 0.5rem 1rem; border-radius: 0.75rem; font-weight: 800; font-size: 0.75rem; border: 1px solid #f1f5f9; background: white; color: #0B3B5E; transition: all 0.2s; }
    .pagination li.active span { background: #0B3B5E; color: white; border-color: #0B3B5E; }
    .pagination li a:hover { background: #F97316; color: white; border-color: #F97316; }
</style>
@endpush