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
                <span class="text-sm text-pc-orange font-medium">Centro de Reportes</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ══ Cabecera ══ --}}
    <div class="mb-10">
        <h2 class="text-3xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-4">
            <i class="fas fa-file-invoice-dollar text-pc-orange"></i> Centro de Reportes Institucionales
        </h2>
        <p class="text-gray-400 text-[11px] font-bold uppercase tracking-[0.2em] mt-2">Generación de documentación técnica, analítica y administrativa</p>
    </div>

    {{-- ══ Métricas en Tiempo Real ══ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-pc-blue">
            <div class="w-11 h-11 rounded-xl bg-pc-blue/5 text-pc-blue flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
            <div>
                <span class="block text-2xl font-black text-pc-blue">{{ $stats['total_employees'] }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Personal Activo</span>
            </div>
        </div>
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fas fa-user-check"></i></div>
            <div>
                <span class="block text-2xl font-black text-green-600">{{ $stats['attendances_today'] }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Asistencias Hoy</span>
            </div>
        </div>
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-pc-orange">
            <div class="w-11 h-11 rounded-xl bg-pc-orange/5 text-pc-orange flex items-center justify-center text-xl"><i class="fas fa-umbrella-beach"></i></div>
            <div>
                <span class="block text-2xl font-black text-pc-orange">{{ $stats['vacations_active'] }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Vacaciones Activas</span>
            </div>
        </div>
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-11 h-11 rounded-xl bg-red-50 text-pc-red flex items-center justify-center text-xl"><i class="fas fa-heart-pulse"></i></div>
            <div>
                <span class="block text-2xl font-black text-pc-red">{{ $stats['leaves_active'] }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Reposos Activos</span>
            </div>
        </div>
    </div>

    {{-- ══ MÓDULO 1: PERSONAL ══ --}}
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-pc-blue text-white flex items-center justify-center text-lg shadow-lg shadow-blue-100">
                <i class="fas fa-users-gear"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-pc-blue uppercase tracking-tight">Módulo: Personal</h3>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Expedientes, listas y hojas de vida institucionales</p>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-pc-blue/20 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Lista Completa Excel --}}
            <div class="card-pc p-6 flex flex-col border-t-4 border-t-pc-blue">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-excel"></i></div>
                    <div>
                        <h4 class="text-xs font-black text-gray-800 uppercase">Lista de Personal</h4>
                        <p class="text-[9px] text-gray-400">Excel · Filtrable por depto. y estado</p>
                    </div>
                </div>
                <form action="{{ route('reports.employees.excel') }}" method="GET" class="flex-1 space-y-3 no-double-click" target="_blank">
                    <div>
                        <label class="label-pc text-[9px]">Unidad Adscrita</label>
                        <select name="department_id" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                            <option value="">Todas las Unidades</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-pc text-[9px]">Estado Laboral</label>
                        <select name="status" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                            <option value="">Todos los Estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="reposo">En Reposo</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                    </button>
                </form>
            </div>

            {{-- Lista PDF Oficial --}}
            <div class="card-pc p-6 flex flex-col border-t-4 border-t-pc-red">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-pc-red flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-pdf"></i></div>
                    <div>
                        <h4 class="text-xs font-black text-gray-800 uppercase">Planilla Oficial de Personal</h4>
                        <p class="text-[9px] text-gray-400">PDF · Con logos y firmas institucionales</p>
                    </div>
                </div>
                <form action="{{ route('reports.employees.pdf') }}" method="GET" class="flex-1 space-y-3 no-double-click" target="_blank">
                    <div>
                        <label class="label-pc text-[9px]">Unidad Adscrita</label>
                        <select name="department_id" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                            <option value="">Todas las Unidades</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-pc text-[9px]">Estado Laboral</label>
                        <select name="status" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                            <option value="">Todos los Estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="reposo">En Reposo</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-pc-red hover:bg-red-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-100">
                        <i class="fas fa-file-pdf"></i> Generar PDF Oficial
                    </button>
                </form>
            </div>

            {{-- Hoja de Vida Individual --}}
            <div class="card-pc p-6 flex flex-col border-t-4 border-t-pc-orange" x-data="{ 
                selectedEmployee: '',
                open: false,
                search: '',
                results: [],
                loading: false,
                fetchResults() {
                    if (this.search.length < 2) {
                        this.results = [];
                        this.open = false;
                        return;
                    }
                    this.loading = true;
                    fetch('{{ route('api.employees.autocomplete') }}?term=' + encodeURIComponent(this.search))
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                            this.open = true;
                        })
                        .catch(() => { this.loading = false; });
                },
                selectItem(item) {
                    this.selectedEmployee = item.id;
                    this.search = item.label;
                    this.open = false;
                },
                clear() {
                    this.selectedEmployee = '';
                    this.search = '';
                    this.results = [];
                    this.open = false;
                }
            }">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-pc-orange/10 text-pc-orange flex items-center justify-center text-lg shadow-inner"><i class="fas fa-id-card-alt"></i></div>
                    <div>
                        <h4 class="text-xs font-black text-gray-800 uppercase">Hoja de Vida Institucional</h4>
                        <p class="text-[9px] text-gray-400">PDF Individual · Perfil completo del trabajador</p>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div class="relative">
                        <label class="label-pc text-[9px]">Seleccionar Trabajador <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <input type="text"
                                   x-model="search"
                                   @input.debounce.300ms="fetchResults()"
                                   @focus="open = true"
                                   @click.away="setTimeout(() => { if (!selectedEmployee) search = ''; open = false; }, 200)"
                                   placeholder="Buscar por nombre o cédula..."
                                   class="w-full rounded-md border border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-[10px] pr-8 py-2.5 px-3 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200 font-bold">
                            
                            <div class="absolute right-2.5 flex items-center space-x-1">
                                <template x-if="loading">
                                    <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                                </template>
                                <template x-if="!loading && search">
                                    <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <i class="fas fa-times text-[10px]"></i>
                                    </button>
                                </template>
                                <template x-if="!loading && !search">
                                    <i class="fas fa-search text-gray-400 text-[10px]"></i>
                                </template>
                            </div>
                        </div>
                        
                        <input type="hidden" name="employee_id" x-model="selectedEmployee">

                        <div x-show="open && results.length > 0"
                             x-cloak
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                            <ul class="py-1 text-[11px] text-gray-700 font-medium">
                                <template x-for="item in results" :key="item.id">
                                    <li>
                                        <button type="button"
                                                @click="selectItem(item)"
                                                class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                            <span x-text="item.label"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 rounded-xl p-3">
                        <p class="text-[9px] text-amber-700 dark:text-amber-500 font-bold"><i class="fas fa-info-circle mr-1"></i> Incluye: datos personales, laborales, historial de vacaciones, reposos, asistencias y despliegues operativos.</p>
                    </div>
                    <button type="button" @click="window.open('{{ url('/reports/employees') }}/' + selectedEmployee + '/profile', '_blank')"
                       :disabled="!selectedEmployee"
                       :class="selectedEmployee ? 'bg-pc-orange hover:bg-orange-600 cursor-pointer' : 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none'"
                       class="w-full text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-orange-100">
                        <i class="fas fa-download"></i> Descargar Hoja de Vida
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MÓDULO 2: ASISTENCIAS ══ --}}
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center text-lg shadow-lg shadow-green-100">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-green-700 uppercase tracking-tight">Módulo: Control de Asistencia</h3>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Auditoría de entradas, salidas y puntualidad</p>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-green-500/20 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach([['excel','bg-green-600 hover:bg-green-700','shadow-green-100','fas fa-file-excel','Reporte Excel de Asistencias','Excel · Registros detallados por período','reports.attendances.excel'],
                      ['pdf',  'bg-pc-red hover:bg-red-700',     'shadow-red-100',  'fas fa-file-pdf', 'Planilla Oficial de Asistencias','PDF · Con logos y firmas institucionales','reports.attendances.pdf']] as $btn)
            <div class="card-pc p-6 flex flex-col border-t-4 {{ $btn[0] === 'excel' ? 'border-t-green-500' : 'border-t-pc-red' }}">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl {{ $btn[0] === 'excel' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-pc-red' }} flex items-center justify-center text-lg shadow-inner">
                        <i class="{{ $btn[3] }}"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-gray-800 uppercase">{{ $btn[4] }}</h4>
                        <p class="text-[9px] text-gray-400">{{ $btn[5] }}</p>
                    </div>
                </div>
                <form action="{{ route($btn[6]) }}" method="GET" target="_blank" class="flex-1 space-y-3 no-double-click">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label-pc text-[9px]">Período Mensual</label>
                            <input type="month" name="period" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200" value="{{ date('Y-m') }}">
                        </div>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '',
                            results: [],
                            loading: false,
                            fetchResults() {
                                if (this.search.length < 2) {
                                    this.results = [];
                                    this.open = false;
                                    return;
                                }
                                this.loading = true;
                                fetch('{{ route("api.employees.autocomplete") }}?term=' + encodeURIComponent(this.search))
                                    .then(res => res.json())
                                    .then(data => {
                                        this.results = data;
                                        this.loading = false;
                                        this.open = true;
                                    })
                                    .catch(() => { this.loading = false; });
                            },
                            selectItem(item) {
                                this.selectedId = item.id;
                                this.search = item.label;
                                this.open = false;
                            },
                            clear() {
                                this.selectedId = '';
                                this.search = '';
                                this.results = [];
                                this.open = false;
                            }
                        }" class="relative">
                            <label class="label-pc text-[9px]">Empleado (Opc.)</label>
                            <div class="relative flex items-center">
                                <input type="text"
                                       x-model="search"
                                       @input.debounce.300ms="fetchResults()"
                                       @focus="open = true"
                                       @click.away="setTimeout(() => { if (!selectedId) search = ''; open = false; }, 200)"
                                       placeholder="Todos..."
                                       class="w-full rounded-md border border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-[10px] pr-8 py-2.5 px-3 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200 font-bold">
                                
                                <div class="absolute right-2.5 flex items-center space-x-1">
                                    <template x-if="loading">
                                        <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                                    </template>
                                    <template x-if="!loading && search">
                                        <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </template>
                                    <template x-if="!loading && !search">
                                        <i class="fas fa-search text-gray-400 text-[10px]"></i>
                                    </template>
                                </div>
                            </div>
                            
                            <input type="hidden" name="employee_id" x-model="selectedId">

                            <div x-show="open && results.length > 0"
                                 x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                                <ul class="py-1 text-[11px] text-gray-700 font-medium">
                                    <template x-for="item in results" :key="item.id">
                                        <li>
                                            <button type="button"
                                                    @click="selectItem(item)"
                                                    class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                                <span x-text="item.label"></span>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full {{ $btn[1] }} text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg {{ $btn[2] }}">
                        <i class="{{ $btn[3] }}"></i> Generar Reporte
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══ MÓDULO 3: VACACIONES ══ --}}
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-pc-orange text-white flex items-center justify-center text-lg shadow-lg shadow-orange-100">
                <i class="fas fa-umbrella-beach"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-pc-orange uppercase tracking-tight">Módulo: Vacaciones</h3>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Historial y saldos de disfrute vacacional</p>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-pc-orange/20 to-transparent"></div>
        </div>
        <div class="card-pc p-6 border-t-4 border-t-pc-orange">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-excel"></i></div>
                <div>
                    <h4 class="text-xs font-black text-gray-800 uppercase">Historial de Vacaciones</h4>
                    <p class="text-[9px] text-gray-400">Excel · Con saldos regulares y vencidos</p>
                </div>
            </div>
            <form action="{{ route('reports.vacations.excel') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end no-double-click" target="_blank">
                <div>
                    <label class="label-pc text-[9px]">Año</label>
                    <select name="year" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                        <option value="">Todos los años</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="label-pc text-[9px]">Estado de la Solicitud</label>
                    <select name="status" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="en_curso">En curso</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    results: [],
                    loading: false,
                    fetchResults() {
                        if (this.search.length < 2) {
                            this.results = [];
                            this.open = false;
                            return;
                        }
                        this.loading = true;
                        fetch('{{ route("api.employees.autocomplete") }}?term=' + encodeURIComponent(this.search))
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.loading = false;
                                this.open = true;
                            })
                            .catch(() => { this.loading = false; });
                    },
                    selectItem(item) {
                        this.selectedId = item.id;
                        this.search = item.label;
                        this.open = false;
                    },
                    clear() {
                        this.selectedId = '';
                        this.search = '';
                        this.results = [];
                        this.open = false;
                    }
                }" class="relative">
                    <label class="label-pc text-[9px]">Empleado (Opc.)</label>
                    <div class="relative flex items-center">
                        <input type="text"
                               x-model="search"
                               @input.debounce.300ms="fetchResults()"
                               @focus="open = true"
                               @click.away="setTimeout(() => { if (!selectedId) search = ''; open = false; }, 200)"
                               placeholder="Todos..."
                               class="w-full rounded-md border border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-[10px] pr-8 py-2.5 px-3 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200 font-bold">
                        
                        <div class="absolute right-2.5 flex items-center space-x-1">
                            <template x-if="loading">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                            </template>
                            <template x-if="!loading && search">
                                <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </template>
                            <template x-if="!loading && !search">
                                <i class="fas fa-search text-gray-400 text-[10px]"></i>
                            </template>
                        </div>
                    </div>
                    
                    <input type="hidden" name="employee_id" x-model="selectedId">

                    <div x-show="open && results.length > 0"
                         x-cloak
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                        <ul class="py-1 text-[11px] text-gray-700 font-medium">
                            <template x-for="item in results" :key="item.id">
                                <li>
                                    <button type="button"
                                            @click="selectItem(item)"
                                            class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                        <span x-text="item.label"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
            </form>
        </div>
    </div>

    {{-- ══ MÓDULO 4: REPOSOS ══ --}}
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-pc-red text-white flex items-center justify-center text-lg shadow-lg shadow-red-100">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-pc-red uppercase tracking-tight">Módulo: Reposos Médicos</h3>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Historial de reposos e incapacidades</p>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-pc-red/20 to-transparent"></div>
        </div>
        <div class="card-pc p-6 border-t-4 border-t-pc-red">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-excel"></i></div>
                <div>
                    <h4 class="text-xs font-black text-gray-800 uppercase">Registro de Reposos Médicos</h4>
                    <p class="text-[9px] text-gray-400">Excel · Médico, institución y diagnóstico</p>
                </div>
            </div>
            <form action="{{ route('reports.leaves.excel') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end no-double-click" target="_blank">
                <div>
                    <label class="label-pc text-[9px]">Año</label>
                    <select name="year" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                        <option value="">Todos los años</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    results: [],
                    loading: false,
                    fetchResults() {
                        if (this.search.length < 2) {
                            this.results = [];
                            this.open = false;
                            return;
                        }
                        this.loading = true;
                        fetch('{{ route("api.employees.autocomplete") }}?term=' + encodeURIComponent(this.search))
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.loading = false;
                                this.open = true;
                            })
                            .catch(() => { this.loading = false; });
                    },
                    selectItem(item) {
                        this.selectedId = item.id;
                        this.search = item.label;
                        this.open = false;
                    },
                    clear() {
                        this.selectedId = '';
                        this.search = '';
                        this.results = [];
                        this.open = false;
                    }
                }" class="relative">
                    <label class="label-pc text-[9px]">Empleado (Opc.)</label>
                    <div class="relative flex items-center">
                        <input type="text"
                               x-model="search"
                               @input.debounce.300ms="fetchResults()"
                               @focus="open = true"
                               @click.away="setTimeout(() => { if (!selectedId) search = ''; open = false; }, 200)"
                               placeholder="Todos..."
                               class="w-full rounded-md border border-gray-300 shadow-sm focus:border-pc-orange focus:ring focus:ring-pc-orange focus:ring-opacity-50 text-[10px] pr-8 py-2.5 px-3 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200 font-bold">
                        
                        <div class="absolute right-2.5 flex items-center space-x-1">
                            <template x-if="loading">
                                <i class="fas fa-spinner animate-spin text-gray-400 text-[10px]"></i>
                            </template>
                            <template x-if="!loading && search">
                                <button type="button" @click="clear()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </template>
                            <template x-if="!loading && !search">
                                <i class="fas fa-search text-gray-400 text-[10px]"></i>
                            </template>
                        </div>
                    </div>
                    
                    <input type="hidden" name="employee_id" x-model="selectedId">

                    <div x-show="open && results.length > 0"
                         x-cloak
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto">
                        <ul class="py-1 text-[11px] text-gray-700 font-medium">
                            <template x-for="item in results" :key="item.id">
                                <li>
                                    <button type="button"
                                            @click="selectItem(item)"
                                            class="w-full text-left px-3 py-2 hover:bg-pc-orange hover:text-white transition-colors">
                                        <span x-text="item.label"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
            </form>
        </div>
    </div>

    {{-- ══ MÓDULO 5: GUARDIAS ══ --}}
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-lg shadow-indigo-100">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-indigo-700 uppercase tracking-tight">Módulo: Guardias Rotativas 24x72</h3>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">Roll mensual de guardia por rotación</p>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-indigo-500/20 to-transparent"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tarjeta Excel --}}
            <div class="card-pc p-6 border-t-4 border-t-green-500 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-excel"></i></div>
                        <div>
                            <h4 class="text-xs font-black text-gray-800 uppercase">Roll de Guardia Mensual (Excel)</h4>
                            <p class="text-[9px] text-gray-400">Excel · Detalle diario por rotación y técnico asignado</p>
                        </div>
                    </div>
                    <form action="{{ route('reports.guards.excel') }}" method="GET" class="space-y-3 no-double-click" target="_blank">
                        <div>
                            <label class="label-pc text-[9px]">Rotación de Guardia</label>
                            <select name="rotation_id" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                <option value="">Todas las Rotaciones</option>
                                @foreach($rotations as $rot)
                                    <option value="{{ $rot->id }}">{{ strtoupper($rot->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label-pc text-[9px]">Mes</label>
                                <select name="month" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                    <option value="">Todos los meses</option>
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label-pc text-[9px]">Año</label>
                                <select name="year" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                    @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100 mt-4">
                            <i class="fas fa-file-excel"></i> Descargar Excel
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tarjeta PDF --}}
            <div class="card-pc p-6 border-t-4 border-t-pc-red flex flex-col justify-between" x-data="{
                rotationId: '',
                month: '{{ date('n') }}',
                year: '{{ date('Y') }}',
                submitPdf(e) {
                    if(!this.rotationId) {
                        alert('Por favor seleccione una rotación de guardia.');
                        return;
                    }
                    const actionUrl = `/guard-rotations/${this.rotationId}/pdf?month=${this.month}&year=${this.year}`;
                    window.open(actionUrl, '_blank');
                }
            }">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pc-red flex items-center justify-center text-lg shadow-inner"><i class="fas fa-file-pdf"></i></div>
                        <div>
                            <h4 class="text-xs font-black text-gray-800 uppercase">Planilla Oficial de Guardias (PDF)</h4>
                            <p class="text-[9px] text-gray-400">PDF · Formato oficial imprimible en una hoja</p>
                        </div>
                    </div>
                    <form @submit.prevent="submitPdf()" class="space-y-3">
                        <div>
                            <label class="label-pc text-[9px]">Rotación de Guardia <span class="text-red-500">*</span></label>
                            <select x-model="rotationId" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                <option value="">Seleccione una Rotación</option>
                                @foreach($rotations as $rot)
                                    <option value="{{ $rot->id }}">{{ strtoupper($rot->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label-pc text-[9px]">Mes</label>
                                <select x-model="month" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}">
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label-pc text-[9px]">Año</label>
                                <select x-model="year" class="input-pc text-[10px] font-bold py-2.5 bg-gray-50/50 dark:bg-slate-800/50 dark:border-slate-700 dark:text-gray-200">
                                    @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-pc-red hover:bg-red-700 text-white font-black text-[9px] uppercase py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-100 mt-4">
                            <i class="fas fa-file-pdf"></i> Generar PDF Oficial
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
