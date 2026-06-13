@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 dark:text-gray-400 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Control de Asistencias</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ══ CABECERA ══════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pc-blue/10 dark:bg-pc-blue/20 flex items-center justify-center">
                    <i class="fas fa-user-check text-pc-blue"></i>
                </div>
                Auditoría de Asistencias
            </h2>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">
                Monitoreo de entradas, salidas y puntualidad operativa del personal
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            {{-- Botones de exportación o adicionales pueden ir aquí --}}
        </div>
    </div>



    {{-- ══ TABLA DE ASISTENCIAS Y FILTROS ═══════════════════════════ --}}
    <div class="card-pc p-0 border-t-4 border-pc-blue overflow-hidden shadow-xl shadow-gray-100/50 dark:bg-slate-900 dark:border-slate-800">
        {{-- Toolbar de Filtros y Distribución --}}
        <div class="bg-gray-50/50 dark:bg-slate-900 px-5 py-3 border-b border-gray-100 dark:border-slate-800">
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 items-center justify-between">
                {{-- Filtros (Autocomplete Integrado) --}}
                <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto flex-1">
                    
                    {{-- Autocomplete de Empleado --}}
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
                    }" class="relative flex-1 lg:flex-none min-w-[200px]">
                        <div class="relative flex items-center">
                            <input type="text"
                                   x-model="search"
                                   @input.debounce.300ms="fetchResults()"
                                   @focus="open = true"
                                   @click.away="setTimeout(() => { open = false; }, 200)"
                                   placeholder="Buscar integrante..."
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
                    
                    <div class="flex flex-1 lg:flex-none items-center gap-2 min-w-[200px]">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-pc text-[10px] font-bold py-2.5 px-3 w-full" title="Desde">
                        <span class="text-gray-400 text-xs">-</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-pc text-[10px] font-bold py-2.5 px-3 w-full" title="Hasta">
                    </div>
                    
                    <select name="status" class="input-pc text-[10px] font-bold py-2 px-3 flex-1 lg:flex-none w-full lg:w-auto">
                        <option value="">ESTADO</option>
                        <option value="puntual" {{ request('status') == 'puntual' ? 'selected' : '' }}>PUNTUAL</option>
                        <option value="tardanza" {{ request('status') == 'tardanza' ? 'selected' : '' }}>TARDANZA</option>
                        <option value="ausente" {{ request('status') == 'ausente' ? 'selected' : '' }}>AUSENTE</option>
                        <option value="justificado" {{ request('status') == 'justificado' ? 'selected' : '' }}>JUSTIFICADO</option>
                    </select>

                    <div class="flex items-center gap-2 w-full lg:w-auto shrink-0">
                        <button type="submit" class="flex-1 lg:flex-none bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-5 py-2 rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('attendances.index') }}" title="Restablecer" class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-400 rounded-xl hover:text-pc-orange transition-all shadow-sm">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>

                {{-- Fichas de Distribución Integradas en el Buscador --}}
                <div class="w-full lg:w-auto flex flex-col justify-center lg:pl-6 lg:border-l border-gray-200 dark:border-slate-700 pt-3 lg:pt-0 shrink-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chart-pie text-pc-orange text-sm"></i>
                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Distribución: <span class="text-pc-blue">{{ $stats['total'] }} Registros</span></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <div class="bg-green-50 dark:bg-green-500/10 border border-green-100 dark:border-green-500/20 px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm">
                            <span class="text-[12px] font-black text-green-600 leading-none">{{ $stats['puntuales'] }}</span>
                            <span class="text-[8px] font-black text-green-600/70 uppercase">Puntuales</span>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-100 dark:border-yellow-500/20 px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm">
                            <span class="text-[12px] font-black text-yellow-600 leading-none">{{ $stats['tardanzas'] }}</span>
                            <span class="text-[8px] font-black text-yellow-600/70 uppercase">Tardanzas</span>
                        </div>
                        <div class="bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm">
                            <span class="text-[12px] font-black text-pc-red leading-none">{{ $stats['ausentes'] }}</span>
                            <span class="text-[8px] font-black text-pc-red/70 uppercase">Ausentes</span>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm">
                            <span class="text-[12px] font-black text-blue-600 leading-none">{{ $stats['justificados'] ?? 0 }}</span>
                            <span class="text-[8px] font-black text-blue-600/70 uppercase">Justificad.</span>
                        </div>
                    </div>
                    
                    @php
                        $total = max(1, $stats['total']);
                        $puntualPct    = round(($stats['puntuales']    / $total) * 100);
                        $tardanzaPct   = round(($stats['tardanzas']    / $total) * 100);
                        $ausentePct    = round(($stats['ausentes']     / $total) * 100);
                        $justificadoPct = round((($stats['justificados'] ?? 0) / $total) * 100);
                    @endphp
                    
                    <div class="mt-2 flex gap-0.5 h-1.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden w-full shadow-inner">
                        @if($puntualPct > 0)<div class="bg-green-500 h-full transition-all duration-700" style="width: {{ $puntualPct }}%" title="Puntual: {{ $puntualPct }}%"></div>@endif
                        @if($tardanzaPct > 0)<div class="bg-yellow-400 h-full transition-all duration-700" style="width: {{ $tardanzaPct }}%" title="Tardanza: {{ $tardanzaPct }}%"></div>@endif
                        @if($ausentePct > 0)<div class="bg-pc-red h-full transition-all duration-700" style="width: {{ $ausentePct }}%" title="Ausente: {{ $ausentePct }}%"></div>@endif
                        @if($justificadoPct > 0)<div class="bg-blue-400 h-full transition-all duration-700" style="width: {{ $justificadoPct }}%" title="Justificado: {{ $justificadoPct }}%"></div>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-700">
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest">Integrante</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Entrada</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Salida</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Horas</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="px-6 py-4 text-[10px] font-black text-pc-blue dark:text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                    @forelse($attendances as $att)
                    @php
                        // Calcular horas trabajadas
                        $horasTrabajadas = null;
                        if ($att->check_in && $att->check_out) {
                            $entrada = \Carbon\Carbon::parse($att->check_in);
                            $salida  = \Carbon\Carbon::parse($att->check_out);
                            if ($salida->gt($entrada)) {
                                $diff = $entrada->diff($salida);
                                $horasTrabajadas = sprintf('%dh %02dm', $diff->h + ($diff->days * 24), $diff->i);
                            }
                        }

                        // Config de estado
                        $statusConfig = [
                            'puntual'     => ['bg' => 'bg-green-100 text-green-700 border-green-200',   'dot' => 'bg-green-500',  'label' => 'Puntual',     'ping' => false],
                            'tardanza'    => ['bg' => 'bg-yellow-100 text-yellow-700 border-yellow-200','dot' => 'bg-yellow-400', 'label' => 'Tardanza',    'ping' => false],
                            'ausente'     => ['bg' => 'bg-red-100 text-red-600 border-red-200',          'dot' => 'bg-pc-red',     'label' => 'Ausente',     'ping' => false],
                            'justificado' => ['bg' => 'bg-blue-100 text-blue-700 border-blue-200',       'dot' => 'bg-blue-400',   'label' => 'Justificado', 'ping' => false],
                        ];

                        // Si no tiene status en BD, derivar del check_in/check_out
                        $dbStatus = $att->status; // enum('present', 'absent', 'late', 'permission')
                        
                        // Mapear de enum a nuestros labels de UI
                        if ($dbStatus === 'present') $derivedStatus = 'puntual';
                        elseif ($dbStatus === 'absent') $derivedStatus = 'ausente';
                        elseif ($dbStatus === 'late') $derivedStatus = 'tardanza';
                        elseif ($dbStatus === 'permission') $derivedStatus = 'justificado';
                        else {
                            if (!$att->check_in) $derivedStatus = 'ausente';
                            elseif ($att->check_in && !$att->check_out) $derivedStatus = 'puntual';
                            else $derivedStatus = 'puntual';
                        }

                        $sc = $statusConfig[$derivedStatus] ?? [
                            'bg' => 'bg-gray-100 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400', 'label' => ucfirst($derivedStatus), 'ping' => false
                        ];

                        // Badge de jornada activa (entró pero no salió)
                        $enJornada = $att->check_in && !$att->check_out && $att->date->isToday();
                    @endphp
                    <tr class="hover:bg-orange-50/20 dark:hover:bg-slate-800/30 transition-colors group">

                        {{-- Integrante --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-pc-orange/10 text-pc-orange flex items-center justify-center font-black text-[11px] shrink-0
                                            {{ $enJornada ? 'ring-2 ring-green-400 ring-offset-1' : '' }}">
                                    {{ strtoupper(substr($att->employee->first_name, 0, 1) . substr($att->employee->last_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-black text-gray-800 dark:text-gray-100 uppercase truncate leading-none">
                                        {{ $att->employee->full_name }}
                                    </p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase">
                                        {{ $att->employee->id_number }}
                                    </p>
                                    @if($enJornada)
                                        <span class="inline-flex items-center gap-1 text-[8px] font-black text-green-600 mt-0.5">
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                            </span>
                                            En jornada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Fecha --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-[11px] font-black text-gray-700 dark:text-gray-300">{{ $att->date->format('d M') }}</span>
                                <span class="text-[9px] font-bold text-gray-400">{{ $att->date->format('Y') }}</span>
                                @if($att->date->isToday())
                                    <span class="text-[8px] font-black text-pc-orange uppercase">Hoy</span>
                                @endif
                            </div>
                        </td>

                        {{-- Entrada --}}
                        <td class="px-6 py-4 text-center">
                            @if($att->check_in)
                                <div class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-500/5 px-2.5 py-1.5 rounded-lg border border-green-100 dark:border-green-500/10">
                                    <i class="fas fa-sign-in-alt text-[8px] text-green-500"></i>
                                    <span class="text-[10px] font-black text-green-600">{{ \Carbon\Carbon::parse($att->check_in)->format('H:i') }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-gray-200 dark:text-slate-600">——</span>
                            @endif
                        </td>

                        {{-- Salida --}}
                        <td class="px-6 py-4 text-center">
                            @if($att->check_out)
                                <div class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-500/5 px-2.5 py-1.5 rounded-lg border border-red-100 dark:border-red-500/10">
                                    <i class="fas fa-sign-out-alt text-[8px] text-pc-red"></i>
                                    <span class="text-[10px] font-black text-pc-red">{{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }}</span>
                                </div>
                            @elseif($enJornada)
                                <span class="text-[8px] font-black text-green-500 uppercase animate-pulse">Activo</span>
                            @else
                                <span class="text-[10px] font-black text-gray-200 dark:text-slate-600">——</span>
                            @endif
                        </td>

                        {{-- Horas trabajadas --}}
                        <td class="px-6 py-4 text-center">
                            @if($horasTrabajadas)
                                <span class="text-[10px] font-black text-pc-blue dark:text-gray-300">{{ $horasTrabajadas }}</span>
                            @elseif($enJornada)
                                @php
                                    $hrsTranscurridas = \Carbon\Carbon::parse($att->check_in)->diffInMinutes(now());
                                    $hrsStr = sprintf('%dh %02dm', intdiv($hrsTranscurridas, 60), $hrsTranscurridas % 60);
                                @endphp
                                <span class="text-[10px] font-black text-green-500">{{ $hrsStr }}+</span>
                            @else
                                <span class="text-[10px] font-black text-gray-200 dark:text-slate-600">——</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[8px] font-black rounded-lg uppercase tracking-widest border {{ $sc['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                                @if($derivedStatus === 'justificado' && $att->justification_reason)
                                    <i class="fas fa-info-circle ml-1 text-blue-500 cursor-help" title="{{ $att->justification_reason }}"></i>
                                @endif
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('attendances.show', $att) }}" title="Ver detalle"
                                   class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-pc-blue hover:bg-pc-blue hover:text-white hover:border-pc-blue transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                @if($derivedStatus === 'ausente' || $derivedStatus === 'tardanza')
                                    <button type="button" @click="openJustifyModal({{ $att->id }}, '{{ addslashes($att->employee->full_name) }}', '{{ $att->date->format('d/m/Y') }}')"
                                            title="Justificar"
                                            class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-indigo-400 hover:bg-indigo-400 hover:text-white hover:border-indigo-400 transition-all shadow-sm">
                                        <i class="fas fa-file-signature text-xs"></i>
                                    </button>
                                @endif

                                @can('delete', $att)
                                    <form action="{{ route('attendances.destroy', $att) }}" method="POST"
                                          class="inline confirm-delete"
                                          data-label="la asistencia de {{ $att->employee->full_name }}">
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
                        <td colspan="7" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-orange-50 dark:bg-slate-800 flex items-center justify-center">
                                    <i class="fas fa-user-clock text-gray-200 dark:text-slate-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Sin registros</p>
                                    <p class="text-[10px] text-gray-300 font-bold mt-1">No se encontraron asistencias con los criterios seleccionados</p>
                                </div>
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
                Mostrando {{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }} de {{ $attendances->total() }} registros
            </p>
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Modal para justificar ausencia/tardanza --}}
<div x-data="{
        showModal: false,
        attendanceId: null,
        employeeName: '',
        attendanceDate: '',
        reason: '',
        openJustifyModal(id, name, date) {
            this.attendanceId = id;
            this.employeeName = name;
            this.attendanceDate = date;
            this.reason = '';
            this.showModal = true;
        }
    }" 
    @open-justify-modal.window="openJustifyModal($event.detail.id, $event.detail.name, $event.detail.date)"
    x-init="
        window.openJustifyModal = (id, name, date) => {
            $dispatch('open-justify-modal', { id, name, date });
        }
    "
>
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-pc-blue/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity>
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md border border-gray-100 dark:border-slate-800" @click.away="showModal = false">
            <h3 class="text-lg font-black text-pc-blue dark:text-white uppercase mb-6 flex items-center gap-3">
                <i class="fas fa-file-signature text-blue-500"></i> Justificar Asistencia
            </h3>
            
            <div class="bg-gray-50 dark:bg-slate-800 p-4 rounded-xl mb-6">
                <p class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase truncate" x-text="employeeName"></p>
                <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">Fecha: <span x-text="attendanceDate"></span></p>
            </div>

            <form :action="`/attendances/${attendanceId}/justify`" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-6">
                    <label class="label-pc">Motivo de la justificación <span class="text-red-500">*</span></label>
                    <textarea name="reason" x-model="reason" rows="3" required class="input-pc" placeholder="Ej: Reposo médico, contingencia, etc..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="showModal = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 uppercase px-6 py-3">Cancelar</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-black text-[10px] uppercase px-8 py-3 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                        <i class="fas fa-check"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection