@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-700 hover:text-pc-orange">Personal</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Expediente de {{ $employee->first_name }}</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ tab: 'perfil' }">
    
    {{-- Cabecera de Perfil --}}
    <div class="card-pc p-8 mb-8 border-l-8 border-l-pc-blue relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-5 pointer-events-none translate-x-1/4 -translate-y-1/4">
            <i class="fas fa-id-card text-[200px]"></i>
        </div>
        
        <div class="flex flex-col md:flex-row gap-8 items-center md:items-start relative z-10">
            <div class="w-32 h-32 rounded-3xl bg-pc-blue text-white flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-200">
                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-2">
                    <h2 class="text-3xl font-black text-pc-blue uppercase tracking-tight">{{ $employee->full_name }}</h2>
                    <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-widest bg-pc-orange text-white">
                        @php $rawId = $employee->id_number; $numPart = preg_replace('/[^0-9]/', '', $rawId); $pfx = preg_replace('/[0-9]/', '', $rawId); @endphp
                        {{ $numPart ? $pfx . number_format((float)$numPart, 0, ',', '.') : $rawId }}
                    </span>
                </div>
                <p class="text-gray-500 font-black text-sm uppercase tracking-widest flex items-center justify-center md:justify-start gap-2">
                    <i class="fas fa-briefcase text-pc-orange"></i> {{ $employee->position ?: 'Sin Cargo Asignado' }}
                </p>
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4">
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">
                        <i class="fas fa-building text-pc-blue text-xs"></i>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $employee->department->name ?? 'Sin Unidad' }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">
                        <i class="fas fa-calendar-alt text-pc-blue text-xs"></i>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Ingreso: {{ $employee->hired_date ? $employee->hired_date->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col gap-2 w-full md:w-auto" x-data="{ openEdit: false }">
                <div class="relative">
                    <button @click="openEdit = !openEdit" class="w-full bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-6 py-3 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i> Actualizar Datos <i class="fas fa-chevron-down ml-1 text-[8px]"></i>
                    </button>
                    
                    <div x-show="openEdit" @click.away="openEdit = false" x-cloak
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden z-50 transform origin-top-right transition-all">
                        <div class="p-3 border-b border-gray-50 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50">
                            <span class="text-[9px] font-black text-pc-blue dark:text-pc-orange uppercase tracking-widest">Seleccionar Sección</span>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('employees.edit', $employee) }}#personal" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-bold text-gray-600 dark:text-gray-300 hover:bg-pc-blue hover:text-white transition-all">
                                <i class="fas fa-user w-4 text-center"></i> Datos Personales
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}#academic" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-bold text-gray-600 dark:text-gray-300 hover:bg-pc-blue hover:text-white transition-all">
                                <i class="fas fa-graduation-cap w-4 text-center"></i> Académicos
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}#laboral" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-bold text-gray-600 dark:text-gray-300 hover:bg-pc-blue hover:text-white transition-all">
                                <i class="fas fa-briefcase w-4 text-center"></i> Laborales / Rango
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}#documents" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-bold text-gray-600 dark:text-gray-300 hover:bg-pc-blue hover:text-white transition-all">
                                <i class="fas fa-folder-open w-4 text-center"></i> Documentación
                            </a>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-pc-blue font-black text-[10px] uppercase px-6 py-2 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Volver al Registro
                </a>
            </div>
        </div>
    </div>

    {{-- Navegación Interna (Pestañas) --}}
    <div class="flex flex-wrap gap-2 mb-8 bg-gray-100/50 p-1.5 rounded-2xl border border-gray-200 shadow-inner">
        <button @click="tab = 'perfil'" :class="tab === 'perfil' ? 'bg-white text-pc-blue shadow-md' : 'text-gray-500 hover:text-pc-blue'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-user-circle"></i> <span class="hidden sm:inline">Perfil Integral</span>
        </button>
        <button @click="tab = 'laboral'" :class="tab === 'laboral' ? 'bg-white text-pc-blue shadow-md' : 'text-gray-500 hover:text-pc-blue'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-shield-alt"></i> <span class="hidden sm:inline">Datos de Servicio</span>
        </button>
        <button @click="tab = 'historial'" :class="tab === 'historial' ? 'bg-white text-pc-blue shadow-md' : 'text-gray-500 hover:text-pc-blue'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-history"></i> <span class="hidden sm:inline">Historial Operativo</span>
        </button>
        <button @click="tab = 'documentos'" :class="tab === 'documentos' ? 'bg-white text-pc-blue shadow-md' : 'text-gray-500 hover:text-pc-blue'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
            <i class="fas fa-folder-open"></i> <span class="hidden sm:inline">Archivos ({{ $employee->documents->count() }})</span>
        </button>
    </div>

    {{-- Contenido de Pestañas --}}
    <div class="space-y-8 pb-12">
        
        {{-- Pestaña: Perfil Integral --}}
        <div x-show="tab === 'perfil'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card-pc p-8">
                <h3 class="text-sm font-black text-pc-blue uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-pc-orange"></i> Información Personal
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha Nacimiento</p>
                            <p class="text-xs font-bold text-gray-800">{{ $employee->birth_date ? $employee->birth_date->format('d/m/Y') : 'No Registrada' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Grupo Sanguíneo</p>
                            <p class="text-xs font-black text-pc-red">{{ $employee->blood_type ?: 'Desconocido' }}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Dirección de Domicilio</p>
                        <p class="text-xs font-bold text-gray-800 leading-relaxed">{{ $employee->address ?: 'No Registrada' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Teléfono Personal</p>
                            <p class="text-xs font-bold text-gray-800">{{ $employee->personal_phone ?: 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado Civil</p>
                            <p class="text-xs font-bold text-gray-800 uppercase">{{ $employee->marital_status ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-pc p-8">
                <h3 class="text-sm font-black text-pc-blue uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-pc-orange"></i> Perfil Académico
                </h3>
                <div class="space-y-4">
                    <div class="p-6 bg-pc-blue/5 rounded-2xl border border-pc-blue/10">
                        <p class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-2">Grado de Instrucción</p>
                        <p class="text-lg font-black text-pc-blue tracking-tight">{{ $employee->education_level ?: 'Sin Registrar' }}</p>
                        <p class="text-xs font-bold text-gray-500 mt-1 uppercase">{{ $employee->degree ?: 'Sin Título Específico' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Especializaciones / Cursos</p>
                        <p class="text-xs font-medium text-gray-600 italic">{{ $employee->specializations ?: 'Ninguna registrada' }}</p>
                    </div>
                    @if($employee->currently_studying)
                        <div class="p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-green-700 uppercase">Actualmente cursando estudios</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pestaña: Datos de Servicio --}}
        <div x-show="tab === 'laboral'" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="card-pc p-8">
                    <h3 class="text-sm font-black text-pc-blue uppercase mb-8 flex items-center gap-2">
                        <i class="fas fa-briefcase text-pc-orange"></i> Asignación Institucional
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 rounded-xl border-l-4 border-l-pc-blue">
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Código de Empleado</p>
                            <p class="text-base font-black text-pc-blue tracking-widest">{{ $employee->employee_code }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border-l-4 border-l-pc-orange">
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Estado de Fuerza</p>
                            <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-widest border {{ $employee->status === 'activo' ? 'bg-green-100 text-green-600 border-green-200' : 'bg-orange-100 text-orange-600 border-orange-200' }}">
                                {{ $employee->status }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Tipo de Personal</p>
                            <p class="text-xs font-black text-gray-700 uppercase">{{ $employee->employment_type }} / {{ $employee->employee_type }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Rango / Jerarquía</p>
                            <p class="text-xs font-black text-gray-700 uppercase">{{ $employee->rank ? $employee->rank->name : 'Administrativo' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="card-pc p-8 bg-pc-blue text-white overflow-hidden relative">
                    <div class="absolute right-0 bottom-0 opacity-10 translate-x-1/4 translate-y-1/4">
                        <i class="fas fa-umbrella-beach text-[120px]"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase mb-6 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-pc-orange"></i> Vacaciones
                    </h3>
                    <div class="text-center py-6">
                        <p class="text-5xl font-black mb-2">{{ $employee->getAvailableVacationDays() }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Días Disponibles Hoy</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/10 flex justify-between text-[10px] font-bold uppercase">
                        <span>Antigüedad: {{ $employee->hired_date ? $employee->hired_date->diffInYears(now()) : 0 }} años</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pestaña: Historial Operativo --}}
        <div x-show="tab === 'historial'" x-transition class="space-y-8">
            {{-- Resumen de Movilidad --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-pc p-6 bg-white border-l-4 border-l-pc-blue">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Despliegues</p>
                        <i class="fas fa-truck text-pc-blue opacity-20"></i>
                    </div>
                    <p class="text-2xl font-black text-pc-blue mt-2">{{ $employee->deployments->count() }}</p>
                </div>
                <div class="card-pc p-6 bg-white border-l-4 border-l-pc-red">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Reposos</p>
                        <i class="fas fa-notes-medical text-pc-red opacity-20"></i>
                    </div>
                    <p class="text-2xl font-black text-pc-red mt-2">{{ $employee->leaves->count() }}</p>
                </div>
                <div class="card-pc p-6 bg-white border-l-4 border-l-green-500">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Asistencias Mes</p>
                        <i class="fas fa-check-circle text-green-500 opacity-20"></i>
                    </div>
                    <p class="text-2xl font-black text-green-500 mt-2">{{ $employee->attendances()->whereMonth('date', now()->month)->count() }}</p>
                </div>
            </div>

            <div class="card-pc p-8">
                <h3 class="text-sm font-black text-pc-blue uppercase mb-8 flex items-center gap-2">
                    <i class="fas fa-route text-pc-orange"></i> Línea de Tiempo de Solicitudes
                </h3>
                <div class="space-y-6">
                    @forelse($employee->vacations->merge($employee->leaves)->sortByDesc('created_at')->take(10) as $event)
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center {{ $event instanceof \App\Models\Vacation ? 'bg-pc-blue/10 text-pc-blue' : 'bg-pc-red/10 text-pc-red' }}">
                                <i class="fas {{ $event instanceof \App\Models\Vacation ? 'fa-umbrella-beach' : 'fa-notes-medical' }} text-xs"></i>
                            </div>
                            <div class="flex-1 border-b border-gray-50 pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-black text-gray-800 uppercase">{{ $event instanceof \App\Models\Vacation ? 'Solicitud de Vacaciones' : 'Registro de Reposo' }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold mt-1">
                                            Período: {{ $event->start_date->format('d/m/Y') }} al {{ $event->end_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <span class="text-[9px] font-black px-2 py-1 rounded bg-gray-100 uppercase">{{ $event->status }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 text-xs font-bold py-8 uppercase tracking-widest">Sin registros históricos en esta sección</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pestaña: Documentos --}}
        <div x-show="tab === 'documentos'" x-transition class="card-pc p-8">
            <h3 class="text-sm font-black text-pc-blue uppercase mb-8 flex items-center gap-2">
                <i class="fas fa-folder-open text-pc-orange"></i> Repositorio de Documentos
            </h3>
            @if($employee->documents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($employee->documents as $doc)
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 hover:border-pc-blue transition-all group flex flex-col">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-xl bg-white text-pc-blue flex items-center justify-center shadow-sm">
                                    <i class="fas {{ str_contains($doc->file_name, 'pdf') ? 'fa-file-pdf' : 'fa-file-image' }} text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-black text-gray-800 uppercase truncate">{{ $doc->title }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $doc->document_type }}</p>
                                </div>
                            </div>
                            <div class="mt-auto pt-4 border-t border-gray-200 flex justify-between items-center">
                                <span class="text-[9px] font-black text-gray-400 uppercase">{{ $doc->created_at->format('d/m/Y') }}</span>
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="bg-pc-blue text-white text-[9px] font-black uppercase px-3 py-2 rounded-lg shadow-md shadow-blue-100 hover:bg-blue-800 transition-all">
                                    <i class="fas fa-download mr-1"></i> Ver Archivo
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-200 text-3xl"></i>
                    </div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No se han cargado documentos en este expediente</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection