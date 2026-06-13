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
                <span class="text-sm text-pc-orange font-medium">Control de Reposos Médicos</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ══════════════════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pc-red/10 flex items-center justify-center">
                    <i class="fas fa-notes-medical text-pc-red"></i>
                </div>
                Gestión de Reposos Médicos
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1 ml-13">
                Control de licencias médicas y justificaciones de salud del personal
            </p>
        </div>
        @can('create', App\Models\Leave::class)
            <a href="{{ route('leaves.create') }}"
               class="bg-pc-red hover:bg-red-700 text-white font-black text-[10px] uppercase px-6 py-3.5 rounded-xl shadow-lg shadow-red-100/50 transition-all flex items-center gap-2 shrink-0">
                <i class="fas fa-plus-circle"></i> Cargar Reposo
            </a>
        @endcan
    </div>

    {{-- ══════════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Pendientes --}}
        <div class="card-pc p-5 border-l-4 border-yellow-400 dark:bg-slate-900 dark:border-slate-800 group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-400/10 flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-500 text-sm"></i>
                </div>
                <span class="text-3xl font-black text-yellow-500">{{ $stats['pendientes'] }}</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Pendientes</p>
            <p class="text-[9px] text-gray-400 font-bold mt-0.5">Aguardan aprobación</p>
        </div>

        {{-- En Curso --}}
        <div class="card-pc p-5 border-l-4 border-green-500 dark:bg-slate-900 dark:border-slate-800 group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center relative">
                    <i class="fas fa-heartbeat text-green-500 text-sm"></i>
                    @if($stats['en_curso'] > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></span>
                    @endif
                </div>
                <span class="text-3xl font-black text-green-500">{{ $stats['en_curso'] }}</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">En Curso</p>
            <p class="text-[9px] text-gray-400 font-bold mt-0.5">Activos hoy</p>
        </div>

        {{-- Finalizados --}}
        <div class="card-pc p-5 border-l-4 border-blue-400 dark:bg-slate-900 dark:border-slate-800 group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-400/10 flex items-center justify-center">
                    <i class="fas fa-check-double text-blue-400 text-sm"></i>
                </div>
                <span class="text-3xl font-black text-blue-400">{{ $stats['finalizados'] }}</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Finalizados</p>
            <p class="text-[9px] text-gray-400 font-bold mt-0.5">Período concluido</p>
        </div>

        {{-- Rechazados --}}
        <div class="card-pc p-5 border-l-4 border-pc-red dark:bg-slate-900 dark:border-slate-800 group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                    <i class="fas fa-times-circle text-pc-red text-sm"></i>
                </div>
                <span class="text-3xl font-black text-pc-red">{{ $stats['rechazados'] }}</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Rechazados</p>
            <p class="text-[9px] text-gray-400 font-bold mt-0.5">No aprobados</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         FILTROS
    ══════════════════════════════════════════════════════ --}}
    <div class="card-pc p-6 bg-gray-50/50 dark:bg-slate-900 dark:border-slate-800">
        <form method="GET" action="{{ route('leaves.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            @php
                $selectedEmp = request('employee_id') ? $employees->firstWhere('id', request('employee_id')) : null;
            @endphp
            <div x-data="{
                open: false,
                search: '{{ $selectedEmp ? $selectedEmp->full_name : '' }}',
                selectedId: '{{ request('employee_id', '') }}',
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
                <label class="label-pc">Integrante</label>
                <div class="relative flex items-center">
                    <input type="text"
                           x-model="search"
                           @input.debounce.300ms="fetchResults()"
                           @focus="open = true"
                           @click.away="setTimeout(() => { open = false; }, 200)"
                           placeholder="TODOS"
                           class="input-pc text-[10px] font-bold py-2.5 px-3 w-full pr-8">
                    
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
                
                <input type="hidden" name="employee_id" :value="selectedId">

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
            <div>
                <label class="label-pc">Estado</label>
                <select name="status" class="input-pc text-[10px] font-bold py-2.5">
                    <option value="">TODOS</option>
                    <option value="pendiente"  {{ request('status') == 'pendiente'  ? 'selected' : '' }}>PENDIENTE</option>
                    <option value="aprobado"   {{ request('status') == 'aprobado'   ? 'selected' : '' }}>APROBADO</option>
                    <option value="en_curso"   {{ request('status') == 'en_curso'   ? 'selected' : '' }}>EN CURSO</option>
                    <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>FINALIZADO</option>
                    <option value="rechazado"  {{ request('status') == 'rechazado'  ? 'selected' : '' }}>RECHAZADO</option>
                </select>
            </div>
            <div>
                <label class="label-pc">Desde</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-pc py-2.5">
            </div>
            <div>
                <label class="label-pc">Hasta</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-pc py-2.5">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-pc-red text-white font-black text-[10px] uppercase py-3 rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-100">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
                <a href="{{ route('leaves.index') }}"
                   class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-400 p-3 rounded-xl hover:text-pc-red transition-all shadow-sm">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════
         TABLA DE REPOSOS
    ══════════════════════════════════════════════════════ --}}
    <div class="card-pc overflow-hidden shadow-xl shadow-gray-100/50 dark:bg-slate-900 dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-700">
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest">Integrante</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest">Médico / Institución</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Período</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Progreso</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @forelse($leaves as $leave)
                    @php
                        $computed = $leave->computeStatus();
                        $statusConfig = [
                            'pendiente'  => ['bg' => 'bg-yellow-100 text-yellow-700 border-yellow-200',  'dot' => 'bg-yellow-400',  'label' => 'Pendiente',  'icon' => 'fa-clock',        'ping' => false],
                            'aprobado'   => ['bg' => 'bg-blue-100 text-blue-700 border-blue-200',         'dot' => 'bg-blue-400',    'label' => 'Aprobado',   'icon' => 'fa-check',        'ping' => false],
                            'en_curso'   => ['bg' => 'bg-green-100 text-green-700 border-green-200',      'dot' => 'bg-green-500',   'label' => 'En Curso',   'icon' => 'fa-heartbeat',    'ping' => true],
                            'finalizado' => ['bg' => 'bg-gray-100 text-gray-600 border-gray-200',         'dot' => 'bg-gray-400',    'label' => 'Finalizado', 'icon' => 'fa-check-double', 'ping' => false],
                            'rechazado'  => ['bg' => 'bg-red-100 text-red-600 border-red-200',            'dot' => 'bg-pc-red',      'label' => 'Rechazado',  'icon' => 'fa-times',        'ping' => false],
                        ];
                        $sc = $statusConfig[$computed] ?? $statusConfig['pendiente'];
                        $showProgress = in_array($computed, ['aprobado', 'en_curso', 'finalizado']);
                        $progress = $leave->progress_percent;
                        $barColor = $computed === 'en_curso' ? 'bg-green-500' : ($computed === 'finalizado' ? 'bg-blue-400' : 'bg-blue-300');
                    @endphp
                    <tr class="hover:bg-red-50/20 dark:hover:bg-slate-800/30 transition-colors group">

                        {{-- Integrante --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-pc-red/10 text-pc-red flex items-center justify-center font-black text-[11px] shrink-0">
                                    {{ strtoupper(substr($leave->employee->first_name, 0, 1) . substr($leave->employee->last_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-black text-gray-800 dark:text-gray-100 uppercase truncate leading-none">
                                        {{ $leave->employee->full_name }}
                                    </p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">
                                        {{ $leave->employee->id_number }}
                                    </p>
                                    @if($leave->medical_condition)
                                        <p class="text-[9px] font-bold text-pc-red/70 mt-0.5 truncate max-w-[140px]" title="{{ $leave->medical_condition }}">
                                            <i class="fas fa-stethoscope mr-1"></i>{{ $leave->medical_condition }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Médico / Institución --}}
                        <td class="px-6 py-4">
                            <p class="text-[10px] font-black text-gray-700 dark:text-gray-300">{{ $leave->doctor_name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 mt-0.5 truncate max-w-[140px]">
                                <i class="fas fa-hospital text-[8px] mr-1"></i>{{ $leave->issuing_institution }}
                            </p>
                        </td>

                        {{-- Período --}}
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col items-center gap-1">
                                <div class="inline-flex items-center gap-1.5 bg-gray-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-slate-700">
                                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-300">{{ $leave->start_date->format('d/m/y') }}</span>
                                    <i class="fas fa-arrow-right text-[7px] text-pc-red"></i>
                                    <span class="text-[10px] font-black text-gray-700 dark:text-gray-300">{{ $leave->end_date->format('d/m/y') }}</span>
                                </div>
                                <span class="text-[9px] font-bold text-gray-400 uppercase">
                                    {{ $leave->duration_value }} {{ $leave->duration_label }}
                                </span>
                            </div>
                        </td>

                        {{-- Barra de Progreso --}}
                        <td class="px-6 py-4">
                            @if($showProgress)
                                <div class="w-36 mx-auto">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-[9px] font-bold text-gray-400">{{ $leave->days_elapsed }}d transcurridos</span>
                                        <span class="text-[9px] font-bold {{ $computed === 'en_curso' ? 'text-green-600' : 'text-gray-400' }}">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full transition-all duration-500 {{ $computed === 'en_curso' ? 'animate-pulse' : '' }}"
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        @if($computed === 'en_curso')
                                            <span class="text-[9px] font-bold text-green-600">
                                                <i class="fas fa-hourglass-half mr-0.5"></i>{{ $leave->days_remaining }}d restantes
                                            </span>
                                        @elseif($computed === 'finalizado')
                                            <span class="text-[9px] font-bold text-blue-400">
                                                <i class="fas fa-check-double mr-0.5"></i>Concluido
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold text-blue-400">
                                                Inicia {{ $leave->start_date->diffForHumans() }}
                                            </span>
                                        @endif
                                        <span class="text-[9px] text-gray-300 font-bold">{{ $leave->total_days }}d total</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">—</span>
                                </div>
                            @endif
                        </td>

                        {{-- Estado dinámico --}}
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[8px] font-black rounded-lg uppercase tracking-widest border {{ $sc['bg'] }}">
                                @if($sc['ping'])
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sc['dot'] }} opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $sc['dot'] }}"></span>
                                    </span>
                                @else
                                    <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                                @endif
                                {{ $sc['label'] }}
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                @if($leave->status == 'pendiente')
                                    <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Aprobar"
                                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-green-500 hover:bg-green-500 hover:text-white hover:border-green-500 transition-all shadow-sm">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Rechazar"
                                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-pc-red hover:bg-pc-red hover:text-white hover:border-pc-red transition-all shadow-sm">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('leaves.show', $leave) }}" title="Ver detalle"
                                   class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-pc-blue hover:bg-pc-blue hover:text-white hover:border-pc-blue transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @can('update', $leave)
                                    <a href="{{ route('leaves.edit', $leave) }}" title="Editar"
                                       class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-indigo-400 hover:bg-indigo-400 hover:text-white hover:border-indigo-400 transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                @endcan
                                @can('delete', $leave)
                                    <form action="{{ route('leaves.destroy', $leave) }}" method="POST"
                                          class="inline confirm-delete"
                                          data-label="el reposo de {{ $leave->employee->full_name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar"
                                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-gray-300 hover:bg-pc-red hover:text-white hover:border-pc-red transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-red-50 dark:bg-slate-800 flex items-center justify-center">
                                    <i class="fas fa-briefcase-medical text-gray-200 dark:text-slate-700 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Sin registros</p>
                                    <p class="text-[10px] text-gray-300 font-bold mt-1">No se encontraron reposos con los criterios seleccionados</p>
                                </div>
                                @can('create', App\Models\Leave::class)
                                    <a href="{{ route('leaves.create') }}"
                                       class="bg-pc-red text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-100">
                                        <i class="fas fa-plus-circle mr-1"></i> Cargar primer reposo
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="p-6 bg-gray-50/30 dark:bg-slate-800/20 border-t border-gray-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase">
                Mostrando {{ $leaves->firstItem() ?? 0 }}–{{ $leaves->lastItem() ?? 0 }} de {{ $leaves->total() }} registros
            </p>
            {{ $leaves->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection